<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use Illuminate\Http\Request;
use App\Models\ShoppingCard;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShoppingCardController extends Controller
{
    /**
     * Tampilkan halaman keranjang belanja
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil semua item keranjang user dengan relasi produk
        $items = ShoppingCard::with('produk')
            ->where('user_id', Auth::id())
            ->get();

        // Hitung total
        $total = 0;
        foreach ($items as $item) {
            if ($item->produk) {
                $total += $item->produk->harga * $item->qty;
            }
        }

        return view('customer.pages.shoppingCard', compact('items', 'total'));
    }

    /**
     * Tambah produk ke keranjang (AJAX)
     */
    public function add(AddToCartRequest $request)
    {
        $validated = $request->validated();
        
        $productId = $validated['product_id'];
        $ukuran = $validated['ukuran'];
        $qty = $validated['qty'] ?? 1;
        $product = Produk::with('sizeStocks')
            ->where('is_active', true)
            ->findOrFail($productId);

        // Cek apakah produk+ukuran sudah ada di keranjang user
        $item = ShoppingCard::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->where('ukuran', $ukuran)
            ->first();

        $currentSizeQty = ShoppingCard::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->where('ukuran', $ukuran)
            ->sum('qty');
        $newQty = $qty + ($item ? $item->qty : 0);
        $availableStock = $product->stockForSize($ukuran);

        if ($currentSizeQty + $qty > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => 'Stok ukuran ' . $ukuran . ' tidak mencukupi. Stok tersedia: ' . $availableStock . '.'
            ], 422);
        }

        if ($item) {
            // Update qty jika sudah ada
            $item->qty = $newQty;
            $item->save();
        } else {
            // Buat item baru
            ShoppingCard::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'ukuran' => $ukuran,
                'qty' => $qty,
            ]);
        }

        return response()->json([
            'success' => true, 
            'message' => 'Produk berhasil ditambahkan ke keranjang!'
        ]);
    }

    /**
     * Update qty item di keranjang (AJAX)
     */
    public function update(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|exists:shopping_card,id',
            'qty' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.'
            ], 422);
        }

        $cartId = $request->input('cart_id');
        $qty = $request->input('qty');

        // Pastikan item milik user yang sedang login
        $item = ShoppingCard::with('produk')
            ->where('id', $cartId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan.'
            ], 404);
        }

        if (!$item->produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        $otherQty = ShoppingCard::where('user_id', Auth::id())
            ->where('product_id', $item->product_id)
            ->where('ukuran', $item->ukuran)
            ->where('id', '!=', $item->id)
            ->sum('qty');
        $availableStock = $item->produk->stockForSize($item->ukuran);

        if ($otherQty + $qty > $availableStock) {
            return response()->json([
                'success' => false,
                'message' => 'Stok ukuran ' . $item->ukuran . ' tidak mencukupi. Stok tersedia: ' . $availableStock . '.'
            ], 422);
        }

        $item->qty = $qty;
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Jumlah berhasil diperbarui.'
        ]);
    }

    /**
     * Hapus item dari keranjang (AJAX)
     */
    public function delete($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Pastikan item milik user yang sedang login
        $item = ShoppingCard::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan.'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil dihapus dari keranjang.'
        ]);
    }

    /**
     * Proses checkout: Simpan item yang dipilih ke session
     */
    public function checkout(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Validasi: harus ada item yang dipilih
        $validator = Validator::make($request->all(), [
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'exists:shopping_card,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Pilih minimal 1 produk untuk checkout.');
        }

        $selectedIds = $request->input('selected_items');

        // Pastikan semua item milik user yang login
        $items = ShoppingCard::with('produk')
            ->where('user_id', Auth::id())
            ->whereIn('id', $selectedIds)
            ->get();

        if ($items->isEmpty()) {
            return redirect()->back()->with('error', 'Item tidak valid.');
        }

        $requestedByVariant = [];

        foreach ($items as $item) {
            if (!$item->produk || ! $item->produk->is_active) {
                return redirect()->back()->with('error', 'Ada produk di keranjang yang sudah tidak tersedia.');
            }

            $key = $item->product_id . '|' . ($item->ukuran ?? '');
            $requestedByVariant[$key]['qty'] = ($requestedByVariant[$key]['qty'] ?? 0) + $item->qty;
            $requestedByVariant[$key]['produk'] = $item->produk;
            $requestedByVariant[$key]['ukuran'] = $item->ukuran;
        }

        foreach ($requestedByVariant as $request) {
            $availableStock = $request['produk']->stockForSize($request['ukuran']);
            if ($request['qty'] > $availableStock) {
                return redirect()->back()->with('error', 'Stok "' . $request['produk']->nama_produk . '" ukuran ' . $request['ukuran'] . ' tidak mencukupi. Stok tersedia: ' . $availableStock . '.');
            }
        }

        // Simpan ID items yang dipilih ke session
        session(['checkout_items' => $selectedIds]);

        // Redirect ke halaman checkout
        return redirect()->route('customer.checkout.index');
    }
}
