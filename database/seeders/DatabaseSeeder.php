<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (\App\Models\KontenSlideShow::count() === 0) {
            \App\Models\KontenSlideShow::insert([
                [
                    'judul' => 'Promo Lebaran',
                    'caption' => 'Dapatkan diskon hingga 50%',
                    'gambar' => 'assets/konten/1765697663_slide_slideshow__1_.webp',
                    'link' => '#',
                    'aktif' => 1,
                    'urutan' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'judul' => 'Koleksi Terbaru',
                    'caption' => 'Batik Premium Berkualitas',
                    'gambar' => 'assets/konten/1765697674_slide_slideshow__2_.webp',
                    'link' => '#',
                    'aktif' => 1,
                    'urutan' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'judul' => 'Batik Etnik Nusantara',
                    'caption' => 'Karya Anak Bangsa',
                    'gambar' => 'assets/konten/1765697684_slide_slideshow__3_.webp',
                    'link' => '#',
                    'aktif' => 1,
                    'urutan' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        $this->call([
            DeliveryStatusSeeder::class,
            MetodePembayaranSeeder::class,
            MetodePengirimanSeeder::class,
            ProdukSeeder::class,
            // AdminUserSeeder::class, // Commented out to avoid duplicate entry error
        ]);
    }
}
