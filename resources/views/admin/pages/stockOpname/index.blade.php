@extends('admin.layouts.mainLayout')

@section('title', 'Stock Opname')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 p-4 rounded-xl font-semibold motion-pop">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-300 p-4 rounded-xl motion-pop">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="glass-card rounded-3xl p-6 supply-tilt" data-aos="fade-up">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-amber-300 font-bold">Inventory Audit</p>
                <h1 class="text-3xl font-extrabold text-white mt-1">Stock Opname</h1>
                <p class="text-sm text-slate-400 mt-2">Samakan stok sistem dengan stok gudang per produk dan ukuran, lalu simpan riwayat koreksinya.</p>
            </div>
            <a href="{{ route('admin.warehouse.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-slate-200 border border-white/10 font-bold transition-all">
                <i class="ri-archive-stack-line"></i> Dashboard Gudang
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1fr_420px] gap-6">
        <div class="glass-card rounded-3xl overflow-hidden supply-tilt" data-aos="fade-up">
            <div class="p-5 border-b border-white/10">
                <h2 class="text-xl font-extrabold text-white">Koreksi Stok</h2>
                <p class="text-sm text-slate-400 mt-1">Klik sesuaikan jika stok sistem perlu mengikuti stok gudang.</p>
            </div>
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-white/5">
                        <tr>
                            <th class="px-6 py-4 font-bold min-w-[240px]">Produk</th>
                            @foreach($sizes as $size)
                                <th class="px-4 py-4 text-center font-bold min-w-[180px]">{{ $size }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($matrix as $row)
                            <tr class="hover:bg-white/[0.03] transition-colors" data-supply-animate>
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-white">{{ $row['product']->nama_produk }}</div>
                                    <div class="text-xs text-slate-500">{{ $row['product']->sku ?: 'SKU belum ada' }}</div>
                                </td>
                                @foreach($row['sizes'] as $size => $stock)
                                    <td class="px-4 py-4">
                                        <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-3 text-center">
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
                                            <p class="mt-2 text-xs font-bold {{ $stock['difference'] === 0 ? 'text-emerald-300' : 'text-red-300' }}">Selisih {{ $stock['difference'] }}</p>
                                            <form action="{{ route('admin.stock-opname.adjust') }}" method="POST" class="mt-3" onsubmit="return confirm('Sesuaikan stok sistem produk ini dengan stok gudang?')">
                                                @csrf
                                                <input type="hidden" name="produk_id" value="{{ $row['product']->id }}">
                                                <input type="hidden" name="ukuran" value="{{ $size }}">
                                                <input type="hidden" name="note" value="Stock opname manual dari dashboard gudang.">
                                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-extrabold transition-all {{ $stock['difference'] === 0 ? 'bg-white/5 text-slate-500 cursor-default' : 'bg-amber-500 hover:bg-amber-600 text-slate-950' }}" {{ $stock['difference'] === 0 ? 'disabled' : '' }}>
                                                    <i class="ri-refresh-line"></i> Sesuaikan
                                                </button>
                                            </form>
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

        <div class="glass-card rounded-3xl overflow-hidden supply-tilt" data-aos="fade-left">
            <div class="p-5 border-b border-white/10">
                <h2 class="text-xl font-extrabold text-white">Riwayat Opname</h2>
                <p class="text-sm text-slate-400 mt-1">Catatan koreksi stok sistem.</p>
            </div>
            <div class="divide-y divide-white/5 max-h-[760px] overflow-y-auto custom-scrollbar">
                @forelse($opnames as $opname)
                    <div class="p-5" data-supply-animate>
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-extrabold text-white">{{ $opname->opname_number }}</p>
                                <p class="text-sm text-slate-400">{{ $opname->produk?->nama_produk ?? 'Produk dihapus' }} ukuran {{ $opname->ukuran }}</p>
                                <p class="text-xs text-slate-500 mt-1">{{ $opname->created_at->format('d M Y H:i') }} oleh {{ $opname->adjustedBy?->full_name ?: $opname->adjustedBy?->name ?: '-' }}</p>
                            </div>
                            <span class="font-extrabold {{ $opname->difference >= 0 ? 'text-emerald-300' : 'text-red-300' }}">{{ $opname->difference >= 0 ? '+' : '' }}{{ $opname->difference }}</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mt-4 text-center text-xs">
                            <div class="rounded-xl bg-white/5 border border-white/10 p-3">
                                <p class="text-slate-500">Sistem Awal</p>
                                <p class="text-white font-extrabold text-base">{{ $opname->system_stock_before }}</p>
                            </div>
                            <div class="rounded-xl bg-white/5 border border-white/10 p-3">
                                <p class="text-slate-500">Gudang</p>
                                <p class="text-amber-300 font-extrabold text-base">{{ $opname->warehouse_stock_before }}</p>
                            </div>
                            <div class="rounded-xl bg-white/5 border border-white/10 p-3">
                                <p class="text-slate-500">Sistem Akhir</p>
                                <p class="text-white font-extrabold text-base">{{ $opname->system_stock_after }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-500">Belum ada riwayat opname.</div>
                @endforelse
            </div>
            @if($opnames->hasPages())
                <div class="p-4 border-t border-white/10">{{ $opnames->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
