<?php

namespace App\Services;

use App\Models\ProductSizeStock;
use App\Models\Produk;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockProcurement;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockInventoryService
{
    public const SIZES = ['S', 'M', 'L', 'XL'];

    public function applyProcurement(StockProcurement $procurement): void
    {
        DB::transaction(function () use ($procurement) {
            $procurement = StockProcurement::whereKey($procurement->id)->lockForUpdate()->firstOrFail();

            if ($procurement->status !== StockProcurement::STATUS_ARRIVED) {
                throw new \RuntimeException('Stok hanya bisa diterapkan setelah barang dikonfirmasi sampai.');
            }

            $procurement->loadMissing('items.produk');

            foreach ($procurement->items as $item) {
                foreach ($item->sizeQuantities() as $size => $qty) {
                    if ($qty <= 0) {
                        continue;
                    }

                    $this->increaseWarehouseAndSystemStock(
                        $item->produk_id,
                        $size,
                        $qty,
                        StockProcurement::class,
                        $procurement->id,
                        'Tambahan stok dari pengadaan ' . $procurement->procurement_number
                    );
                }
            }

            $procurement->forceFill([
                'status' => StockProcurement::STATUS_STOCK_APPLIED,
                'applied_by' => Auth::id(),
                'stock_applied_at' => now(),
            ])->save();
        });
    }

    public function adjustSystemToWarehouse(int $productId, string $size, ?string $note = null): StockOpname
    {
        $size = strtoupper(trim($size));

        return DB::transaction(function () use ($productId, $size, $note) {
            $product = Produk::whereKey($productId)->lockForUpdate()->firstOrFail();
            $systemStock = ProductSizeStock::where('produk_id', $productId)
                ->where('ukuran', $size)
                ->lockForUpdate()
                ->first();
            $warehouseStock = WarehouseStock::where('produk_id', $productId)
                ->where('ukuran', $size)
                ->lockForUpdate()
                ->first();

            $systemBefore = (int) ($systemStock?->stok ?? 0);
            $warehouseBefore = (int) ($warehouseStock?->stok ?? 0);

            $systemStock = ProductSizeStock::updateOrCreate(
                ['produk_id' => $productId, 'ukuran' => $size],
                [
                    'sku' => $product->sku ? $product->sku . '-' . $size : null,
                    'stok' => $warehouseBefore,
                ]
            );

            $this->syncProductTotalStock($productId);

            $opname = StockOpname::create([
                'opname_number' => $this->generateOpnameNumber(),
                'produk_id' => $productId,
                'ukuran' => $size,
                'system_stock_before' => $systemBefore,
                'warehouse_stock_before' => $warehouseBefore,
                'system_stock_after' => (int) $systemStock->stok,
                'difference' => $warehouseBefore - $systemBefore,
                'adjusted_by' => Auth::id(),
                'note' => $note,
            ]);

            StockMovement::create([
                'produk_id' => $productId,
                'ukuran' => $size,
                'movement_type' => 'stock_opname',
                'reference_type' => StockOpname::class,
                'reference_id' => $opname->id,
                'qty_change' => $warehouseBefore - $systemBefore,
                'system_stock_before' => $systemBefore,
                'system_stock_after' => (int) $systemStock->stok,
                'warehouse_stock_before' => $warehouseBefore,
                'warehouse_stock_after' => $warehouseBefore,
                'user_id' => Auth::id(),
                'note' => $note ?: 'Stok sistem disesuaikan dengan stok gudang.',
            ]);

            return $opname;
        });
    }

    public function increaseWarehouseAndSystemStock(int $productId, string $size, int $qty, ?string $referenceType = null, ?int $referenceId = null, ?string $note = null): void
    {
        $size = strtoupper(trim($size));

        DB::transaction(function () use ($productId, $size, $qty, $referenceType, $referenceId, $note) {
            $product = Produk::whereKey($productId)->lockForUpdate()->firstOrFail();

            $systemStock = ProductSizeStock::where('produk_id', $productId)
                ->where('ukuran', $size)
                ->lockForUpdate()
                ->first();
            $warehouseStock = WarehouseStock::where('produk_id', $productId)
                ->where('ukuran', $size)
                ->lockForUpdate()
                ->first();

            $systemBefore = (int) ($systemStock?->stok ?? 0);
            $warehouseBefore = (int) ($warehouseStock?->stok ?? 0);

            $systemStock = ProductSizeStock::updateOrCreate(
                ['produk_id' => $productId, 'ukuran' => $size],
                [
                    'sku' => $product->sku ? $product->sku . '-' . $size : null,
                    'stok' => $systemBefore + $qty,
                ]
            );

            $warehouseStock = WarehouseStock::updateOrCreate(
                ['produk_id' => $productId, 'ukuran' => $size],
                [
                    'stok' => $warehouseBefore + $qty,
                    'updated_by' => Auth::id(),
                ]
            );

            $this->syncProductTotalStock($productId);

            StockMovement::create([
                'produk_id' => $productId,
                'ukuran' => $size,
                'movement_type' => 'procurement_applied',
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'qty_change' => $qty,
                'system_stock_before' => $systemBefore,
                'system_stock_after' => (int) $systemStock->stok,
                'warehouse_stock_before' => $warehouseBefore,
                'warehouse_stock_after' => (int) $warehouseStock->stok,
                'user_id' => Auth::id(),
                'note' => $note,
            ]);
        });
    }

    public function syncProductTotalStock(int $productId): void
    {
        $total = ProductSizeStock::where('produk_id', $productId)->sum('stok');

        Produk::whereKey($productId)->update([
            'stok' => (int) $total,
        ]);
    }

    public function stockMatrix()
    {
        return Produk::with(['sizeStocks', 'warehouseStocks'])
            ->where('is_active', true)
            ->orderBy('nama_produk')
            ->get()
            ->map(function (Produk $product) {
                return [
                    'product' => $product,
                    'sizes' => collect(self::SIZES)->mapWithKeys(function (string $size) use ($product) {
                        $system = (int) ($product->sizeStocks->firstWhere('ukuran', $size)?->stok ?? 0);
                        $warehouse = (int) ($product->warehouseStocks->firstWhere('ukuran', $size)?->stok ?? 0);

                        return [$size => [
                            'system' => $system,
                            'warehouse' => $warehouse,
                            'difference' => $warehouse - $system,
                        ]];
                    }),
                ];
            });
    }

    private function generateOpnameNumber(): string
    {
        return 'OPN-' . now()->format('Ymd-His') . '-' . random_int(100, 999);
    }
}
