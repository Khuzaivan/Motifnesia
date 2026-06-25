<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'opname_number',
        'produk_id',
        'ukuran',
        'system_stock_before',
        'warehouse_stock_before',
        'system_stock_after',
        'difference',
        'adjusted_by',
        'note',
    ];

    protected $casts = [
        'system_stock_before' => 'integer',
        'warehouse_stock_before' => 'integer',
        'system_stock_after' => 'integer',
        'difference' => 'integer',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function adjustedBy()
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }
}
