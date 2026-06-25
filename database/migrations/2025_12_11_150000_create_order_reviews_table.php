<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->unsignedInteger('produk_id');
            $table->tinyInteger('rating')->comment('Rating 1-5');
            $table->text('deskripsi_ulasan')->nullable();
            $table->timestamps();

            // Foreign key untuk produk (INT)
            $table->foreign('produk_id')->references('id')->on('produk')->onDelete('cascade');

            // Index untuk performa query
            $table->index(['user_id', 'produk_id']);
            $table->index('order_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_reviews');
    }
};
