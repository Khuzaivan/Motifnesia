@extends('supplier.layouts.mainLayout')

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

    <div class="supplier-card rounded-3xl p-6 supply-tilt" data-aos="fade-up">
        <p class="text-xs uppercase tracking-[0.18em] text-amber-300 font-bold">Supplier Dashboard</p>
        <h1 class="text-3xl font-extrabold text-white mt-1">{{ $supplier->name }}</h1>
        <p class="text-sm text-slate-400 mt-2">Terima pengadaan stok dari admin Motifnesia, lalu update status pengiriman sesuai kondisi barang.</p>
    </div>

    <div class="supplier-card rounded-3xl overflow-hidden supply-tilt" data-aos="fade-up">
        <div class="p-5 border-b border-white/10">
            <h2 class="text-xl font-extrabold text-white">Pesanan Pengadaan</h2>
            <p class="text-sm text-slate-400 mt-1">Status yang bisa supplier ubah: Pending menjadi Disetujui, lalu Disetujui menjadi Diantar.</p>
        </div>
        <div class="divide-y divide-white/5">
            @forelse($procurements as $procurement)
                <div class="p-5 hover:bg-white/[0.03] transition-colors" data-supply-animate>
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-3">
                                <h3 class="text-lg font-extrabold text-white">{{ $procurement->procurement_number }}</h3>
                                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-xs font-bold {{ $badgeClasses[$procurement->status] ?? 'bg-slate-500/10 text-slate-300 border-slate-500/20' }}">
                                    {{ $procurement->status_label }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-500 mt-2">{{ $procurement->items->count() }} produk | {{ $procurement->total_qty }} pcs | Dibuat {{ $procurement->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <a href="{{ route('supplier.procurements.show', $procurement) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold transition-all">
                            <i class="ri-eye-line"></i> Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-16 text-center text-slate-500">Belum ada pesanan pengadaan.</div>
            @endforelse
        </div>
    </div>

    @if($procurements->hasPages())
        <div class="flex justify-center">{{ $procurements->links() }}</div>
    @endif
</div>
@endsection
