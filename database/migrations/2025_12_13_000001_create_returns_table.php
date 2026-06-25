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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->unsignedInteger('produk_id');
            $table->foreign('produk_id')->references('id')->on('produk')->onDelete('cascade');
            
            // Return details
            $table->enum('reason', [
                'Ukuran tidak sesuai',
                'Barang rusak/cacat',
                'Salah kirim produk',
                'Tidak sesuai deskripsi',
                'Berubah pikiran',
                'Lainnya'
            ]);
            $table->text('description')->nullable(); // Keterangan tambahan
            $table->string('photo_proof')->nullable(); // Foto bukti (jika barang rusak)
            
            // Status & action
            $table->enum('status', ['Pending', 'Disetujui', 'Ditolak', 'Diproses', 'Selesai'])->default('Pending');
            $table->text('admin_note')->nullable(); // Catatan dari admin (alasan tolak, dll)
            $table->enum('action_type', ['Refund', 'Tukar Barang'])->default('Refund');
            
            // Refund info
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->enum('refund_status', ['Belum', 'Diproses', 'Selesai'])->default('Belum');
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('order_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
