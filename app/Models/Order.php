<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'alamat',
        'metode_pengiriman_id',
        'metode_pembayaran_id',
        'delivery_status_id',
        'total_ongkir',
        'total_bayar',
        'payment_number',
        'payment_status',
        'payment_proof',
        'payment_deadline_at',
        'paid_at',
        'payment_verified_at',
        'payment_rejected_at',
        'payment_note',
        'customer_confirmed_at',
        'sold_counted_at',
        'membership_redemption_id',
        'voucher_code',
        'voucher_discount',
        'membership_tier',
        'membership_tier_discount',
    ];

    protected $casts = [
        'total_ongkir' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'voucher_discount' => 'decimal:2',
        'membership_tier_discount' => 'decimal:2',
        'payment_deadline_at' => 'datetime',
        'paid_at' => 'datetime',
        'payment_verified_at' => 'datetime',
        'payment_rejected_at' => 'datetime',
        'customer_confirmed_at' => 'datetime',
        'sold_counted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function metodePengiriman()
    {
        return $this->belongsTo(MetodePengiriman::class);
    }

    public function metodePembayaran()
    {
        return $this->belongsTo(MetodePembayaran::class);
    }

    public function deliveryStatus()
    {
        return $this->belongsTo(DeliveryStatus::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function membershipRedemption()
    {
        return $this->belongsTo(UserRewardRedemption::class, 'membership_redemption_id');
    }
}
