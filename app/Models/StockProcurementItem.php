<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockProcurementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_procurement_id',
        'produk_id',
        'qty_s',
        'qty_m',
        'qty_l',
        'qty_xl',
        'total_qty',
    ];

    protected $casts = [
        'qty_s' => 'integer',
        'qty_m' => 'integer',
        'qty_l' => 'integer',
        'qty_xl' => 'integer',
        'total_qty' => 'integer',
    ];

    public function procurement()
    {
        return $this->belongsTo(StockProcurement::class, 'stock_procurement_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function sizeQuantities(): array
    {
        return [
            'S' => (int) $this->qty_s,
            'M' => (int) $this->qty_m,
            'L' => (int) $this->qty_l,
            'XL' => (int) $this->qty_xl,
        ];
    }
}
