<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkout_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('checkout_id');
            $table->unsignedInteger('product_id');
            $table->string('ukuran', 10);
            $table->integer('qty');
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('subtotal', 12, 2);
            
            $table->foreign('checkout_id')->references('id')->on('checkout')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('produk')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkout_items');
    }
};
