<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrderItem extends Model
{
    use HasFactory;
    protected $table = 'product_order_items';
    protected $fillable = [
        'product_order_id',
        'produk_id',
        'nama',
        'harga',
        'qty',
        'subtotal',
        'ukuran',
    ];
}
