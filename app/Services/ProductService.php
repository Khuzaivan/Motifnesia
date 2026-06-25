<?php

namespace App\Services;

use App\Models\Produk;
use App\Models\ProductSizeStock;
use App\Support\AssetUrl;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class ProductService
{
    /**
     * Upload product image
     * 
     * @param UploadedFile $file
     * @return string Path relatif gambar
     */
    public function uploadProductImage(UploadedFile $file): string
    {
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) 
                    . '-' . time() 
                    . '.' . $file->getClientOriginalExtension();
        
        $destinationPath = public_path('assets/photoProduct');
        
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        
        $file->move($destinationPath, $filename);
        
        return 'assets/photoProduct/' . $filename;
    }
    
    /**
     * Delete product image
     * 
     * @param string $imagePath
     * @return bool
     */
    public function deleteProductImage(string $imagePath): bool
    {
        $fullPath = public_path($imagePath);
        
        if (file_exists($fullPath)) {
            return @unlink($fullPath);
        }
        
        return false;
    }
    
    /**
     * Calculate discounted price
     * 
     * @param float $originalPrice
     * @param int $discountPercent
     * @return float
     */
    public function calculateDiscountedPrice(float $originalPrice, int $discountPercent): float
    {
        if ($discountPercent < 0 || $discountPercent > 100) {
            return $originalPrice;
        }
        
        return $originalPrice - ($originalPrice * ($discountPercent / 100));
    }
    
    /**
     * Prepare product data untuk create/update
     * 
     * @param array $data
     * @param string|null $imagePath
     * @return array
     */
    public function prepareProductData(array $data, ?string $imagePath = null): array
    {
        $hargaAsli = $data['price'];
        $diskonPersen = $data['diskon_persen'] ?? 0;
        $hargaDiskon = $this->calculateDiscountedPrice($hargaAsli, $diskonPersen);
        
        $productData = [
            'nama_produk'   => $data['name'],
            'deskripsi'     => $data['description'],
            'harga'         => $hargaAsli,
            'kategori'      => $data['category'],
            'gender'        => $this->normalizeGenderCategory($data['category'] ?? null),
            'stok'          => $data['stock'],
            'material'      => $data['material'] ?? null,
            'proses'        => $data['process'] ?? null,
            'sku'           => $data['sku'] ?? null,
            'tags'          => $data['tags'] ?? null,
            'ukuran'        => $data['ukuran'] ?? null,
            'jenis_lengan'  => $data['jenis_lengan'] ?? null,
            'diskon_persen' => $diskonPersen,
            'harga_diskon'  => $hargaDiskon,
            'filosofi_motif' => $data['filosofi_motif'] ?? null,
        ];
        
        if ($imagePath) {
            $productData['gambar'] = $imagePath;
        }
        
        return $productData;
    }

    public function normalizeGenderCategory(?string $category): ?string
    {
        $category = trim((string) $category);

        foreach (['Pria', 'Wanita', 'Anak-anak'] as $gender) {
            if (strcasecmp($category, $gender) === 0) {
                return $gender;
            }
        }

        return null;
    }

    public function normalizeSizeStocks(array $data): array
    {
        $rawStocks = $data['size_stocks'] ?? [];
        $sizes = ['S', 'M', 'L', 'XL'];
        $stocks = [];

        foreach ($sizes as $size) {
            $qty = isset($rawStocks[$size]) ? (int) $rawStocks[$size] : null;

            if ($qty !== null && $qty > 0) {
                $stocks[$size] = $qty;
            }
        }

        if ($stocks) {
            return $stocks;
        }

        $legacySize = $data['ukuran'] ?? null;
        $legacyStock = (int) ($data['stock'] ?? 0);

        if ($legacySize && $legacyStock > 0) {
            return [$legacySize => $legacyStock];
        }

        return [];
    }

    public function syncSizeStocks(Produk $produk, array $stocks): void
    {
        $keepSizes = [];

        foreach ($stocks as $size => $qty) {
            $qty = max(0, (int) $qty);
            $keepSizes[] = $size;

            ProductSizeStock::updateOrCreate(
                ['produk_id' => $produk->id, 'ukuran' => $size],
                [
                    'sku' => $produk->sku ? $produk->sku . '-' . $size : null,
                    'stok' => $qty,
                ]
            );

            if (\Illuminate\Support\Facades\Schema::hasTable('warehouse_stocks')) {
                \App\Models\WarehouseStock::updateOrCreate(
                    ['produk_id' => $produk->id, 'ukuran' => $size],
                    ['stok' => $qty]
                );
            }
        }

        if ($keepSizes) {
            ProductSizeStock::where('produk_id', $produk->id)
                ->whereNotIn('ukuran', $keepSizes)
                ->delete();

            if (\Illuminate\Support\Facades\Schema::hasTable('warehouse_stocks')) {
                \App\Models\WarehouseStock::where('produk_id', $produk->id)
                    ->whereNotIn('ukuran', $keepSizes)
                    ->delete();
            }
        }

        $totalStock = ProductSizeStock::where('produk_id', $produk->id)->sum('stok');

        if ($totalStock > 0 || $keepSizes) {
            $produk->forceFill([
                'stok' => (int) $totalStock,
                'ukuran' => implode(',', $keepSizes),
            ])->save();
        }
    }

    public function stockForSize(Produk $produk, string $size): int
    {
        $variant = ProductSizeStock::where('produk_id', $produk->id)
            ->where('ukuran', $size)
            ->first();

        return $variant ? (int) $variant->stok : (int) $produk->stok;
    }
    
    /**
     * Get product with calculated rating
     * 
     * @param Produk $product
     * @return array
     */
    public function formatProductWithRating(Produk $product): array
    {
        $avgRating = $product->reviews()
            ->where('moderation_status', 'approved')
            ->avg('rating') ?? 5.0;
        $sizeStocks = $product->relationLoaded('sizeStocks')
            ? $product->sizeStocks
            : $product->sizeStocks()->get();
        
        return [
            'id'            => $product->id,
            'nama'          => $product->nama_produk ?? '',
            'harga'         => $product->harga ?? 0,
            'harga_diskon'  => $product->harga_diskon ?? $product->harga ?? 0,
            'diskon_persen' => $product->diskon_persen ?? 0,
            'gambar'        => $product->gambar ?? '',
            'deskripsi'     => $product->deskripsi ?? '',
            'stok'          => $product->stok ?? 0,
            'size_stocks'   => $sizeStocks->mapWithKeys(fn ($stock) => [$stock->ukuran => (int) $stock->stok])->all(),
            'terjual'       => $product->terjual ?? 0,
            'rating'        => round($avgRating, 1),
            'gambar_url'    => AssetUrl::product($product->gambar),
        ];
    }
}
