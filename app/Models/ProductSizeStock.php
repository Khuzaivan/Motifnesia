<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSizeStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'produk_id',
        'ukuran',
        'sku',
        'stok',
        'low_stock_threshold',
    ];

    protected $casts = [
        'stok' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
