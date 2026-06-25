@extends('admin.layouts.mainLayout')

@section('title', 'Detail Pengadaan')

@section('content')
@php
    $statusOrder = ['pending', 'approved', 'in_delivery', 'arrived', 'stock_applied'];
    $statusIndex = array_search($procurement->status, $statusOrder, true);
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
        <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-5">
            <div>
                <a href="{{ route('admin.stock-procurements.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-amber-300 transition-colors mb-3">
                    <i class="ri-arrow-left-line"></i> Kembali ke pengadaan
                </a>
                <p class="text-xs uppercase tracking-[0.18em] text-amber-300 font-bold">Detail Procurement</p>
                <h1 class="text-3xl font-extrabold text-white mt-1">{{ $procurement->procurement_number }}</h1>
                <p class="text-sm text-slate-400 mt-2">{{ $procurement->note ?: 'Tidak ada catatan pengadaan.' }}</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                @if(in_array($procurement->status, ['approved', 'in_delivery'], true))
                    <form action="{{ route('admin.stock-procurements.confirm-arrived', $procurement) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-magnetic inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold transition-all">
                            <i class="ri-check-double-line"></i> Konfirmasi Sampai
                        </button>
                    </form>
                @endif

                @if($procurement->status === 'arrived')
                    <form action="{{ route('admin.stock-procurements.apply-stock', $procurement) }}" method="POST" onsubmit="return confirm('Terapkan stok pengadaan ini ke stok gudang dan stok sistem?')">
                        @csrf
                        <button type="submit" class="btn-magnetic inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-extrabold transition-all">
                            <i class="ri-archive-stack-line"></i> Terapkan Stok
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1fr_360px] gap-6">
        <div class="space-y-6">
            <div class="glass-card rounded-3xl p-6 supply-tilt" data-aos="fade-up">
                <div class="flex flex-wrap gap-3">
                    @foreach($statusOrder as $index => $status)
                        @php
                            $isDone = $statusIndex !== false && $index <= $statusIndex;
                            $isCurrent = $status === $procurement->status;
                        @endphp
                        <div class="flex-1 min-w-[140px] rounded-2xl border p-4 transition-all {{ $isDone ? 'border-amber-400/30 bg-amber-500/10' : 'border-white/10 bg-white/[0.03]' }}">
                            <div class="flex items-center justify-between gap-2">
                                <span class="w-8 h-8 rounded-xl flex items-center justify-center {{ $isDone ? 'bg-amber-500 text-slate-950' : 'bg-white/5 text-slate-500' }}">
                                    <i class="{{ $isDone ? 'ri-check-line' : 'ri-time-line' }}"></i>
                                </span>
                                @if($isCurrent)
                                    <span class="text-[10px] uppercase tracking-widest text-amber-200 font-extrabold">Saat ini</span>
                                @endif
                            </div>
                            <p class="text-sm font-extrabold text-white mt-3">{{ $statusLabels[$status] ?? $status }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="glass-card rounded-3xl overflow-hidden supply-tilt" data-aos="fade-up">
                <div class="p-5 border-b border-white/10 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-extrabold text-white">Item Pengadaan</h2>
                        <p class="text-sm text-slate-400 mt-1">Total {{ $procurement->total_qty }} pcs dari {{ $procurement->items->count() }} produk.</p>
                    </div>
                    <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border text-xs font-bold {{ $badgeClasses[$procurement->status] ?? 'bg-slate-500/10 text-slate-300 border-slate-500/20' }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span> {{ $procurement->status_label }}
                    </span>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-white/5">
                            <tr>
                                <th class="px-6 py-4 font-bold min-w-[260px]">Produk</th>
                                @foreach($sizes as $size)
                                    <th class="px-4 py-4 text-center font-bold">{{ $size }}</th>
                                @endforeach
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
                                    @foreach($item->sizeQuantities() as $size => $qty)
                                        <td class="px-4 py-4 text-center font-bold {{ $qty > 0 ? 'text-white' : 'text-slate-600' }}">{{ $qty }}</td>
                                    @endforeach
                                    <td class="px-6 py-4 text-center font-extrabold text-amber-300">{{ $item->total_qty }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="glass-card rounded-3xl p-6 supply-tilt" data-aos="fade-left">
                <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                    <i class="ri-building-4-line text-amber-400"></i> Supplier
                </h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <p class="text-slate-500">Nama</p>
                        <p class="text-white font-bold">{{ $procurement->supplier?->name ?? 'Supplier dihapus' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Kontak</p>
                        <p class="text-slate-300">{{ $procurement->supplier?->contact_person ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Email</p>
                        <p class="text-slate-300">{{ $procurement->supplier?->email ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Akun Portal</p>
                        <p class="text-slate-300">{{ $procurement->supplier?->user ? 'Tersambung' : 'Belum tersambung' }}</p>
                    </div>
                </div>
            </div>

            <div class="glass-card rounded-3xl p-6 supply-tilt" data-aos="fade-left">
                <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                    <i class="ri-time-line text-amber-400"></i> Timeline
                </h2>
                <div class="mt-5 space-y-4 text-sm">
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-amber-300"><i class="ri-add-line"></i></span>
                        <div>
                            <p class="font-bold text-white">Dibuat</p>
                            <p class="text-slate-500">{{ $procurement->created_at->format('d M Y H:i') }} oleh {{ $procurement->creator?->full_name ?: $procurement->creator?->name ?: '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-blue-300"><i class="ri-check-line"></i></span>
                        <div>
                            <p class="font-bold text-white">Disetujui Supplier</p>
                            <p class="text-slate-500">{{ optional($procurement->approved_at)->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-cyan-300"><i class="ri-truck-line"></i></span>
                        <div>
                            <p class="font-bold text-white">Diantar Supplier</p>
                            <p class="text-slate-500">{{ optional($procurement->in_delivery_at)->format('d M Y H:i') ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-emerald-300"><i class="ri-check-double-line"></i></span>
                        <div>
                            <p class="font-bold text-white">Dikonfirmasi Sampai</p>
                            <p class="text-slate-500">{{ optional($procurement->arrived_at)->format('d M Y H:i') ?? '-' }} oleh {{ $procurement->confirmer?->full_name ?: $procurement->confirmer?->name ?: '-' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-violet-300"><i class="ri-archive-stack-line"></i></span>
                        <div>
                            <p class="font-bold text-white">Stok Diterapkan</p>
                            <p class="text-slate-500">{{ optional($procurement->stock_applied_at)->format('d M Y H:i') ?? '-' }} oleh {{ $procurement->applier?->full_name ?: $procurement->applier?->name ?: '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
