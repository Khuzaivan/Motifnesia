<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\OrderReview;
use App\Models\OrderItem;
use App\Models\Produk;

class OrderReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'deskripsi_ulasan' => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();
        $orderItemId = $request->order_item_id;
        $rating = $request->rating;
        $deskripsi = $request->deskripsi_ulasan;

        // Get order item details
        $orderItem = OrderItem::with('order', 'produk')->findOrFail($orderItemId);

        // Verify user owns this order
        if ($orderItem->order->user_id != $userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ((int) $orderItem->order->delivery_status_id !== (int) config('order.delivery_status.delivered', 5)) {
            return response()->json(['success' => false, 'message' => 'Ulasan hanya bisa dikirim setelah pesanan sampai'], 400);
        }

        // Check if already reviewed
        $existingReview = OrderReview::where('order_item_id', $orderItemId)->first();
        if ($existingReview) {
            return response()->json(['success' => false, 'message' => 'Produk ini sudah diulas'], 400);
        }

        // Create review
        $review = OrderReview::create([
            'user_id' => $userId,
            'order_item_id' => $orderItemId,
            'order_id' => $orderItem->order_id,
            'produk_id' => $orderItem->produk_id,
            'rating' => $rating,
            'deskripsi_ulasan' => $deskripsi,
            'moderation_status' => 'approved',
        ]);

        // Update product rating average
        $this->updateProductRating($orderItem->produk_id);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil dikirim!',
            'review' => $review,
        ]);
    }

    /**
     * Get review by order_item_id
     */
    public function show($orderItemId)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $review = OrderReview::with('produk')
            ->where('order_item_id', $orderItemId)
            ->where('user_id', $userId)
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false, 
                'message' => 'Ulasan tidak ditemukan',
                'debug' => [
                    'order_item_id' => $orderItemId,
                    'user_id' => $userId,
                ]
            ], 404);
        }

        return response()->json([
            'success' => true,
            'review' => [
                'rating' => $review->rating,
                'deskripsi_ulasan' => $review->deskripsi_ulasan,
                'created_at' => $review->created_at->format('d M Y'),
            ],
        ]);
    }

    /**
     * Update product rating average
     */
    private function updateProductRating($productId)
    {
        // Calculate average rating for this product
        $avgRating = OrderReview::where('produk_id', $productId)
            ->where('moderation_status', 'approved')
            ->avg('rating');

        // Update produk table
        Produk::where('id', $productId)->update([
            'rating' => round($avgRating, 2),
        ]);
    }
}
