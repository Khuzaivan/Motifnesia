@extends('admin.layouts.mainLayout')

@section('title', 'Gudang')

@section('content')
@php
    $totalSystem = $matrix->sum(fn ($row) => $row['sizes']->sum('system'));
    $totalWarehouse = $matrix->sum(fn ($row) => $row['sizes']->sum('warehouse'));
    $diffRows = $matrix->filter(fn ($row) => $row['sizes']->contains(fn ($size) => $size['difference'] !== 0))->count();
    $badgeClasses = [
        'approved' => 'bg-blue-500/10 text-blue-300 border-blue-500/20',
        'in_delivery' => 'bg-cyan-500/10 text-cyan-300 border-cyan-500/20',
        'arrived' => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
    ];
@endphp

<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 p-4 rounded-xl font-semibold motion-pop">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-300 p-4 rounded-xl font-semibold motion-pop">{{ session('error') }}</div>
    @endif

    <div class="glass-card rounded-3xl p-6 supply-tilt" data-aos="fade-up">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-amber-300 font-bold">Warehouse Control</p>
                <h1 class="text-3xl font-extrabold text-white mt-1">Gudang Motifnesia</h1>
                <p class="text-sm text-slate-400 mt-2">Monitor stok gudang dan stok sistem per ukuran. Stok masuk diterapkan dari pengadaan yang sudah sampai.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.stock-procurements.create') }}" class="btn-magnetic inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold transition-all">
                    <i class="ri-add-line"></i> Buat Pengadaan
                </a>
                <a href="{{ route('admin.stock-opname.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 font-bold transition-all">
                    <i class="ri-file-list-3-line"></i> Stock Opname
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-5" data-supply-animate>
            <p class="text-xs uppercase tracking-widest text-slate-400 font-bold">Produk Aktif</p>
            <p class="text-3xl font-extrabold text-white mt-2">{{ $matrix->count() }}</p>
        </div>
        <div class="glass-card rounded-2xl p-5" data-supply-animate>
            <p class="text-xs uppercase tracking-widest text-slate-400 font-bold">Stok Sistem</p>
            <p class="text-3xl font-extrabold text-white mt-2">{{ $totalSystem }}</p>
        </div>
        <div class="glass-card rounded-2xl p-5" data-supply-animate>
            <p class="text-xs uppercase tracking-widest text-slate-400 font-bold">Stok Gudang</p>
            <p class="text-3xl font-extrabold text-amber-300 mt-2">{{ $totalWarehouse }}</p>
        </div>
        <div class="glass-card rounded-2xl p-5" data-supply-animate>
            <p class="text-xs uppercase tracking-widest text-slate-400 font-bold">Selisih Produk</p>
            <p class="text-3xl font-extrabold {{ $diffRows > 0 ? 'text-red-300' : 'text-emerald-300' }} mt-2">{{ $diffRows }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1fr_420px] gap-6">
        <div class="glass-card rounded-3xl overflow-hidden supply-tilt" data-aos="fade-up">
            <div class="p-5 border-b border-white/10">
                <h2 class="text-xl font-extrabold text-white">Matrix Stok Per Ukuran</h2>
                <p class="text-sm text-slate-400 mt-1">Sistem adalah stok yang dipakai checkout. Gudang adalah stok fisik hasil penerimaan.</p>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-white/5">
                        <tr>
                            <th class="px-6 py-4 font-bold min-w-[260px]">Produk</th>
                            @foreach($sizes as $size)
                                <th class="px-4 py-4 text-center font-bold min-w-[140px]">{{ $size }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($matrix as $row)
                            <tr class="hover:bg-white/[0.03] transition-colors" data-supply-animate>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $row['product']->image_url }}" alt="{{ $row['product']->nama_produk }}" class="w-14 h-14 rounded-xl object-cover bg-white/5 border border-white/10">
                                        <div>
                                            <div class="font-extrabold text-white">{{ $row['product']->nama_produk }}</div>
                                            <div class="text-xs text-slate-500">{{ $row['product']->sku ?: 'SKU belum ada' }}</div>
                                        </div>
                                    </div>
                                </td>
                                @foreach($row['sizes'] as $size => $stock)
                                    <td class="px-4 py-4 text-center">
                                        <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-3">
                                            <div class="grid grid-cols-2 gap-2 text-xs">
                                                <div>
                                                    <p class="text-slate-500">Sistem</p>
                                                    <p class="font-extrabold text-white text-lg">{{ $stock['system'] }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-slate-500">Gudang</p>
                                                    <p class="font-extrabold text-amber-300 text-lg">{{ $stock['warehouse'] }}</p>
                                                </div>
                                            </div>
                                            <p class="mt-2 text-xs font-bold {{ $stock['difference'] === 0 ? 'text-emerald-300' : 'text-red-300' }}">
                                                Selisih {{ $stock['difference'] }}
                                            </p>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-slate-500">Belum ada produk aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="glass-card rounded-3xl overflow-hidden supply-tilt" data-aos="fade-left">
                <div class="p-5 border-b border-white/10">
                    <h2 class="text-xl font-extrabold text-white">Barang Masuk</h2>
                    <p class="text-sm text-slate-400 mt-1">Pengadaan yang perlu dipantau gudang.</p>
                </div>
                <div class="divide-y divide-white/5">
                    @forelse($readyProcurements as $procurement)
                        <div class="p-5" data-supply-animate>
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-extrabold text-white">{{ $procurement->procurement_number }}</p>
                                    <p class="text-sm text-slate-500">{{ $procurement->supplier?->name ?? 'Supplier dihapus' }} | {{ $procurement->total_qty }} pcs</p>
                                </div>
                                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-xs font-bold {{ $badgeClasses[$procurement->status] ?? 'bg-slate-500/10 text-slate-300 border-slate-500/20' }}">
                                    {{ $procurement->status_label }}
                                </span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <a href="{{ route('admin.stock-procurements.show', $procurement) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-200 text-sm font-bold">
                                    <i class="ri-eye-line"></i> Detail
                                </a>
                                @if(in_array($procurement->status, ['approved', 'in_delivery'], true))
                                    <form action="{{ route('admin.stock-procurements.confirm-arrived', $procurement) }}" method="POST">
                                        @csrf
                                        <button class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-500/10 hover:bg-emerald-500 text-emerald-300 hover:text-white text-sm font-bold">
                                            <i class="ri-check-double-line"></i> Sampai
                                        </button>
                                    </form>
                                @elseif($procurement->status === 'arrived')
                                    <form action="{{ route('admin.stock-procurements.apply-stock', $procurement) }}" method="POST">
                                        @csrf
                                        <button class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-sm font-extrabold">
                                            <i class="ri-archive-stack-line"></i> Terapkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center text-slate-500">Belum ada barang masuk aktif.</div>
                    @endforelse
                </div>
            </div>

            <div class="glass-card rounded-3xl overflow-hidden supply-tilt" data-aos="fade-left">
                <div class="p-5 border-b border-white/10">
                    <h2 class="text-xl font-extrabold text-white">Pergerakan Stok</h2>
                    <p class="text-sm text-slate-400 mt-1">Riwayat terbaru dari procurement dan opname.</p>
                </div>
                <div class="divide-y divide-white/5 max-h-[520px] overflow-y-auto custom-scrollbar">
                    @forelse($movements as $movement)
                        <div class="p-4" data-supply-animate>
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-bold text-white">{{ $movement->produk?->nama_produk ?? 'Produk dihapus' }} ukuran {{ $movement->ukuran }}</p>
                                    <p class="text-xs text-slate-500">{{ str_replace('_', ' ', $movement->movement_type) }} | {{ $movement->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <span class="font-extrabold {{ $movement->qty_change >= 0 ? 'text-emerald-300' : 'text-red-300' }}">{{ $movement->qty_change >= 0 ? '+' : '' }}{{ $movement->qty_change }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-2">{{ $movement->note ?: '-' }}</p>
                        </div>
                    @empty
                        <div class="p-10 text-center text-slate-500">Belum ada pergerakan stok.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
