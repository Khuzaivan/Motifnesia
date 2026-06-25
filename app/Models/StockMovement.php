<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'produk_id',
        'ukuran',
        'movement_type',
        'reference_type',
        'reference_id',
        'qty_change',
        'system_stock_before',
        'system_stock_after',
        'warehouse_stock_before',
        'warehouse_stock_after',
        'user_id',
        'note',
    ];

    protected $casts = [
        'qty_change' => 'integer',
        'system_stock_before' => 'integer',
        'system_stock_after' => 'integer',
        'warehouse_stock_before' => 'integer',
        'warehouse_stock_after' => 'integer',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
