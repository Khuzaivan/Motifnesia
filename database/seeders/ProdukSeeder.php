<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Produk::count() > 0) {
            return;
        }

        $images = [
            'assets/photoProduct/67f94fe6cfe97-1766813126.jpg',
            'assets/photoProduct/67fea6b873404-1766813420.jpg',
            'assets/photoProduct/67fea580ea0bc-1766813482.jpg',
            'assets/photoProduct/67fea72592429-1766813533.png',
            'assets/photoProduct/67febcea64883-1766813720.jpg',
            'assets/photoProduct/67febd5b51ed5-1766813445.jpg',
            'assets/photoProduct/6848d2fbf2daf-1766813604.jpg',
            'assets/photoProduct/68368b288feef-1766813574.jpg',
            'assets/photoProduct/batik-7-1766813737.png',
            'assets/photoProduct/batik-8-1766813463.png',
            'assets/photoProduct/batik-11-1766813550.jpg'
        ];

        Produk::create([
            'gambar' => $images[0],
            'nama_produk' => 'Batik Kawung Modern',
            'harga' => 150000.00,
            'material' => 'Katun',
            'proses' => 'Press',
            'sku' => 'BTK-KWG-001',
            'tags' => 'batik,kawung,modern',
            'stok' => 50,
            'kategori' => 'Pria',
            'jenis_lengan' => 'Pendek',
            'terjual' => '25',
            'deskripsi' => 'Batik motif kawung dengan desain modern, cocok untuk acara formal maupun santai.',
            'diskon_persen' => 0,
            'harga_diskon' => null,
            'ukuran' => 'XL',
        ]);

        Produk::create([
            'gambar' => $images[1],
            'nama_produk' => 'Batik Parang Premium',
            'harga' => 200000.00,
            'material' => 'Sutra',
            'proses' => 'Tulis',
            'sku' => 'BTK-PRG-002',
            'tags' => 'batik,parang,premium',
            'stok' => 30,
            'kategori' => 'Pria',
            'jenis_lengan' => 'Panjang',
            'terjual' => '15',
            'deskripsi' => 'Batik motif parang dengan kualitas premium, dibuat dengan teknik tulis tangan.',
            'diskon_persen' => 10,
            'harga_diskon' => 180000.00,
            'ukuran' => 'L',
        ]);

        Produk::create([
            'gambar' => $images[2],
            'nama_produk' => 'Batik Mega Mendung',
            'harga' => 175000.00,
            'material' => 'Katun',
            'proses' => 'Press',
            'sku' => 'BTK-MGM-003',
            'tags' => 'batik,mega mendung,cirebon',
            'stok' => 40,
            'kategori' => 'Wanita',
            'jenis_lengan' => 'Pendek',
            'terjual' => '30',
            'deskripsi' => 'Batik khas Cirebon dengan motif mega mendung yang ikonik.',
            'diskon_persen' => 0,
            'harga_diskon' => null,
            'ukuran' => 'M',
        ]);

        Produk::create([
            'gambar' => $images[3],
            'nama_produk' => 'Batik Truntum Klasik',
            'harga' => 165000.00,
            'material' => 'Katun',
            'proses' => 'Press',
            'sku' => 'BTK-TRT-004',
            'tags' => 'batik,truntum,klasik',
            'stok' => 60,
            'kategori' => 'Pria',
            'jenis_lengan' => 'Panjang',
            'terjual' => '20',
            'deskripsi' => 'Batik motif truntum dengan sentuhan klasik yang elegan.',
            'diskon_persen' => 5,
            'harga_diskon' => 156750.00,
            'ukuran' => 'L',
        ]);

        Produk::create([
            'gambar' => $images[4],
            'nama_produk' => 'Batik Sidomukti Elegan',
            'harga' => 245000.00,
            'material' => 'Katun',
            'proses' => 'Press',
            'sku' => 'BTK-SDM-005',
            'tags' => 'batik,sidomukti,elegan',
            'stok' => 35,
            'kategori' => 'Pria',
            'jenis_lengan' => 'Pendek',
            'terjual' => '18',
            'deskripsi' => 'Batik sidomukti dengan detail halus dan pewarnaan elegan.',
            'diskon_persen' => 0,
            'harga_diskon' => null,
            'ukuran' => 'XL',
        ]);

        Produk::create([
            'gambar' => $images[5],
            'nama_produk' => 'Batik Pekalongan Kontemporer',
            'harga' => 320000.00,
            'material' => 'Katun',
            'proses' => 'Press',
            'sku' => 'BTK-PKL-006',
            'tags' => 'batik,pekalongan,kontemporer',
            'stok' => 25,
            'kategori' => 'Wanita',
            'jenis_lengan' => 'Panjang',
            'terjual' => '12',
            'deskripsi' => 'Batik Pekalongan dengan desain kontemporer yang eye-catching.',
            'diskon_persen' => 15,
            'harga_diskon' => 272000.00,
            'ukuran' => 'L',
        ]);

        Produk::create([
            'gambar' => $images[6],
            'nama_produk' => 'Batik Sekar Jagad',
            'harga' => 480000.00,
            'material' => 'Sutra',
            'proses' => 'Tulis',
            'sku' => 'BTK-SKJ-007',
            'tags' => 'batik,sekar jagad,premium',
            'stok' => 15,
            'kategori' => 'Pria',
            'jenis_lengan' => 'Pendek',
            'terjual' => '8',
            'deskripsi' => 'Batik sekar jagad motif rumit dengan teknik tulis tangan asli.',
            'diskon_persen' => 0,
            'harga_diskon' => null,
            'ukuran' => 'M',
        ]);

        Produk::create([
            'gambar' => $images[7],
            'nama_produk' => 'Batik Nitik Jogja',
            'harga' => 195000.00,
            'material' => 'Katun',
            'proses' => 'Press',
            'sku' => 'BTK-NTK-008',
            'tags' => 'batik,nitik,jogja',
            'stok' => 45,
            'kategori' => 'Wanita',
            'jenis_lengan' => 'Panjang',
            'terjual' => '28',
            'deskripsi' => 'Batik nitik khas Yogyakarta dengan motif geometris yang indah.',
            'diskon_persen' => 10,
            'harga_diskon' => 175500.00,
            'ukuran' => 'L',
        ]);

        Produk::create([
            'gambar' => $images[8],
            'nama_produk' => 'Batik Lereng Solo',
            'harga' => 650000.00,
            'material' => 'Sutra',
            'proses' => 'Tulis',
            'sku' => 'BTK-LRG-009',
            'tags' => 'batik,lereng,solo,premium',
            'stok' => 10,
            'kategori' => 'Pria',
            'jenis_lengan' => 'Panjang',
            'terjual' => '5',
            'deskripsi' => 'Batik lereng Solo dengan kualitas terbaik, cocok untuk acara resmi.',
            'diskon_persen' => 0,
            'harga_diskon' => null,
            'ukuran' => '',
        ]);

        Produk::create([
            'gambar' => $images[9],
            'nama_produk' => 'Batik Tiga Negeri',
            'harga' => 385000.00,
            'material' => 'Katun',
            'proses' => 'Press',
            'sku' => 'BTK-TGN-010',
            'tags' => 'batik,tiga negeri,multicolor',
            'stok' => 20,
            'kategori' => 'Anak-anak',
            'jenis_lengan' => 'Pendek',
            'terjual' => '14',
            'deskripsi' => 'Batik tiga negeri dengan perpaduan warna khas dari tiga daerah.',
            'diskon_persen' => 20,
            'harga_diskon' => 308000.00,
            'ukuran' => 'S',
        ]);

        Produk::create([
            'gambar' => $images[10],
            'nama_produk' => 'Batik Ceplok Tradisional',
            'harga' => 210000.00,
            'material' => 'Katun',
            'proses' => 'Press',
            'sku' => 'BTK-CPL-011',
            'tags' => 'batik,ceplok,tradisional',
            'stok' => 40,
            'kategori' => 'Pria',
            'jenis_lengan' => 'Pendek',
            'terjual' => '22',
            'deskripsi' => 'Batik motif ceplok dengan corak tradisional yang timeless.',
            'diskon_persen' => 0,
            'harga_diskon' => null,
            'ukuran' => 'XL',
        ]);

        Produk::create([
            'gambar' => $images[0],
            'nama_produk' => 'Batik Sogan Klasik',
            'harga' => 275000.00,
            'material' => 'Katun',
            'proses' => 'Tulis',
            'sku' => 'BTK-SGN-012',
            'tags' => 'batik,sogan,klasik',
            'stok' => 30,
            'kategori' => 'Wanita',
            'jenis_lengan' => 'Panjang',
            'terjual' => '16',
            'deskripsi' => 'Batik sogan dengan warna natural khas pewarnaan tradisional.',
            'diskon_persen' => 5,
            'harga_diskon' => 261250.00,
            'ukuran' => 'M',
        ]);

        Produk::create([
            'gambar' => $images[1],
            'nama_produk' => 'Batik Tambal Modern',
            'harga' => 425000.00,
            'material' => 'Sutra',
            'proses' => 'Press',
            'sku' => 'BTK-TMB-013',
            'tags' => 'batik,tambal,modern',
            'stok' => 18,
            'kategori' => 'Pria',
            'jenis_lengan' => 'Pendek',
            'terjual' => '9',
            'deskripsi' => 'Batik motif tambal dengan sentuhan modern dan unik.',
            'diskon_persen' => 0,
            'harga_diskon' => null,
            'ukuran' => 'L',
        ]);

        Produk::create([
            'gambar' => $images[2],
            'nama_produk' => 'Batik Lasem Heritage',
            'harga' => 550000.00,
            'material' => 'Katun',
            'proses' => 'Tulis',
            'sku' => 'BTK-LSM-014',
            'tags' => 'batik,lasem,heritage',
            'stok' => 12,
            'kategori' => 'Wanita',
            'jenis_lengan' => 'Panjang',
            'terjual' => '7',
            'deskripsi' => 'Batik Lasem dengan motif warisan budaya yang kaya makna.',
            'diskon_persen' => 10,
            'harga_diskon' => 495000.00,
            'ukuran' => '',
        ]);

        Produk::create([
            'gambar' => $images[3],
            'nama_produk' => 'Batik Gentongan Madura',
            'harga' => 340000.00,
            'material' => 'Katun',
            'proses' => 'Press',
            'sku' => 'BTK-GTG-015',
            'tags' => 'batik,gentongan,madura',
            'stok' => 22,
            'kategori' => 'Anak-anak',
            'jenis_lengan' => 'Pendek',
            'terjual' => '11',
            'deskripsi' => 'Batik gentongan khas Madura dengan motif berani dan warna cerah.',
            'diskon_persen' => 0,
            'harga_diskon' => null,
            'ukuran' => 'S',
        ]);

        Produk::create([
            'gambar' => $images[4],
            'nama_produk' => 'Batik Tuban Gedog',
            'harga' => 720000.00,
            'material' => 'Sutra',
            'proses' => 'Tulis',
            'sku' => 'BTK-TBG-016',
            'tags' => 'batik,tuban,gedog,eksklusif',
            'stok' => 8,
            'kategori' => 'Pria',
            'jenis_lengan' => 'Panjang',
            'terjual' => '4',
            'deskripsi' => 'Batik Tuban Gedog eksklusif dengan teknik pewarnaan alami.',
            'diskon_persen' => 0,
            'harga_diskon' => null,
            'ukuran' => 'M',
        ]);

        Produk::create([
            'gambar' => $images[5],
            'nama_produk' => 'Batik Dayak Etnik',
            'harga' => 290000.00,
            'material' => 'Katun',
            'proses' => 'Press',
            'sku' => 'BTK-DYK-017',
            'tags' => 'batik,dayak,etnik',
            'stok' => 28,
            'kategori' => 'Wanita',
            'jenis_lengan' => 'Pendek',
            'terjual' => '13',
            'deskripsi' => 'Batik motif Dayak dengan corak etnik yang khas dan eksotis.',
            'diskon_persen' => 15,
            'harga_diskon' => 246500.00,
            'ukuran' => 'XL',
        ]);

        Produk::create([
            'gambar' => $images[6],
            'nama_produk' => 'Batik Buketan Vintage',
            'harga' => 180000.00,
            'material' => 'Katun',
            'proses' => 'Press',
            'sku' => 'BTK-BKT-018',
            'tags' => 'batik,buketan,vintage',
            'stok' => 50,
            'kategori' => 'Pria',
            'jenis_lengan' => 'Panjang',
            'terjual' => '26',
            'deskripsi' => 'Batik buketan dengan motif bunga-bunga vintage yang cantik.',
            'diskon_persen' => 0,
            'harga_diskon' => null,
            'ukuran' => 'L',
        ]);

        Produk::create([
            'gambar' => $images[7],
            'nama_produk' => 'Batik Garuda Nusantara',
            'harga' => 890000.00,
            'material' => 'Sutra',
            'proses' => 'Tulis',
            'sku' => 'BTK-GRD-019',
            'tags' => 'batik,garuda,nusantara,premium',
            'stok' => 5,
            'kategori' => 'Pria',
            'jenis_lengan' => 'Panjang',
            'terjual' => '2',
            'deskripsi' => 'Batik premium motif garuda dengan detail rumit, karya seni tinggi.',
            'diskon_persen' => 0,
            'harga_diskon' => null,
            'ukuran' => 'XL',
        ]);
    }
}
