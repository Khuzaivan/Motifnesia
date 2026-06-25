@extends('supplier.layouts.mainLayout')

@section('title', 'Detail Pengadaan')

@section('content')
@php
    $badgeClasses = [
        'pending' => 'bg-amber-500/10 text-amber-300 border-amber-500/20',
        'approved' => 'bg-blue-500/10 text-blue-300 border-blue-500/20',
        'in_delivery' => 'bg-cyan-500/10 text-cyan-300 border-cyan-500/20',
        'arrived' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
        'stock_applied' => 'bg-violet-500/10 text-violet-300 border-violet-500/20',
    ];
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 p-4 rounded-xl font-semibold motion-pop">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-300 p-4 rounded-xl font-semibold motion-pop">{{ session('error') }}</div>
    @endif

    <div class="supplier-card rounded-3xl p-6 supply-tilt" data-aos="fade-up">
        <a href="{{ route('supplier.procurements.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-amber-300 transition-colors mb-3">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
        <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-amber-300 font-bold">Procurement Order</p>
                <h1 class="text-3xl font-extrabold text-white mt-1">{{ $procurement->procurement_number }}</h1>
                <p class="text-sm text-slate-400 mt-2">{{ $procurement->note ?: 'Tidak ada catatan pengadaan.' }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @if($procurement->status === 'pending')
                    <form action="{{ route('supplier.procurements.status', $procurement) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="approved">
                        <button class="btn-magnetic inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-blue-500 hover:bg-blue-600 text-white font-extrabold transition-all">
                            <i class="ri-check-line"></i> Setujui Pengadaan
                        </button>
                    </form>
                @elseif($procurement->status === 'approved')
                    <form action="{{ route('supplier.procurements.status', $procurement) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="in_delivery">
                        <button class="btn-magnetic inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold transition-all">
                            <i class="ri-truck-line"></i> Barang Diantar
                        </button>
                    </form>
                @endif
                <span class="inline-flex items-center gap-2 px-4 py-3 rounded-xl border text-sm font-bold {{ $badgeClasses[$procurement->status] ?? 'bg-slate-500/10 text-slate-300 border-slate-500/20' }}">
                    {{ $procurement->status_label }}
                </span>
            </div>
        </div>
    </div>

    <div class="supplier-card rounded-3xl overflow-hidden supply-tilt" data-aos="fade-up">
        <div class="p-5 border-b border-white/10">
            <h2 class="text-xl font-extrabold text-white">Item yang Dipesan</h2>
            <p class="text-sm text-slate-400 mt-1">Total {{ $procurement->total_qty }} pcs dari {{ $procurement->items->count() }} produk.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-white/5">
                    <tr>
                        <th class="px-6 py-4 font-bold min-w-[260px]">Produk</th>
                        <th class="px-4 py-4 text-center font-bold">S</th>
                        <th class="px-4 py-4 text-center font-bold">M</th>
                        <th class="px-4 py-4 text-center font-bold">L</th>
                        <th class="px-4 py-4 text-center font-bold">XL</th>
                        <th class="px-6 py-4 text-center font-bold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($procurement->items as $item)
                        <tr class="hover:bg-white/[0.03] transition-colors" data-supply-animate>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->produk?->image_url }}" alt="{{ $item->produk?->nama_produk }}" class="w-14 h-14 rounded-xl object-cover bg-white/5 border border-white/10">
                                    <div>
                                        <div class="font-extrabold text-white">{{ $item->produk?->nama_produk ?? 'Produk dihapus' }}</div>
                                        <div class="text-xs text-slate-500">{{ $item->produk?->sku ?: 'SKU belum ada' }}</div>
                                    </div>
                                </div>
                            </td>
                            @foreach($item->sizeQuantities() as $qty)
                                <td class="px-4 py-4 text-center font-bold {{ $qty > 0 ? 'text-white' : 'text-slate-600' }}">{{ $qty }}</td>
                            @endforeach
                            <td class="px-6 py-4 text-center font-extrabold text-amber-300">{{ $item->total_qty }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="supplier-card rounded-3xl p-6 supply-tilt" data-aos="fade-up">
        <h2 class="text-xl font-extrabold text-white">Catatan Alur</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-4 text-sm">
            <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                <p class="font-extrabold text-amber-300">1. Pending</p>
                <p class="text-slate-500 mt-1">Menunggu supplier menyetujui.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                <p class="font-extrabold text-blue-300">2. Disetujui</p>
                <p class="text-slate-500 mt-1">Supplier siap menyiapkan barang.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                <p class="font-extrabold text-cyan-300">3. Diantar</p>
                <p class="text-slate-500 mt-1">Barang sedang dikirim ke Motifnesia.</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                <p class="font-extrabold text-emerald-300">4. Sampai</p>
                <p class="text-slate-500 mt-1">Dikonfirmasi admin gudang.</p>
            </div>
        </div>
    </div>
</div>
@endsection
