@extends('admin.layouts.mainLayout')

@section('title', 'Pengadaan Stok')

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

    <div class="glass-card rounded-3xl p-6 supply-tilt" data-aos="fade-up">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-amber-300 font-bold">Procurement</p>
                <h1 class="text-3xl font-extrabold text-white mt-1">Pengadaan Stok Supplier</h1>
                <p class="text-sm text-slate-400 mt-2">Buat pengadaan, supplier menyetujui dan mengantar, lalu admin gudang menerapkan stok setelah barang sampai.</p>
            </div>
            <a href="{{ route('admin.stock-procurements.create') }}" class="btn-magnetic inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold transition-all">
                <i class="ri-add-line"></i> Buat Pengadaan
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        @foreach($statusLabels as $status => $label)
            <a href="{{ route('admin.stock-procurements.index', ['status' => $status]) }}" class="glass-card rounded-2xl p-4 hover:-translate-y-1 transition-all {{ $currentStatus === $status ? 'border-amber-400/40' : '' }}" data-supply-animate>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">{{ $label }}</p>
                <p class="text-2xl font-extrabold text-white mt-2">{{ (int) ($stats[$status] ?? 0) }}</p>
            </a>
        @endforeach
    </div>

    <div class="glass-card rounded-3xl overflow-hidden supply-tilt" data-aos="fade-up">
        <div class="p-5 border-b border-white/10 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.stock-procurements.index', ['status' => 'all']) }}" class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $currentStatus === 'all' ? 'bg-amber-500 text-slate-950' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">Semua</a>
                @foreach($statusLabels as $status => $label)
                    <a href="{{ route('admin.stock-procurements.index', ['status' => $status]) }}" class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ $currentStatus === $status ? 'bg-amber-500 text-slate-950' : 'bg-white/5 text-slate-300 hover:bg-white/10' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-white/5">
                    <tr>
                        <th class="px-6 py-4 font-bold">No Pengadaan</th>
                        <th class="px-6 py-4 font-bold">Supplier</th>
                        <th class="px-6 py-4 font-bold">Total Qty</th>
                        <th class="px-6 py-4 font-bold">Status</th>
                        <th class="px-6 py-4 font-bold">Dibuat</th>
                        <th class="px-6 py-4 text-right font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($procurements as $procurement)
                        <tr class="hover:bg-white/[0.03] transition-colors" data-supply-animate>
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-white">{{ $procurement->procurement_number }}</div>
                                <div class="text-xs text-slate-500">{{ $procurement->items->count() }} produk</div>
                            </td>
                            <td class="px-6 py-4">{{ $procurement->supplier?->name ?? 'Supplier dihapus' }}</td>
                            <td class="px-6 py-4 font-bold text-white">{{ $procurement->total_qty }} pcs</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-xs font-bold {{ $badgeClasses[$procurement->status] ?? 'bg-slate-500/10 text-slate-300 border-slate-500/20' }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span> {{ $procurement->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div>{{ $procurement->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-slate-500">{{ $procurement->creator?->full_name ?: $procurement->creator?->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.stock-procurements.show', $procurement) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-white/5 hover:bg-amber-500 text-slate-200 hover:text-slate-950 border border-white/10 font-bold transition-all">
                                    <i class="ri-eye-line"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-500">Belum ada pengadaan stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($procurements->hasPages())
        <div class="flex justify-center">{{ $procurements->links() }}</div>
    @endif
</div>
@endsection
