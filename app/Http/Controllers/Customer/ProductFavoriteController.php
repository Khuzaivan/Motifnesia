<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ProductFavorite;
use App\Models\ShoppingCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductFavoriteController extends Controller
{
    /**
     * Tampilkan halaman favorite
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil semua favorite user dengan relasi produk
        $favorites = ProductFavorite::with('produk')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('customer.pages.productFavorite', compact('favorites'));
    }

    /**
     * Tambah produk ke favorite
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu.'
            ], 401);
        }

        $request->validate([
            'produk_id' => 'required|exists:produk,id'
        ]);

        // Check apakah sudah ada di favorite
        $exists = ProductFavorite::where('user_id', Auth::id())
            ->where('produk_id', $request->produk_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Produk sudah ada di favorite.'
            ], 400);
        }

        // Insert ke favorite
        ProductFavorite::create([
            'user_id' => Auth::id(),
            'produk_id' => $request->produk_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Menambahkan produk ke Favorite'
        ]);
    }

    /**
     * Hapus produk dari favorite
     */
    public function destroy($id)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $favorite = ProductFavorite::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$favorite) {
            return redirect()->back()->with('error', 'Favorite tidak ditemukan.');
        }

        $favorite->delete();

        return redirect()->back()->with('success', 'Produk berhasil dihapus dari favorite.');
    }

    /**
     * Tambah produk dari favorite ke shopping cart
     */
    public function addToCart($favoriteId)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $favorite = ProductFavorite::with('produk.sizeStocks')
            ->where('id', $favoriteId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$favorite) {
            return redirect()->back()->with('error', 'Favorite tidak ditemukan.');
        }

        if (!$favorite->produk || ! $favorite->produk->is_active || $favorite->produk->stok < 1) {
            return redirect()->back()->with('error', 'Produk sedang tidak tersedia.');
        }

        $selectedSize = optional($favorite->produk->sizeStocks->first(fn ($stock) => (int) $stock->stok > 0))->ukuran;
        $selectedSize = $selectedSize ?: 'M';
        $availableStock = $favorite->produk->stockForSize($selectedSize);

        $currentQty = ShoppingCard::where('user_id', Auth::id())
            ->where('product_id', $favorite->produk_id)
            ->where('ukuran', $selectedSize)
            ->sum('qty');

        if ($currentQty + 1 > $availableStock) {
            return redirect()->back()->with('error', 'Stok produk tidak mencukupi.');
        }

        // Gunakan ukuran pertama yang masih tersedia supaya tidak asal masuk ukuran M.
        $cartItem = ShoppingCard::where('user_id', Auth::id())
            ->where('product_id', $favorite->produk_id)
            ->where('ukuran', $selectedSize)
            ->first();

        if ($cartItem) {
            // Jika sudah ada, tambah qty
            $cartItem->qty += 1;
            $cartItem->save();
        } else {
            // Jika belum ada, insert baru
            ShoppingCard::create([
                'user_id' => Auth::id(),
                'product_id' => $favorite->produk_id,
                'qty' => 1,
                'ukuran' => $selectedSize,
            ]);
        }

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }
}
