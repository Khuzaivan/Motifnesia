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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('label')->nullable(); // Rumah, Kantor, Kost, dll
            $table->string('recipient_name'); // Nama penerima
            $table->string('phone_number');
            $table->text('address_line'); // Jalan, No. Rumah, RT/RW
            $table->string('city');
            $table->string('province');
            $table->string('postal_code', 10);
            $table->text('notes')->nullable(); // Catatan tambahan (patokan, dll)
            $table->boolean('is_primary')->default(false); // Alamat utama
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
