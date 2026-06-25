<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Tampilkan halaman Daftar Produk Favorit (Wishlist).
     */
    public function index()
    {
        // Data Dummy untuk produk favorit
        $favoriteItems = [
            [
                'id' => 1,
                'nama' => 'Batik Mega Mendung',
                'harga' => 876902,
                'gambar' => 'batik_mega_mendung.jpg', // Asumsi nama file gambar
            ],
            [
                'id' => 2,
                'nama' => 'Batik Sekar Jagad',
                'harga' => 765248,
                'gambar' => 'batik_sekar_jagad.jpg',
            ],
            [
                'id' => 3,
                'nama' => 'Batik Tujuh Rupa',
                'harga' => 398380,
                'gambar' => 'batik_tujuh_rupa.jpg',
            ],
        ];

        return view('customer.pages.favorites', compact('favoriteItems'));
    }
}