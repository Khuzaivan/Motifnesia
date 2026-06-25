<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontenIcon extends Model
{
    use HasFactory;

    protected $table = 'konten_icon';

    protected $fillable = [
        'nama', 'gambar', 'link', 'urutan'
    ];
}
