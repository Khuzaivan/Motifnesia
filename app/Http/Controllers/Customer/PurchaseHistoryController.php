<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReview;
use App\Models\OrderStatusHistory;
use App\Models\ProductReturn;
use App\Models\Produk;
use App\Models\Notification;

class PurchaseHistoryController extends Controller
{
    /**
     * Get purchase history data for authenticated user
     * Returns all checkout items for the authenticated user.
     */
    public static function getHistoryData()
    {
        $userId = Auth::id();

        if (!$userId) {
            return [];
        }

        // Get all order items from orders that belong to this user
        // Include all orders regardless of status for history, but only enable review button if status = Sampai.
        $orderItems = OrderItem::with(['produk', 'order.deliveryStatus', 'review', 'productReturns'])
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('id', 'desc')
            ->get();

        $historyData = [];

        foreach ($orderItems as $item) {
            $order = $item->order;
            $produk = $item->produk;
            $status = $order->deliveryStatus;
            $review = $item->review;

            $deliveredStatusId = (int) config('order.delivery_status.delivered', 5);
            $inTransitStatusId = (int) config('order.delivery_status.in_transit', 4);
            $isDelivered = (int) $order->delivery_status_id === $deliveredStatusId;
            $hasCustomerConfirmed = (bool) $order->customer_confirmed_at;

            // Customer confirm is the trigger for review/return windows on the customer side.
            $canReview = $isDelivered && $hasCustomerConfirmed && !$review;
            $hasReviewed = $review !== null;

            // Check if this item has a return request
            $returnRequest = $item->productReturns->sortByDesc('created_at')->first();
            $hasReturn = $returnRequest !== null;
            $canConfirmArrival = in_array((int) $order->delivery_status_id, [$inTransitStatusId, $deliveredStatusId], true)
                && ! $hasCustomerConfirmed
                && $order->payment_status !== 'rejected';

            $historyData[] = [
                'order_item_id' => $item->id,
                'order_id' => $order->id,
                'produk_id' => $item->produk_id,
                'nama' => $produk->nama_produk ?? 'Produk',
                'gambar' => $produk->gambar ?? 'placeholder.jpg',
                'gambar_url' => $produk ? $produk->image_url : \App\Support\AssetUrl::product(null),
                'ukuran' => $item->ukuran,
                'qty' => $item->qty,
                'harga' => $item->harga,
                'subtotal' => $item->subtotal,
                'status_nama' => $order->payment_status === 'expired'
                    ? 'Pembayaran Kedaluwarsa'
                    : ($order->payment_status === 'rejected'
                        ? 'Pembayaran Ditolak'
                        : ($status->nama_status ?? 'Menunggu Konfirmasi')),
                'status_id' => $order->delivery_status_id,
                'payment_status' => $order->payment_status,
                'customer_confirmed_at' => $order->customer_confirmed_at,
                'can_confirm_arrival' => $canConfirmArrival,
                'can_review' => $canReview,
                'has_reviewed' => $hasReviewed,
                'status_ulasan' => $hasReviewed ? 'lihat' : ($canReview ? 'beri' : 'disabled'),
                'has_return' => $hasReturn,
                'can_return' => $isDelivered && $hasCustomerConfirmed && ! $hasReturn && ProductReturn::canReturnOrder($order->id),
                'return_id' => $returnRequest?->id,
                'return_status' => $returnRequest?->status,
                'return_stage' => $returnRequest?->return_stage,
                'return_deadline_at' => ProductReturn::returnDeadlineForOrder($order->id),
            ];
        }

        return $historyData;
    }

    public function confirmArrived(Order $order)
    {
        if ((int) $order->user_id !== (int) Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $inTransitStatusId = (int) config('order.delivery_status.in_transit', 4);
        $deliveredStatusId = (int) config('order.delivery_status.delivered', 5);

        if (! in_array((int) $order->delivery_status_id, [$inTransitStatusId, $deliveredStatusId], true)) {
            return redirect()->back()->with('error', 'Pesanan belum berada di tahap pengiriman yang bisa dikonfirmasi sampai.');
        }

        if ($order->payment_status === 'rejected') {
            return redirect()->back()->with('error', 'Pembayaran pesanan ini ditolak, jadi status sampai tidak bisa dikonfirmasi.');
        }

        $order->forceFill([
            'delivery_status_id' => $deliveredStatusId,
            'customer_confirmed_at' => $order->customer_confirmed_at ?: now(),
        ])->save();

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'delivery_status_id' => $deliveredStatusId,
            'changed_at' => now(),
        ]);

        Notification::create([
            'user_id' => null,
            'type' => 'order',
            'title' => 'Customer Konfirmasi Pesanan Sampai #' . $order->id,
            'message' => 'Customer sudah mengonfirmasi pesanan #' . $order->id . ' telah sampai.',
            'link' => route('admin.orders.status'),
            'priority' => 'important',
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Pesanan berhasil dikonfirmasi sampai. Anda sekarang bisa memberi ulasan atau mengajukan retur sesuai tenggat.');
    }
}
