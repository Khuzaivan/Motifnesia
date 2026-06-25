<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('order_number')->unique();
            $table->text('alamat');
            $table->foreignId('metode_pengiriman_id')->constrained('metode_pengiriman');
            $table->foreignId('metode_pembayaran_id')->constrained('metode_pembayaran');
            $table->foreignId('delivery_status_id')->constrained('delivery_status');
            $table->decimal('total_ongkir', 15, 2);
            $table->decimal('total_bayar', 15, 2);
            $table->string('payment_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
