<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar pelanggan yang pernah membeli produk.
     */
    public function index()
    {
        Gate::authorize('is-owner');
        // Ambil user yang punya minimal 1 order (pernah checkout)
        $customers = User::whereHas('orders')
            ->withCount([
                'orders as total_products' => function ($query) {
                    $query->join('order_items', 'orders.id', '=', 'order_items.order_id')
                          ->select(DB::raw('SUM(order_items.qty)'));
                }
            ])
            ->with(['orders.orderItems.produk'])
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'username' => $user->name,
                    'full_name' => $user->full_name ?? $user->name,
                    'email' => $user->email,
                    'account_status' => $user->account_status ?? 'active',
                    'total_products' => $user->total_products ?? 0,
                    'orders' => $user->orders
                ];
            });

        return view('admin.pages.customerList', [
            'customers' => $customers,
            'activePage' => 'customers'
        ]);
    }

    /**
     * Nonaktifkan/aktifkan customer tanpa menghapus histori transaksi.
     */
    public function destroy($id)
    {
        Gate::authorize('is-owner');
        $user = User::findOrFail($id);
        $nextStatus = ($user->account_status ?? 'active') === 'active' ? 'suspended' : 'active';

        $user->update([
            'account_status' => $nextStatus,
            'account_status_reason' => $nextStatus === 'suspended'
                ? 'Dinonaktifkan admin karena perlu peninjauan akun/transaksi.'
                : null,
            'account_status_changed_at' => now(),
            'account_status_changed_by' => Auth::id(),
        ]);

        AuditLogService::log(
            $nextStatus === 'suspended' ? 'suspend_user' : 'activate_user',
            $user,
            null,
            ['new_status' => $nextStatus]
        );

        return redirect()->route('admin.customers.index')
                        ->with('success', $nextStatus === 'suspended' ? 'Pelanggan berhasil dinonaktifkan.' : 'Pelanggan berhasil diaktifkan kembali.');
    }
}
