<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\OrderReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductReviewController extends Controller
{
    /**
     * Menampilkan halaman ulasan produk.
     * Tampilkan semua produk dengan filter rating.
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, highest, lowest
        
        // Ambil semua produk
        $query = Produk::withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->with(['reviews.user', 'reviews.order']);
        
        // Apply filter
        if ($filter === 'highest') {
            // Rating tertinggi: yang ada review dulu (desc), yang 0 di bawah
            $query->orderByRaw('CASE WHEN reviews_count > 0 THEN 0 ELSE 1 END')
                  ->orderByDesc('reviews_avg_rating');
        } elseif ($filter === 'lowest') {
            // Rating terendah: yang ada review dulu (asc), yang 0 tetep di bawah
            $query->orderByRaw('CASE WHEN reviews_count > 0 THEN 0 ELSE 1 END')
                  ->orderBy('reviews_avg_rating');
        } else {
            $query->orderBy('id', 'desc'); // Default: semua produk terbaru
        }
        
        $products = $query->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'nama_produk' => $product->nama_produk,
                'gambar' => $product->gambar,
                'gambar_url' => $product->image_url,
                'average_rating' => round($product->reviews_avg_rating ?? 0, 1),
                'total_reviews' => $product->reviews_count ?? 0,
                'has_reviews' => $product->reviews_count > 0,
                'reviews' => $product->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'customer_name' => $review->user->full_name ?? $review->user->name ?? 'Guest',
                        'rating' => $review->rating,
                        'comment' => $review->deskripsi_ulasan,
                        'moderation_status' => $review->moderation_status ?? 'approved',
                        'moderation_note' => $review->moderation_note,
                        'date' => $review->created_at->format('d M Y'),
                        'order_number' => $review->order->order_number ?? '-',
                    ];
                })
            ];
        });

        return view('admin.pages.reviewModeration', [
            'products' => $products,
            'activePage' => 'reviews',
            'currentFilter' => $filter
        ]);
    }

    /**
     * Menampilkan detail ulasan untuk produk tertentu.
     */
    public function show($id)
    {
        $product = Produk::with(['reviews.user', 'reviews.order'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->findOrFail($id);

        $productData = [
            'id' => $product->id,
            'nama_produk' => $product->nama_produk,
            'gambar' => $product->gambar,
            'gambar_url' => $product->image_url,
            'sku' => $product->sku,
            'average_rating' => round($product->reviews_avg_rating ?? 0, 1),
            'total_reviews' => $product->reviews_count ?? 0,
            'reviews' => $product->reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'customer_name' => $review->user->full_name ?? $review->user->name ?? 'Guest',
                    'customer_email' => $review->user->email ?? 'N/A',
                    'rating' => $review->rating,
                    'comment' => $review->deskripsi_ulasan,
                    'moderation_status' => $review->moderation_status ?? 'approved',
                    'moderation_note' => $review->moderation_note,
                    'date' => $review->created_at->format('d M Y'),
                    'order_number' => $review->order->order_number ?? '-',
                ];
            })
        ];

        return view('admin.pages.productReviewDetail', [
            'product' => $productData,
            'activePage' => 'reviews'
        ]);
    }

    public function moderate(Request $request, $id)
    {
        $validated = $request->validate([
            'moderation_status' => ['required', Rule::in(['approved', 'hidden', 'rejected'])],
            'moderation_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $review = OrderReview::findOrFail($id);
        $review->update([
            'moderation_status' => $validated['moderation_status'],
            'moderation_note' => $validated['moderation_note'] ?? null,
            'moderated_at' => now(),
            'moderated_by' => Auth::id(),
        ]);

        $this->updateProductRating($review->produk_id);

        return back()->with('success', 'Status ulasan berhasil diperbarui.');
    }

    private function updateProductRating(int $productId): void
    {
        $avgRating = OrderReview::where('produk_id', $productId)
            ->where('moderation_status', 'approved')
            ->avg('rating');

        Produk::whereKey($productId)->update([
            'rating' => round($avgRating ?? 0, 2),
        ]);
    }
}
