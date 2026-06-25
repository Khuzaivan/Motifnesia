<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodePengirimanSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('metode_pengiriman')->count() > 0) {
            return;
        }

        DB::table('metode_pengiriman')->insert([
            [
                'nama_pengiriman' => 'Reguler',
                'deskripsi_pengiriman' => 'pengiriman standar',
                'harga' => 20000,
            ],
            [
                'nama_pengiriman' => 'Ekspres',
                'deskripsi_pengiriman' => 'pengiriman cepat (Next Day / Same Day)',
                'harga' => 15000,
            ],
            [
                'nama_pengiriman' => 'Ekonomis',
                'deskripsi_pengiriman' => 'ongkos kirim lebih murah',
                'harga' => 10000,
            ],
        ]);
    }
}
