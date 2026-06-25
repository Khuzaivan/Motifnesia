<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockProcurement extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_IN_DELIVERY = 'in_delivery';
    public const STATUS_ARRIVED = 'arrived';
    public const STATUS_STOCK_APPLIED = 'stock_applied';

    protected $fillable = [
        'procurement_number',
        'supplier_id',
        'created_by',
        'confirmed_by',
        'applied_by',
        'status',
        'note',
        'approved_at',
        'in_delivery_at',
        'arrived_at',
        'stock_applied_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'in_delivery_at' => 'datetime',
        'arrived_at' => 'datetime',
        'stock_applied_at' => 'datetime',
    ];

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_IN_DELIVERY => 'Diantar',
            self::STATUS_ARRIVED => 'Sampai',
            self::STATUS_STOCK_APPLIED => 'Stok Diterapkan',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(StockProcurementItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function applier()
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function getTotalQtyAttribute(): int
    {
        if ($this->relationLoaded('items')) {
            return (int) $this->items->sum('total_qty');
        }

        return (int) $this->items()->sum('total_qty');
    }
}
