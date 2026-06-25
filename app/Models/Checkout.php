<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    protected $table = 'checkout';
    
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'alamat',
        'pengiriman',
        'pembayaran',
        'total_harga',
        'ongkir',
        'total_bayar',
        'status_id',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'ongkir' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CheckoutItem::class, 'checkout_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusTransaksi::class, 'status_id');
    }

    public function reviews()
    {
        return $this->hasMany(OrderReview::class, 'checkout_id');
    }
}
