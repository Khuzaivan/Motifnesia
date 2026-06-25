<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\StockProcurement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcurementController extends Controller
{
    public function index()
    {
        $supplier = $this->currentSupplier();

        $procurements = StockProcurement::with('items.produk')
            ->where('supplier_id', $supplier->id)
            ->latest()
            ->paginate(10);

        return view('supplier.procurements.index', [
            'supplier' => $supplier,
            'procurements' => $procurements,
            'statusLabels' => StockProcurement::statusLabels(),
        ]);
    }

    public function show(StockProcurement $procurement)
    {
        $supplier = $this->currentSupplier();
        abort_if((int) $procurement->supplier_id !== (int) $supplier->id, 403);

        $procurement->load(['supplier', 'items.produk.sizeStocks', 'creator', 'confirmer']);

        return view('supplier.procurements.show', [
            'supplier' => $supplier,
            'procurement' => $procurement,
            'statusLabels' => StockProcurement::statusLabels(),
        ]);
    }

    public function updateStatus(Request $request, StockProcurement $procurement)
    {
        $supplier = $this->currentSupplier();
        abort_if((int) $procurement->supplier_id !== (int) $supplier->id, 403);

        $validated = $request->validate([
            'status' => 'required|in:approved,in_delivery',
        ]);

        $targetStatus = $validated['status'];
        $payload = [];

        if ($targetStatus === StockProcurement::STATUS_APPROVED && $procurement->status === StockProcurement::STATUS_PENDING) {
            $payload = [
                'status' => StockProcurement::STATUS_APPROVED,
                'approved_at' => now(),
            ];
        } elseif ($targetStatus === StockProcurement::STATUS_IN_DELIVERY && $procurement->status === StockProcurement::STATUS_APPROVED) {
            $payload = [
                'status' => StockProcurement::STATUS_IN_DELIVERY,
                'in_delivery_at' => now(),
            ];
        } else {
            return back()->with('error', 'Perubahan status tidak sesuai alur pengadaan.');
        }

        $procurement->update($payload);

        return back()->with('success', 'Status pengadaan berhasil diperbarui menjadi ' . $procurement->fresh()->status_label . '.');
    }

    private function currentSupplier(): Supplier
    {
        return Supplier::where('user_id', Auth::id())->firstOrFail();
    }
}
