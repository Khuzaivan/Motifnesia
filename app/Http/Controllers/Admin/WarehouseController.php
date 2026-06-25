<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\StockProcurement;
use App\Services\StockInventoryService;
use Illuminate\Support\Facades\Gate;

class WarehouseController extends Controller
{
    public function index(StockInventoryService $stockInventoryService)
    {
        Gate::authorize('is-gudang');

        $matrix = $stockInventoryService->stockMatrix();

        $readyProcurements = StockProcurement::with(['supplier', 'items'])
            ->whereIn('status', [
                StockProcurement::STATUS_APPROVED,
                StockProcurement::STATUS_IN_DELIVERY,
                StockProcurement::STATUS_ARRIVED,
            ])
            ->latest()
            ->take(8)
            ->get();

        $movements = StockMovement::with(['produk', 'user'])
            ->latest()
            ->take(12)
            ->get();

        return view('admin.pages.warehouse.index', [
            'matrix' => $matrix,
            'readyProcurements' => $readyProcurements,
            'movements' => $movements,
            'sizes' => StockInventoryService::SIZES,
            'activePage' => 'warehouse',
        ]);
    }
}
