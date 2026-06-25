<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontenSlideShow extends Model
{
    use HasFactory;

    protected $table = 'konten_slide_show';

    protected $fillable = [
        'judul', 'caption', 'gambar', 'link', 'aktif', 'urutan'
    ];
}
