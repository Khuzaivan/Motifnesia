<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodePembayaranSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('metode_pembayaran')->count() > 0) {
            return;
        }

        DB::table('metode_pembayaran')->insert([
            [
                'nama_pembayaran' => 'Saldo Motifnesia',
                'deskripsi_pembayaran' => 'Pembayaran dengan saldo akun Motifnesia',
            ],
            [
                'nama_pembayaran' => 'Mandiri Virtual Account',
                'deskripsi_pembayaran' => 'Pembayaran melalui Mandiri VA',
            ],
            [
                'nama_pembayaran' => 'BCA Virtual Account',
                'deskripsi_pembayaran' => 'Pembayaran melalui BCA VA',
            ],
            [
                'nama_pembayaran' => 'COD',
                'deskripsi_pembayaran' => 'Bayar di Tempat (Cash On Delivery)',
            ],
            [
                'nama_pembayaran' => 'Go Pay',
                'deskripsi_pembayaran' => 'Pembayaran melalui GoPay',
            ],
        ]);
    }
}
