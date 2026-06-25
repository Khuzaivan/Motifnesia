<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutItem extends Model
{
    use HasFactory;

    protected $table = 'checkout_items';

    public $timestamps = false;

    protected $fillable = [
        'checkout_id',
        'product_id',
        'ukuran',
        'qty',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relasi ke Checkout
     */
    public function checkout()
    {
        return $this->belongsTo(Checkout::class, 'checkout_id');
    }

    /**
     * Relasi ke Produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'product_id');
    }

    /**
     * Relasi ke OrderReview
     */
    public function review()
    {
        return $this->hasOne(OrderReview::class, 'order_item_id');
    }
}
