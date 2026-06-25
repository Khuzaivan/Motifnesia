<?php

namespace App\Models;

use App\Support\AssetUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'gambar',
        'nama_produk',
        'harga',
        'material',
        'proses',
        'sku',
        'tags',
        'stok',
        'ukuran',
        'kategori',
        'gender',
        'jenis_lengan',
        'terjual',
        'is_active',
        'archived_at',
        'deskripsi',
        'diskon_persen',
        'harga_diskon',
        'filosofi_motif',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'harga_diskon' => 'decimal:2',
        'stok' => 'integer',
        'terjual' => 'integer',
        'is_active' => 'boolean',
        'archived_at' => 'datetime',
        'diskon_persen' => 'integer',
    ];
    
    /**
     * Relasi ke OrderReview
     */
    public function reviews()
    {
        return $this->hasMany(OrderReview::class, 'produk_id');
    }

    public function sizeStocks()
    {
        return $this->hasMany(ProductSizeStock::class, 'produk_id');
    }

    public function warehouseStocks()
    {
        return $this->hasMany(WarehouseStock::class, 'produk_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'produk_id');
    }

    public function activeSizeStocks()
    {
        return $this->sizeStocks()->where('stok', '>', 0);
    }

    public function stockForSize(?string $size): int
    {
        if (! $size) {
            return (int) $this->stok;
        }

        if ($this->relationLoaded('sizeStocks')) {
            $stock = $this->sizeStocks->firstWhere('ukuran', $size);
            return $stock ? (int) $stock->stok : (int) $this->stok;
        }

        $stock = $this->sizeStocks()->where('ukuran', $size)->first();
        return $stock ? (int) $stock->stok : (int) $this->stok;
    }

    public function getImageUrlAttribute(): string
    {
        return AssetUrl::product($this->gambar);
    }
}
