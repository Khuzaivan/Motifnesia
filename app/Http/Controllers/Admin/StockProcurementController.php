<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\StockProcurement;
use App\Models\Supplier;
use App\Services\StockInventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class StockProcurementController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('is-gudang');

        $status = $request->get('status', 'all');

        $query = StockProcurement::with(['supplier', 'creator', 'items'])
            ->latest();

        if ($status !== 'all' && array_key_exists($status, StockProcurement::statusLabels())) {
            $query->where('status', $status);
        }

        $procurements = $query->paginate(10)->withQueryString();

        $stats = StockProcurement::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.pages.stockProcurements.index', [
            'procurements' => $procurements,
            'statusLabels' => StockProcurement::statusLabels(),
            'currentStatus' => $status,
            'stats' => $stats,
            'activePage' => 'stock-procurements',
        ]);
    }

    public function create()
    {
        Gate::authorize('is-gudang');

        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $products = Produk::with('sizeStocks')
            ->where('is_active', true)
            ->orderBy('nama_produk')
            ->get();

        return view('admin.pages.stockProcurements.create', [
            'suppliers' => $suppliers,
            'products' => $products,
            'sizes' => StockInventoryService::SIZES,
            'activePage' => 'stock-procurements',
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('is-gudang');

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'note' => 'nullable|string|max:1000',
            'items' => 'required|array',
            'items.*.qty_s' => 'nullable|integer|min:0',
            'items.*.qty_m' => 'nullable|integer|min:0',
            'items.*.qty_l' => 'nullable|integer|min:0',
            'items.*.qty_xl' => 'nullable|integer|min:0',
        ]);

        $items = $this->normalizeItems($validated['items']);

        if (empty($items)) {
            return back()->withInput()
                ->with('error', 'Minimal isi satu produk dan satu ukuran dengan qty lebih dari 0.');
        }

        $procurement = DB::transaction(function () use ($validated, $items) {
            $procurement = StockProcurement::create([
                'procurement_number' => $this->generateProcurementNumber(),
                'supplier_id' => $validated['supplier_id'],
                'created_by' => auth()->id(),
                'status' => StockProcurement::STATUS_PENDING,
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($items as $item) {
                $procurement->items()->create($item);
            }

            return $procurement;
        });

        return redirect()->route('admin.stock-procurements.show', $procurement)
            ->with('success', 'Pengadaan stok berhasil dibuat dan siap diproses supplier.');
    }

    public function show(StockProcurement $stockProcurement)
    {
        Gate::authorize('is-gudang');

        $stockProcurement->load(['supplier.user', 'creator', 'confirmer', 'applier', 'items.produk.sizeStocks']);

        return view('admin.pages.stockProcurements.show', [
            'procurement' => $stockProcurement,
            'statusLabels' => StockProcurement::statusLabels(),
            'sizes' => StockInventoryService::SIZES,
            'activePage' => 'stock-procurements',
        ]);
    }

    public function confirmArrived(StockProcurement $stockProcurement)
    {
        Gate::authorize('is-gudang');

        if (! in_array($stockProcurement->status, [StockProcurement::STATUS_APPROVED, StockProcurement::STATUS_IN_DELIVERY], true)) {
            return back()->with('error', 'Barang hanya bisa dikonfirmasi sampai setelah disetujui atau sedang diantar supplier.');
        }

        $stockProcurement->update([
            'status' => StockProcurement::STATUS_ARRIVED,
            'confirmed_by' => auth()->id(),
            'arrived_at' => now(),
        ]);

        return back()->with('success', 'Barang pengadaan dikonfirmasi sudah sampai. Admin gudang dapat menerapkan stok.');
    }

    public function applyStock(StockProcurement $stockProcurement, StockInventoryService $stockInventoryService)
    {
        Gate::authorize('is-gudang');

        try {
            $stockInventoryService->applyProcurement($stockProcurement);
        } catch (\RuntimeException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.stock-procurements.show', $stockProcurement)
            ->with('success', 'Stok gudang dan stok sistem berhasil diterapkan.');
    }

    private function normalizeItems(array $rawItems): array
    {
        $productIds = collect(array_keys($rawItems))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values();

        $products = Produk::whereIn('id', $productIds)
            ->where('is_active', true)
            ->pluck('id')
            ->all();

        $validProductIds = array_flip($products);
        $items = [];

        foreach ($rawItems as $productId => $row) {
            $productId = (int) $productId;

            if (! isset($validProductIds[$productId])) {
                continue;
            }

            $qtyS = max(0, (int) ($row['qty_s'] ?? 0));
            $qtyM = max(0, (int) ($row['qty_m'] ?? 0));
            $qtyL = max(0, (int) ($row['qty_l'] ?? 0));
            $qtyXl = max(0, (int) ($row['qty_xl'] ?? 0));
            $total = $qtyS + $qtyM + $qtyL + $qtyXl;

            if ($total <= 0) {
                continue;
            }

            $items[] = [
                'produk_id' => $productId,
                'qty_s' => $qtyS,
                'qty_m' => $qtyM,
                'qty_l' => $qtyL,
                'qty_xl' => $qtyXl,
                'total_qty' => $total,
            ];
        }

        return $items;
    }

    private function generateProcurementNumber(): string
    {
        do {
            $number = 'PGD-' . now()->format('Ymd-His') . '-' . random_int(100, 999);
        } while (StockProcurement::where('procurement_number', $number)->exists());

        return $number;
    }
}
