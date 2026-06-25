<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gambar', 255)->nullable();
            $table->string('nama_produk', 100)->nullable();
            $table->decimal('harga', 10, 2)->nullable();
            $table->string('material', 100)->nullable();
            $table->string('proses', 100)->nullable();
            $table->string('sku', 50)->nullable();
            $table->string('tags', 255)->nullable();
            $table->integer('stok')->default(0);
            $table->string('kategori', 50)->nullable();
            $table->string('jenis_lengan', 50)->nullable();
            $table->string('terjual', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('diskon_persen')->default(0);
            $table->decimal('harga_diskon', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produk');
    }
};
