<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryStatusSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('delivery_status')->count() > 0) {
            return;
        }

        DB::table('delivery_status')->insert([
            [
                'id' => 1,
                'nama_status' => 'Pending',
                'deskripsi' => 'Menunggu konfirmasi pembayaran',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nama_status' => 'Diproses',
                'deskripsi' => 'Pesanan sedang diproses',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nama_status' => 'Dikemas',
                'deskripsi' => 'Pesanan sedang dikemas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'nama_status' => 'Dalam Perjalanan',
                'deskripsi' => 'Pesanan dalam perjalanan ke alamat tujuan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'nama_status' => 'Sampai',
                'deskripsi' => 'Pesanan telah sampai di tujuan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
