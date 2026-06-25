<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_transaksi', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_status', 20);
            $table->string('keterangan', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_transaksi');
    }
};
