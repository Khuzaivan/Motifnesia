<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\OrderReview;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Menampilkan halaman ulasan produk.
     * Hanya produk yang punya ulasan yang ditampilkan.
     */
    public function index()
    {
        // Ambil produk yang punya minimal 1 review
        $products = Produk::whereHas('reviews')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->with(['reviews.user', 'reviews.orderItem'])
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'nama_produk' => $product->nama_produk,
                    'gambar' => $product->gambar,
                    'average_rating' => round($product->reviews_avg_rating ?? 0, 1),
                    'total_reviews' => $product->reviews_count ?? 0,
                    'reviews' => $product->reviews->map(function ($review) {
                        return [
                            'id' => $review->id,
                            'customer_name' => $review->user->full_name ?? $review->user->name ?? 'Guest',
                            'rating' => $review->rating,
                            'comment' => $review->deskripsi_ulasan,
                            'date' => $review->created_at->format('d M Y'),
                            'order_number' => $review->order->order_number ?? '-',
                        ];
                    })
                ];
            });

        return view('admin.pages.productReviews', [
            'products' => $products,
            'activePage' => 'reviews'
        ]);
    }
}