<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductReturn extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'user_id',
        'order_id',
        'order_item_id',
        'produk_id',
        'reason',
        'description',
        'photo_proof',
        'status',
        'admin_note',
        'action_type',
        'refund_amount',
        'refund_status',
        'return_stage',
        'courier_photo',
        'courier_note',
        'courier_submitted_at',
        'return_deadline_at',
        'approved_at',
        'rejected_at',
        'completed_at',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'courier_submitted_at' => 'datetime',
        'return_deadline_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke OrderItem
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Relasi ke Produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Pending' => 'orange',
            'Disetujui' => 'green',
            'Ditolak' => 'red',
            'Diproses' => 'blue',
            'Selesai' => 'green',
            default => 'gray'
        };
    }

    /**
     * Get relative time
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Check if return is still within valid period (7 days from order received)
     */
    public static function canReturnOrder($orderId)
    {
        $order = Order::find($orderId);
        $deliveredStatusId = config('order.delivery_status.delivered', 5);
        
        if (!$order || (int) $order->delivery_status_id !== (int) $deliveredStatusId) {
            return false;
        }

        $receivedDate = $order->customer_confirmed_at ?: OrderStatusHistory::where('order_id', $orderId)
            ->where('delivery_status_id', $deliveredStatusId)
            ->latest('changed_at')
            ->value('changed_at') ?: $order->updated_at;

        return Carbon::now()->lessThanOrEqualTo(Carbon::parse($receivedDate)->addDays(7)->endOfDay());
    }

    public static function returnDeadlineForOrder($orderId): ?Carbon
    {
        $order = Order::find($orderId);
        $deliveredStatusId = config('order.delivery_status.delivered', 5);

        if (!$order || (int) $order->delivery_status_id !== (int) $deliveredStatusId) {
            return null;
        }

        $receivedDate = $order->customer_confirmed_at ?: OrderStatusHistory::where('order_id', $orderId)
            ->where('delivery_status_id', $deliveredStatusId)
            ->latest('changed_at')
            ->value('changed_at') ?: $order->updated_at;

        return Carbon::parse($receivedDate)->addDays(7)->endOfDay();
    }

    /**
     * Check if item already has return request
     */
    public static function hasReturnRequest($orderItemId)
    {
        return self::where('order_item_id', $orderItemId)->exists();
    }
}
