<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    use HasFactory;
    protected $table = 'product_order';
    protected $fillable = [
        'user_id',
        'alamat',
        'metode_pengiriman_id',
        'metode_pembayaran_id',
        'subtotal_produk',
        'total_ongkir',
        'total_bayar',
        'payment_number',
        'status',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function metodePengiriman()
    {
        return $this->belongsTo(MetodePengiriman::class, 'metode_pengiriman_id');
    }

    public function metodePembayaran()
    {
        return $this->belongsTo(MetodePembayaran::class, 'metode_pembayaran_id');
    }

    public function details()
    {
        return $this->hasMany(ProductOrderDetail::class, 'order_id');
    }
}
