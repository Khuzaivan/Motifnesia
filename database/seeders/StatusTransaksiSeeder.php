<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('status_transaksi')->insert([
            ['id' => 1, 'nama_status' => 'Menunggu Konfirmasi', 'keterangan' => null],
            ['id' => 2, 'nama_status' => 'Diproses', 'keterangan' => null],
            ['id' => 3, 'nama_status' => 'Dikirim', 'keterangan' => null],
            ['id' => 4, 'nama_status' => 'Sampai', 'keterangan' => null],
        ]);
    }
}
