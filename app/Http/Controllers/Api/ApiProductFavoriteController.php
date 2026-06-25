<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductFavorite;
use App\Models\ShoppingCard;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiProductFavoriteController extends Controller
{
    /**
     * ========================================
     * GET ALL FAVORITES
     * ========================================
     * Endpoint: GET /api/favorites?user_id={id}
     * 
     * Response includes:
     * - List favorit produk dengan detail produk
     * - Total favorit
     */
    public function index(Request $request)
    {
        try {
            $userId = $request->user()->id;

            // Ambil semua favorites user dengan relasi produk
            $favorites = ProductFavorite::with('produk')
                ->where('user_id', $userId)
                ->latest()
                ->get();

            // Format data untuk response
            $items = [];
            foreach ($favorites as $fav) {
                if ($fav->produk && $fav->produk->is_active) {
                    $items[] = [
                        'favorite_id' => $fav->id,
                        'product_id' => $fav->produk_id,
                        'product_name' => $fav->produk->nama_produk,
                        'product_image' => $fav->produk->gambar,
                        'product_price' => $fav->produk->harga,
                        'product_price_discount' => $fav->produk->harga_diskon,
                        'discount_percent' => $fav->produk->diskon_persen,
                        'stock' => $fav->produk->stok,
                        'kategori' => $fav->produk->kategori,
                        'rating' => $fav->produk->rating,
                        'created_at' => $fav->created_at,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Favorites berhasil diambil',
                'data' => [
                    'favorites' => $items,
                    'total' => count($items)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil favorites',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * ADD TO FAVORITES
     * ========================================
     * Endpoint: POST /api/favorites
     * Body:
     *   - user_id (required)
     *   - produk_id (required)
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'produk_id' => 'required|exists:produk,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = $request->user()->id;
            $produkId = $request->produk_id;

            $produk = Produk::where('is_active', true)->find($produkId);

            if (! $produk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak tersedia'
                ], 404);
            }

            // Cek apakah sudah ada di favorites
            $exists = ProductFavorite::where('user_id', $userId)
                ->where('produk_id', $produkId)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk sudah ada di favorites'
                ], 400);
            }

            // Tambah ke favorites
            $favorite = ProductFavorite::create([
                'user_id' => $userId,
                'produk_id' => $produkId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke favorites',
                'data' => [
                    'favorite_id' => $favorite->id,
                    'product_name' => $produk->nama_produk,
                    'product_image' => $produk->gambar,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan ke favorites',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * REMOVE FROM FAVORITES
     * ========================================
     * Endpoint: DELETE /api/favorites/{id}
     */
    public function destroy(Request $request, $favoriteId)
    {
        try {
            $userId = $request->user()->id;

            // Cari favorite & pastikan milik user
            $favorite = ProductFavorite::where('id', $favoriteId)
                ->where('user_id', $userId)
                ->first();

            if (!$favorite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Favorite tidak ditemukan'
                ], 404);
            }

            $favorite->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus dari favorites'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dari favorites',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * CHECK IF PRODUCT IS FAVORITED
     * ========================================
     * Endpoint: GET /api/favorites/check?produk_id={id}
     */
    public function check(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $produkId = $request->input('produk_id');

            if (!$produkId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk ID required'
                ], 400);
            }

            $favorite = ProductFavorite::where('user_id', $userId)
                ->where('produk_id', $produkId)
                ->first();

            return response()->json([
                'success' => true,
                'message' => 'Check favorite status berhasil',
                'data' => [
                    'is_favorited' => $favorite ? true : false,
                    'favorite_id' => $favorite ? $favorite->id : null,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal check favorite status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * ADD FAVORITE TO CART
     * ========================================
     * Endpoint: POST /api/favorites/{id}/add-to-cart
     * Body:
     *   - ukuran (optional, default: M)
     *   - qty (optional, default: 1)
     */
    public function addToCart(Request $request, $favoriteId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ukuran' => 'nullable|string',
                'qty' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = $request->user()->id;
            $ukuran = $request->ukuran ?? 'M'; // Default M
            $qty = $request->qty ?? 1;

            // Cari favorite & pastikan milik user
            $favorite = ProductFavorite::with('produk.sizeStocks')
                ->where('id', $favoriteId)
                ->where('user_id', $userId)
                ->first();

            if (!$favorite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Favorite tidak ditemukan'
                ], 404);
            }

            if (! $favorite->produk || ! $favorite->produk->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk sudah tidak tersedia'
                ], 404);
            }

            $availableStock = $favorite->produk->stockForSize($ukuran);

            // Cek stok produk per ukuran
            if ($availableStock < $qty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok ukuran ' . $ukuran . ' tidak mencukupi',
                    'available_stock' => $availableStock
                ], 422);
            }

            // Cek apakah produk+ukuran sudah ada di cart
            $cartItem = ShoppingCard::where('user_id', $userId)
                ->where('product_id', $favorite->produk_id)
                ->where('ukuran', $ukuran)
                ->first();

            if ($cartItem) {
                // Update qty jika sudah ada
                $newQty = $cartItem->qty + $qty;
                
                // Cek stok lagi
                if ($availableStock < $newQty) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak mencukupi untuk qty yang diminta',
                        'available_stock' => $availableStock,
                        'current_cart_qty' => $cartItem->qty
                    ], 422);
                }

                $cartItem->qty = $newQty;
                $cartItem->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Jumlah produk di cart berhasil diupdate',
                    'data' => [
                        'cart_id' => $cartItem->id,
                        'product_name' => $favorite->produk->nama_produk,
                        'qty' => $cartItem->qty
                    ]
                ], 200);
            } else {
                // Tambah item baru ke cart
                $cart = ShoppingCard::create([
                    'user_id' => $userId,
                    'product_id' => $favorite->produk_id,
                    'ukuran' => $ukuran,
                    'qty' => $qty,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Produk dari favorite berhasil ditambahkan ke cart',
                    'data' => [
                        'cart_id' => $cart->id,
                        'product_name' => $favorite->produk->nama_produk,
                        'ukuran' => $ukuran,
                        'qty' => $qty
                    ]
                ], 201);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan ke cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================
     * GET FAVORITES COUNT
     * ========================================
     * Endpoint: GET /api/favorites/count?user_id={id}
     */
    public function count(Request $request)
    {
        try {
            $userId = $request->user()->id;

            $count = ProductFavorite::where('user_id', $userId)->count();

            return response()->json([
                'success' => true,
                'message' => 'Favorites count berhasil diambil',
                'data' => [
                    'total_favorites' => $count
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil favorites count',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
