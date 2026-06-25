@extends('admin.layouts.mainLayout')

@section('title', 'Buat Pengadaan Stok')

@section('content')
<div class="space-y-6">
    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-300 p-4 rounded-xl font-semibold motion-pop">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-300 p-4 rounded-xl motion-pop">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="glass-card rounded-3xl p-6 supply-tilt" data-aos="fade-up">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-amber-300 font-bold">Procurement Order</p>
                <h1 class="text-3xl font-extrabold text-white mt-1">Buat Pengadaan Stok</h1>
                <p class="text-sm text-slate-400 mt-2">Qty diisi per ukuran supaya stok sistem dan gudang tetap presisi.</p>
            </div>
            <a href="{{ route('admin.stock-procurements.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 font-bold transition-all">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('admin.stock-procurements.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="glass-card rounded-3xl p-6 grid grid-cols-1 lg:grid-cols-[1fr_1fr_auto] gap-4 items-end" data-aos="fade-up">
            <div>
                <label class="block text-sm font-bold text-slate-300 mb-2">Supplier</label>
                <select name="supplier_id" required class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none">
                    <option value="">Pilih supplier</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-300 mb-2">Catatan Pengadaan</label>
                <input type="text" name="note" value="{{ old('note') }}" class="w-full px-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none" placeholder="Contoh: Restock koleksi batik pria minggu ini">
            </div>
            <div class="rounded-2xl bg-amber-500/10 border border-amber-500/20 px-5 py-3">
                <p class="text-xs text-amber-200 font-bold uppercase tracking-widest">Total Qty</p>
                <p id="procurementGrandTotal" class="text-2xl font-extrabold text-white">0</p>
            </div>
        </div>

        <div class="glass-card rounded-3xl overflow-hidden supply-tilt" data-aos="fade-up">
            <div class="p-5 border-b border-white/10 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h2 class="text-xl font-extrabold text-white">Produk Pengadaan</h2>
                    <p class="text-sm text-slate-400 mt-1">Isi qty ukuran S, M, L, XL pada produk yang ingin dipesan ke supplier.</p>
                </div>
                <div class="relative">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-500"></i>
                    <input type="text" id="procurementProductSearch" class="w-full md:w-72 pl-10 pr-4 py-3 bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none" placeholder="Cari produk...">
                </div>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-white/5">
                        <tr>
                            <th class="px-6 py-4 font-bold min-w-[280px]">Produk</th>
                            @foreach($sizes as $size)
                                <th class="px-3 py-4 text-center font-bold">{{ $size }}</th>
                            @endforeach
                            <th class="px-6 py-4 text-center font-bold">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($products as $product)
                            @php
                                $sizeStockMap = $product->sizeStocks->mapWithKeys(fn ($stock) => [$stock->ukuran => (int) $stock->stok]);
                            @endphp
                            <tr class="procurement-product-row hover:bg-white/[0.03] transition-colors" data-product-name="{{ strtolower($product->nama_produk . ' ' . $product->sku) }}" data-supply-animate>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->nama_produk }}" class="w-14 h-14 rounded-xl object-cover bg-white/5 border border-white/10">
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-white truncate">{{ $product->nama_produk }}</div>
                                            <div class="text-xs text-slate-500">{{ $product->sku ?: 'SKU belum ada' }}</div>
                                            <div class="text-xs text-slate-400 mt-1">Stok sistem: {{ $product->stok }} pcs</div>
                                        </div>
                                    </div>
                                </td>
                                @foreach($sizes as $size)
                                    @php $field = 'qty_' . strtolower($size); @endphp
                                    <td class="px-3 py-4">
                                        <input type="number" min="0" name="items[{{ $product->id }}][{{ $field }}]" value="{{ old('items.' . $product->id . '.' . $field, 0) }}" class="procurement-qty w-24 px-3 py-2 text-center bg-slate-900 border border-white/10 rounded-xl text-white focus:outline-none" data-row="{{ $product->id }}" placeholder="0">
                                        <div class="text-[11px] text-slate-500 mt-1 text-center">Stok {{ (int) ($sizeStockMap[$size] ?? 0) }}</div>
                                    </td>
                                @endforeach
                                <td class="px-6 py-4 text-center">
                                    <span class="procurement-row-total inline-flex min-w-14 justify-center px-3 py-2 rounded-xl bg-white/5 text-white font-extrabold border border-white/10" data-row-total="{{ $product->id }}">0</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center text-slate-500">Produk aktif belum tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-end gap-3">
            <a href="{{ route('admin.stock-procurements.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 font-bold transition-all">
                Batal
            </a>
            <button type="submit" class="btn-magnetic inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold transition-all">
                <i class="ri-send-plane-line"></i> Kirim ke Supplier
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const qtyInputs = document.querySelectorAll('.procurement-qty');
    const grandTotal = document.getElementById('procurementGrandTotal');
    const searchInput = document.getElementById('procurementProductSearch');

    function updateTotals() {
        const totals = {};
        let grand = 0;

        qtyInputs.forEach((input) => {
            const row = input.dataset.row;
            const value = Math.max(0, parseInt(input.value || '0', 10));
            totals[row] = (totals[row] || 0) + value;
            grand += value;
        });

        document.querySelectorAll('.procurement-row-total').forEach((target) => {
            target.textContent = totals[target.dataset.rowTotal] || 0;
        });

        grandTotal.textContent = grand;
    }

    qtyInputs.forEach((input) => input.addEventListener('input', updateTotals));
    updateTotals();

    searchInput?.addEventListener('input', () => {
        const keyword = searchInput.value.trim().toLowerCase();
        document.querySelectorAll('.procurement-product-row').forEach((row) => {
            row.style.display = row.dataset.productName.includes(keyword) ? '' : 'none';
        });
    });
});
</script>
@endpush
