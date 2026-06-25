<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkout', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('alamat');
            $table->string('pengiriman', 20);
            $table->string('pembayaran', 30);
            $table->decimal('total_harga', 12, 2);
            $table->decimal('ongkir', 12, 2);
            $table->decimal('total_bayar', 12, 2);
            $table->unsignedInteger('status_id')->default(1);
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('status_id')->references('id')->on('status_transaksi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkout');
    }
};
