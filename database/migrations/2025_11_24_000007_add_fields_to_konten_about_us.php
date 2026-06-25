<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('konten_about_us', function (Blueprint $table) {
            $table->string('background_gambar')->nullable();
            $table->string('tentang_gambar')->nullable();
            $table->text('tentang_isi')->nullable();
            $table->string('visi_gambar')->nullable();
            $table->text('visi_isi')->nullable();
            $table->string('nilai_gambar')->nullable();
            $table->text('nilai_isi')->nullable();
        });
    }

    public function down()
    {
        Schema::table('konten_about_us', function (Blueprint $table) {
            $table->dropColumn([
                'background_gambar', 'tentang_gambar', 'tentang_isi',
                'visi_gambar', 'visi_isi', 'nilai_gambar', 'nilai_isi'
            ]);
        });
    }
};
