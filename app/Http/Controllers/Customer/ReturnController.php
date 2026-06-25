<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReturn;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReturnController extends Controller
{
    /**
     * Show return form for specific order item
     */
    public function create($orderItemId)
    {
        $orderItem = OrderItem::with(['order', 'produk'])->findOrFail($orderItemId);
        
        // Validation checks
        if ($orderItem->order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $deliveredStatusId = config('order.delivery_status.delivered', 5);

        if ((int) $orderItem->order->delivery_status_id !== (int) $deliveredStatusId) {
            return redirect()->route('customer.profile.index')->with('error', 'Hanya pesanan yang sudah sampai yang bisa diretur.');
        }

        if (!ProductReturn::canReturnOrder($orderItem->order_id)) {
            return redirect()->route('customer.profile.index')->with('error', 'Periode retur sudah lewat (maksimal 7 hari setelah barang diterima).');
        }

        if (ProductReturn::hasReturnRequest($orderItemId)) {
            return redirect()->route('customer.profile.index')->with('error', 'Anda sudah mengajukan retur untuk produk ini.');
        }

        return view('customer.pages.returProduct', [
            'orderItem' => $orderItem,
            'order' => $orderItem->order,
        ]);
    }

    /**
     * Store return request
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'reason' => 'required|in:Ukuran tidak sesuai,Barang rusak/cacat,Salah kirim produk,Tidak sesuai deskripsi,Berubah pikiran,Lainnya',
            'description' => 'nullable|string|max:1000',
            'photo_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'action_type' => 'required|in:Refund,Tukar Barang',
        ]);

        $orderItem = OrderItem::with(['order', 'produk'])->findOrFail($request->order_item_id);

        // Validation
        if ($orderItem->order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $deliveredStatusId = config('order.delivery_status.delivered', 5);
        if ((int) $orderItem->order->delivery_status_id !== (int) $deliveredStatusId || !ProductReturn::canReturnOrder($orderItem->order_id)) {
            return redirect()->back()->with('error', 'Produk ini tidak memenuhi syarat retur.');
        }

        if (ProductReturn::hasReturnRequest($request->order_item_id)) {
            return redirect()->back()->with('error', 'Anda sudah mengajukan retur untuk produk ini.');
        }

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo_proof')) {
            $photoPath = $request->file('photo_proof')->store('returns', 'public');
        }

        // Calculate refund amount (item price * qty)
        $refundAmount = $orderItem->harga * $orderItem->qty;

        // Create return request
        ProductReturn::create([
            'user_id' => Auth::id(),
            'order_id' => $orderItem->order_id,
            'order_item_id' => $orderItem->id,
            'produk_id' => $orderItem->produk_id,
            'reason' => $request->reason,
            'description' => $request->description,
            'photo_proof' => $photoPath,
            'action_type' => $request->action_type,
            'refund_amount' => $refundAmount,
            'status' => 'Pending',
            'return_stage' => 'request_submitted',
            'return_deadline_at' => ProductReturn::returnDeadlineForOrder($orderItem->order_id),
        ]);

        return redirect()->route('customer.returns.index')->with('success', 'Pengajuan retur berhasil dikirim. Admin akan segera memprosesnya.');
    }

    /**
     * Show customer's return requests
     */
    public function index()
    {
        $returns = ProductReturn::with(['order', 'orderItem', 'produk'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.pages.myReturns', [
            'returns' => $returns,
        ]);
    }

    /**
     * Cancel return request (only if still pending)
     */
    public function cancel($id)
    {
        $return = ProductReturn::findOrFail($id);

        if ($return->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($return->status !== 'Pending') {
            return redirect()->back()->with('error', 'Hanya retur dengan status Pending yang bisa dibatalkan.');
        }

        $return->update([
            'status' => 'Ditolak',
            'admin_note' => 'Dibatalkan oleh customer',
            'return_stage' => 'cancelled_by_customer',
            'rejected_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Pengajuan retur berhasil dibatalkan.');
    }

    public function submitCourierProof(Request $request, $id)
    {
        $request->validate([
            'courier_photo' => 'required|image|mimes:jpeg,png,jpg|max:4096',
            'courier_note' => 'nullable|string|max:500',
        ]);

        $return = ProductReturn::with(['order', 'produk'])->findOrFail($id);

        if ($return->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($return->status !== 'Disetujui') {
            return redirect()->back()->with('error', 'Bukti kurir hanya bisa dikirim setelah retur disetujui admin.');
        }

        $photoPath = $request->file('courier_photo')->store('returns/courier', 'public');

        $return->update([
            'courier_photo' => $photoPath,
            'courier_note' => $request->courier_note,
            'courier_submitted_at' => now(),
            'return_stage' => 'courier_proof_submitted',
        ]);

        return redirect()->back()->with('success', 'Bukti pengiriman ke kurir berhasil dikirim. Admin akan memproses tahap berikutnya.');
    }
}
