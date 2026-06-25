<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\DeliveryStatus;
use App\Models\Notification;
use App\Services\AuditLogService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderStatusController extends Controller
{
    /**
     * Menampilkan halaman status pengiriman.
     */
    public function index()
    {
        // Ambil semua orders dengan relasi
        $orders = Order::with(['user', 'orderItems.produk', 'metodePengiriman', 'metodePembayaran', 'deliveryStatus'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil semua delivery status untuk dropdown
        $deliveryStatuses = DeliveryStatus::all();

        return view('admin.pages.orderStatus', [
            'orders' => $orders,
            'deliveryStatuses' => $deliveryStatuses,
            'paymentStatuses' => [
                'waiting_verification' => 'Menunggu Verifikasi',
                'verified' => 'Terverifikasi',
                'rejected' => 'Ditolak',
            ],
            'activePage' => 'order-status'
        ]);
    }

    /**
     * Update status pengiriman 
     */
    public function updateStatus(Request $request, $id)
    {
        Gate::authorize('is-kasir');
        $request->validate([
            'delivery_status_id' => 'required|exists:delivery_status,id'
        ]);

        $order = Order::findOrFail($id);

        if ($order->payment_status !== 'verified' && (int) $request->delivery_status_id !== (int) config('order.delivery_status.pending', 1)) {
            return response()->json([
                'success' => false,
                'message' => 'Verifikasi pembayaran dulu sebelum status pengiriman dilanjutkan.'
            ], 422);
        }

        $oldStatusId = $order->delivery_status_id;
        $order->delivery_status_id = $request->delivery_status_id;
        $order->save();

        // Track status change history untuk notifikasi customer
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'delivery_status_id' => $request->delivery_status_id,
            'changed_at' => now(),
        ]);

        AuditLogService::log('update_delivery_status', $order, null, [
            'old_delivery_status_id' => $oldStatusId,
            'new_delivery_status_id' => (int) $request->delivery_status_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui'
        ]);
    }

    public function updatePaymentStatus(Request $request, $id, OrderService $orderService)
    {
        Gate::authorize('is-finance');
        $request->validate([
            'payment_status' => 'required|in:waiting_verification,verified,rejected',
            'payment_note' => 'nullable|string|max:500',
        ]);

        $order = Order::with('user')->findOrFail($id);
        $status = $request->payment_status;

        $order->payment_status = $status;
        $order->payment_note = $request->payment_note;

        if ($status === 'verified') {
            $order->paid_at = $order->paid_at ?: now();
            $order->payment_verified_at = now();
            $order->payment_rejected_at = null;
            $order->save();

            $orderService->grantMembershipRewardPoints($order->fresh('user'));

            if ($order->user) {
                $order->user->updateSpendingAndTier();
            }
        } elseif ($status === 'rejected') {
            $order->payment_rejected_at = now();
            $order->payment_verified_at = null;
            $order->save();
        } else {
            $order->payment_verified_at = null;
            $order->payment_rejected_at = null;
            $order->save();
        }

        Notification::create([
            'user_id' => $order->user_id,
            'type' => 'order',
            'title' => 'Status Pembayaran Pesanan #' . $order->id,
            'message' => 'Status pembayaran pesanan Anda: ' . ($status === 'verified' ? 'Terverifikasi' : ($status === 'rejected' ? 'Ditolak' : 'Menunggu Verifikasi')) . '.',
            'link' => route('customer.profile.index'),
            'priority' => $status === 'rejected' ? 'urgent' : 'important',
            'is_read' => false,
        ]);

        AuditLogService::log('update_payment_status', $order, null, [
            'new_payment_status' => $status,
            'payment_note' => $request->payment_note,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui.'
        ]);
    }
}  
