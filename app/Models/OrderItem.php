<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'produk_id',
        'nama_produk',
        'ukuran',
        'qty',
        'harga',
        'subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function review()
    {
        return $this->hasOne(OrderReview::class, 'order_item_id');
    }

    public function productReturns()
    {
        return $this->hasMany(ProductReturn::class, 'order_item_id');
    }
}
