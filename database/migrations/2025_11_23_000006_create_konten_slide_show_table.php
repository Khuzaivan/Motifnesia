<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('konten_slide_show', function (Blueprint $table) {
            $table->id();
            $table->string('judul')->nullable();
            $table->string('caption')->nullable();
            $table->string('gambar')->nullable();
            $table->string('link')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('konten_slide_show');
    }
};
