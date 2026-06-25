<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontenAboutUs extends Model
{
    use HasFactory;

    protected $table = 'konten_about_us';

    protected $fillable = [
        'judul', 'isi', 'gambar',
        'background_gambar',
        'tentang_gambar', 'tentang_isi',
        'visi_gambar', 'visi_isi',
        'nilai_gambar', 'nilai_isi'
    ];
}
