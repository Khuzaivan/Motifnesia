<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Tampilkan halaman payment/transaksi
     * Menampilkan ringkasan dari session checkout_data
     */ 
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil data checkout dari session
        $checkoutData = session('checkout_data');
        
        if (!$checkoutData) {
            return redirect()->route('customer.cart.index')->with('error', 'Data checkout tidak ditemukan. Silakan checkout ulang.');
        }

        // Deadline dibuat saat checkout, supaya tidak berubah tiap halaman payment di-refresh.
        $paymentDeadline = ! empty($checkoutData['payment_deadline_at'])
            ? \Carbon\Carbon::parse($checkoutData['payment_deadline_at'])
            : now()->addHours(config('order.payment_deadline_hours', 24));

        return view('customer.pages.payment', compact('checkoutData', 'paymentDeadline'));
    }

    /**
     * Proses pembayaran dan simpan transaksi ke database
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Validasi nomor pembayaran dan bukti transfer
        $validator = Validator::make($request->all(), [
            'payment_number' => 'required|string|min:10',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal: ' . $validator->errors()->first());
        }

        // Ambil data checkout dari session
        $checkoutData = session('checkout_data');
        
        if (!$checkoutData) {
            return redirect()->route('customer.cart.index')->with('error', 'Data checkout tidak ditemukan.');
        }

        try {
            // Handle file upload
            $gambarPath = null;
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $name = time() . '_proof_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $file->getClientOriginalName());
                $file->move(public_path('assets/paymentProof'), $name);
                $gambarPath = 'assets/paymentProof/' . $name;
            }

            // Gunakan OrderService untuk create order
            $orderService = app(OrderService::class);
            $order = $orderService->createOrder($checkoutData, $request->payment_number, $gambarPath);

            // Hapus session checkout
            session()->forget(['checkout_items', 'checkout_data']);
 
            // Redirect ke success page dengan order id
            return redirect()->route('customer.transaction.success', $order->id)
                ->with('success', 'Transaksi berhasil! Menunggu konfirmasi pembayaran.');

        } catch (\Exception $e) {
            Log::error('Payment Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            
            // Redirect back dengan error message
            return redirect()->back()
                ->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Halaman sukses setelah transaksi
     */
    public function success($orderId)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        // Ambil detail order dengan items
        $order = Order::with(['orderItems', 'metodePengiriman', 'metodePembayaran', 'deliveryStatus'])
            ->where('id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return redirect()->route('customer.home')->with('error', 'Transaksi tidak ditemukan.');
        }

        return view('customer.pages.transactionSuccess', compact('order'));
    }
}
