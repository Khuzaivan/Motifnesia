<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Tampilkan halaman konfirmasi checkout (sebelum pembayaran final).
     */
    public function index()
    {
        // ... (Kode yang sudah ada untuk halaman checkout sebelumnya) ...
        // Misalnya: return view('checkout', compact(...));
        
        // Data Dummy untuk halaman checkout
        $transactionData = [
            'item' => ['nama' => 'Batik Mega Mendung', 'ukuran' => 'M', 'jumlah' => 1, 'harga' => 701522, 'gambar' => 'placeholder_batik.jpg'],
            'alamat' => 'Jl. Kebon Jeruk No. 12, Jakarta',
            'subtotal' => 701522, 'ongkir_reguler' => 15000, 'ongkir_ekspres' => 20000, 'ongkir_ekonomis' => 0,
        ];

        $totalHarga = $transactionData['subtotal'];
        $ongkosKirim = 0; 
        $totalBayar = $totalHarga + $ongkosKirim;

        return view('customer.pages.checkOut', compact('transactionData', 'totalHarga', 'ongkosKirim', 'totalBayar'));
    }


    /**
     * Tampilkan halaman konfirmasi pembayaran (setelah memilih metode bayar, seperti GoPay).
     */
    public function showPaymentConfirmation()
    {
        // Data Dummy untuk halaman konfirmasi
        $paymentInfo = [
            'method' => 'GoPay',
            'expiry_date' => '19 Oct 2025',
            'expiry_time' => '23:59:55',
            'payment_number' => '085777xxxxxx',
            'total_tagihan' => 716522, // Contoh: 701.522 + 15.000 (ongkir reguler)
            'transaction_date' => date('d M Y, H:i'),
        ];

        return view('customer.pages.paymentConfirmation', compact('paymentInfo'));
    }
}