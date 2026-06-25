<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\ShoppingCard;
use App\Models\MetodePengiriman;
use App\Models\MetodePembayaran;
use App\Models\User;
use App\Models\UserRewardRedemption;
use App\Support\AssetUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOutController extends Controller
{
    /** 
     * Tampilkan halaman checkout
     * Mengambil data dari session checkout_items dan DB
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil ID items yang dipilih dari session (disimpan saat checkout dari keranjang)
        $selectedIds = session('checkout_items');
        
        if (!$selectedIds || !is_array($selectedIds) || count($selectedIds) === 0) {
            return redirect()->route('customer.cart.index')->with('error', 'Tidak ada item yang dipilih untuk checkout.');
        }

        // Ambil data lengkap dari DB
        $cartItems = ShoppingCard::with('produk')
            ->where('user_id', Auth::id())
            ->whereIn('id', $selectedIds)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('customer.cart.index')->with('error', 'Item tidak valid.');
        }

        if ($stockMessage = $this->validateCartItemsStock($cartItems)) {
            return redirect()->route('customer.cart.index')->with('error', $stockMessage);
        }

        // Ambil data user untuk alamat default
        $user = User::with('primaryAddress')->find(Auth::id());
        $defaultAddress = $this->formatAddress($user);

        if (!$defaultAddress) {
            return redirect()->route('customer.profile.edit')->with('error', 'Tambahkan alamat utama terlebih dahulu sebelum checkout.');
        }

        // Ambil metode pengiriman dan pembayaran dari DB
        $metodePengiriman = MetodePengiriman::all();
        $metodePembayaran = MetodePembayaran::all();
        $memberVouchers = $this->availableMemberVouchers(Auth::id());

        // Hitung subtotal produk
        $subtotalProduk = 0;
        $products = [];
        
        foreach ($cartItems as $item) {
            $harga = $item->produk->harga_diskon ?? $item->produk->harga ?? 0;
            $diskonPersen = $item->produk->diskon_persen ?? 0;
            $qty = $item->qty;
            $subtotal = $harga * $qty;
            $subtotalProduk += $subtotal;

            $products[] = [
                'cart_id' => $item->id,
                'produk_id' => $item->product_id,
                'nama' => $item->produk->nama_produk ?? 'Produk',
                'gambar' => $item->produk->gambar ?? 'placeholder.jpg',
                'gambar_url' => $item->produk ? $item->produk->image_url : AssetUrl::product(null),
                'ukuran' => $item->ukuran,
                'qty' => $qty,
                'harga' => $harga,
                'harga_diskon' => $harga,
                'diskon_persen' => $diskonPersen,
            ];
        }
        $tierDiscountPercent = $user->isMemberActive() ? $user->getTierDiscount() : 0.00;
        $tierDiscount = floor($subtotalProduk * $tierDiscountPercent);

        return view('customer.pages.checkOut', compact(
            'products',
            'subtotalProduk',
            'defaultAddress',
            'metodePengiriman',
            'metodePembayaran',
            'memberVouchers',
            'tierDiscount',
            'tierDiscountPercent',
            'user'
        ));
    }

    /**
     * Proses checkout: Simpan data checkout ke session dan redirect ke payment
     */
    public function store(CheckoutRequest $request)
    {
        $validated = $request->validated();

        // Ambil checkout_items dari session
        $selectedIds = session('checkout_items');
        
        if (!$selectedIds || !is_array($selectedIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Session checkout tidak valid.'
            ], 400);
        }

        // Ambil data cart items dari DB
        $cartItems = ShoppingCard::with('produk')
            ->where('user_id', Auth::id())
            ->whereIn('id', $selectedIds)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan.'
            ], 404);
        }

        if ($stockMessage = $this->validateCartItemsStock($cartItems)) {
            return response()->json([
                'success' => false,
                'message' => $stockMessage
            ], 422);
        }

        // Ambil data metode pengiriman dan pembayaran
        $metodePengiriman = MetodePengiriman::find($request->metode_pengiriman_id);
        $metodePembayaran = MetodePembayaran::find($request->metode_pembayaran_id);

        // Hitung total
        $subtotalProduk = 0;
        $products = [];

        foreach ($cartItems as $item) {
            $harga = $item->produk->harga_diskon ?? $item->produk->harga ?? 0;
            $diskonPersen = $item->produk->diskon_persen ?? 0;
            $qty = $item->qty;
            $subtotal = $harga * $qty;
            $subtotalProduk += $subtotal;

            $products[] = [
                'cart_id' => $item->id,
                'produk_id' => $item->product_id,
                'nama' => $item->produk->nama_produk ?? 'Produk',
                'gambar' => $item->produk->gambar ?? 'placeholder.jpg',
                'gambar_url' => $item->produk ? $item->produk->image_url : AssetUrl::product(null),
                'harga_diskon' => $harga,
                'diskon_persen' => $diskonPersen,
                'ukuran' => $item->ukuran,
                'qty' => $qty,
                'harga' => $harga,
                'subtotal' => $subtotal,
            ];
        }

        $user = Auth::user();
        $tierDiscountPercent = $user->isMemberActive() ? $user->getTierDiscount() : 0.00;
        $tierDiscount = floor($subtotalProduk * $tierDiscountPercent);

        $totalOngkir = (float) $metodePengiriman->harga;
        $requestedVoucherCode = trim((string) ($validated['voucher_code'] ?? ''));
        $voucher = $this->resolveMemberVoucher(Auth::id(), $requestedVoucherCode);

        if ($requestedVoucherCode !== '' && ! $voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher membership tidak valid atau sudah digunakan.'
            ], 422);
        }

        $voucherDiscount = $voucher ? $this->calculateVoucherDiscount($voucher, $subtotalProduk - $tierDiscount, $totalOngkir) : 0;
        $totalBayar = max(0, $subtotalProduk + $totalOngkir - $tierDiscount - $voucherDiscount);

        // Simpan data checkout ke session
        $checkoutData = [
            'alamat' => $request->alamat,
            'metode_pengiriman' => [
                'id' => $metodePengiriman->id,
                'nama' => $metodePengiriman->nama_pengiriman,
                'deskripsi' => $metodePengiriman->deskripsi_pengiriman,
                'harga' => $metodePengiriman->harga,
            ],
            'metode_pembayaran' => [
                'id' => $metodePembayaran->id,
                'nama' => $metodePembayaran->nama_pembayaran,
                'deskripsi' => $metodePembayaran->deskripsi_pembayaran,
            ],
            'products' => $products,
            'subtotal_produk' => $subtotalProduk,
            'total_ongkir' => $totalOngkir,
            'voucher' => $voucher ? [
                'redemption_id' => $voucher->id,
                'reward_id' => $voucher->reward_id,
                'code' => $voucher->voucher_code,
                'title' => $voucher->reward->title ?? 'Voucher Member',
                'discount_type' => $voucher->reward->discount_type ?? null,
                'discount_value' => $voucher->reward->discount_value ?? 0,
                'discount' => $voucherDiscount,
            ] : null,
            'membership_tier' => $user->isMemberActive() ? $user->membership_tier : null,
            'membership_tier_discount' => $tierDiscount,
            'voucher_discount' => $voucherDiscount,
            'total_bayar' => $totalBayar,
            'created_at' => now(),
            'payment_deadline_at' => now()->addHours(config('order.payment_deadline_hours', 24)),
        ];

        // Simpan ke session untuk digunakan di payment page
        session(['checkout_data' => $checkoutData]);

        return response()->json([
            'success' => true,
            'message' => 'Checkout berhasil!',
            'redirect_url' => route('customer.payment.index')
        ]);
    }

    /**
     * Format alamat user
     */
    private function formatAddress($user): ?string
    {
        if (!$user) {
            return null;
        }

        $primaryAddress = $user->primaryAddress ?: $user->addresses()
            ->orderBy('is_primary', 'desc')
            ->orderBy('id')
            ->first();

        if ($primaryAddress) {
            return $this->formatUserAddress($primaryAddress);
        }

        $parts = array_filter([
            $user->address_line,
            $user->city,
            $user->province,
            $user->postal_code,
        ]);

        return !empty($parts) ? implode(', ', $parts) : null;
    }

    private function formatUserAddress($address): string
    {
        return implode(', ', array_filter([
            $address->recipient_name,
            $address->phone_number,
            $address->address_line,
            trim($address->city . ', ' . $address->province . ' ' . $address->postal_code),
            $address->notes,
        ]));
    }

    private function validateCartItemsStock($cartItems): ?string
    {
        $requestedByVariant = [];

        foreach ($cartItems as $item) {
            if (!$item->produk || ! $item->produk->is_active) {
                return 'Ada produk di keranjang yang sudah tidak tersedia.';
            }

            $key = $item->product_id . '|' . ($item->ukuran ?? '');
            $requestedByVariant[$key]['qty'] = ($requestedByVariant[$key]['qty'] ?? 0) + $item->qty;
            $requestedByVariant[$key]['produk'] = $item->produk;
            $requestedByVariant[$key]['ukuran'] = $item->ukuran;
        }

        foreach ($requestedByVariant as $request) {
            $availableStock = $request['produk']->stockForSize($request['ukuran']);
            if ($request['qty'] > $availableStock) {
                return 'Stok "' . $request['produk']->nama_produk . '" ukuran ' . $request['ukuran'] . ' tidak mencukupi. Stok tersedia: ' . $availableStock . '.';
            }
        }

        return null;
    }

    private function availableMemberVouchers(int $userId)
    {
        return UserRewardRedemption::with('reward')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->whereNull('used_at')
            ->latest()
            ->get()
            ->filter(fn ($voucher) => $voucher->reward !== null)
            ->values();
    }

    private function resolveMemberVoucher(int $userId, ?string $voucherCode): ?UserRewardRedemption
    {
        $voucherCode = trim((string) $voucherCode);

        if ($voucherCode === '') {
            return null;
        }

        return UserRewardRedemption::with('reward')
            ->where('user_id', $userId)
            ->where('voucher_code', $voucherCode)
            ->where('status', 'active')
            ->whereNull('used_at')
            ->first();
    }

    private function calculateVoucherDiscount(UserRewardRedemption $voucher, float $subtotalProduk, float $totalOngkir): float
    {
        $reward = $voucher->reward;

        if (! $reward) {
            return 0;
        }

        $discount = match ($reward->discount_type) {
            'fixed' => (float) $reward->discount_value,
            'percent' => $reward->max_discount_value !== null && $reward->max_discount_value > 0
                ? min(floor($subtotalProduk * ((int) $reward->discount_value / 100)), (float) $reward->max_discount_value)
                : floor($subtotalProduk * ((int) $reward->discount_value / 100)),
            'free_shipping' => $totalOngkir,
            default => 0,
        };

        return min(max(0, $discount), $subtotalProduk + $totalOngkir);
    }
}
