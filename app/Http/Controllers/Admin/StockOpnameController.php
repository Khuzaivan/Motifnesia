<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\StockOpname;
use App\Services\StockInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StockOpnameController extends Controller
{
    public function index(StockInventoryService $stockInventoryService)
    {
        Gate::authorize('is-gudang');

        return view('admin.pages.stockOpname.index', [
            'matrix' => $stockInventoryService->stockMatrix(),
            'opnames' => StockOpname::with(['produk', 'adjustedBy'])->latest()->paginate(10),
            'sizes' => StockInventoryService::SIZES,
            'activePage' => 'stock-opname',
        ]);
    }

    public function adjust(Request $request, StockInventoryService $stockInventoryService)
    {
        Gate::authorize('is-gudang');

        $validated = $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'ukuran' => 'required|in:S,M,L,XL',
            'note' => 'nullable|string|max:1000',
        ]);

        $product = Produk::findOrFail($validated['produk_id']);
        $opname = $stockInventoryService->adjustSystemToWarehouse(
            $product->id,
            $validated['ukuran'],
            $validated['note'] ?? null
        );

        return redirect()->route('admin.stock-opname.index')
            ->with('success', 'Stock opname ' . $opname->opname_number . ' berhasil. Stok sistem ukuran ' . $validated['ukuran'] . ' sekarang mengikuti stok gudang.');
    }
}
