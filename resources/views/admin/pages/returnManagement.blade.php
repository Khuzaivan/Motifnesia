@extends('admin.layouts.mainLayout')

@section('title', 'Kelola Retur')

@section('content')
<div class="space-y-6">
    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5 animate-fade-slide-up">
        <div class="glass-card rounded-2xl p-5 flex items-center gap-4 group hover:border-amber-500/50 transition-colors">
            <div class="w-12 h-12 bg-amber-500/10 text-amber-500 rounded-xl flex items-center justify-center text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="ri-box-3-line"></i>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-medium mb-0.5">Total Retur</p>
                <h3 class="text-2xl font-bold text-white font-['Plus_Jakarta_Sans']">{{ $counts['all'] }}</h3>
            </div>
        </div>
        <div class="glass-card rounded-2xl p-5 flex items-center gap-4 group hover:border-yellow-500/50 transition-colors">
            <div class="w-12 h-12 bg-yellow-500/10 text-yellow-400 rounded-xl flex items-center justify-center text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="ri-time-line"></i>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-medium mb-0.5">Pending</p>
                <h3 class="text-2xl font-bold text-white font-['Plus_Jakarta_Sans']">{{ $counts['Pending'] }}</h3>
            </div>
        </div>
        <div class="glass-card rounded-2xl p-5 flex items-center gap-4 group hover:border-emerald-500/50 transition-colors">
            <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-xl flex items-center justify-center text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="ri-checkbox-circle-line"></i>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-medium mb-0.5">Disetujui</p>
                <h3 class="text-2xl font-bold text-white font-['Plus_Jakarta_Sans']">{{ $counts['Disetujui'] }}</h3>
            </div>
        </div>
        <div class="glass-card rounded-2xl p-5 flex items-center gap-4 group hover:border-blue-500/50 transition-colors">
            <div class="w-12 h-12 bg-blue-500/10 text-blue-400 rounded-xl flex items-center justify-center text-2xl shrink-0 group-hover:scale-110 transition-transform">
                <i class="ri-loader-4-line"></i>
            </div>
            <div>
                <p class="text-slate-400 text-xs font-medium mb-0.5">Diproses</p>
                <h3 class="text-2xl font-bold text-white font-['Plus_Jakarta_Sans']">{{ $counts['Diproses'] }}</h3>
            </div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="glass-card rounded-2xl p-2 flex flex-wrap gap-1 animate-fade-slide-up" style="animation-delay: 0.1s;">
        @php
            $tabFilters = [
                'all' => ['label' => 'Semua', 'count' => $counts['all'], 'color' => 'amber'],
                'Pending' => ['label' => 'Pending', 'count' => $counts['Pending'], 'color' => 'yellow'],
                'Disetujui' => ['label' => 'Disetujui', 'count' => $counts['Disetujui'], 'color' => 'emerald'],
                'Ditolak' => ['label' => 'Ditolak', 'count' => $counts['Ditolak'], 'color' => 'red'],
                'Diproses' => ['label' => 'Diproses', 'count' => $counts['Diproses'], 'color' => 'blue'],
                'Selesai' => ['label' => 'Selesai', 'count' => $counts['Selesai'], 'color' => 'slate'],
            ];
        @endphp
        @foreach($tabFilters as $filterKey => $filterData)
            <a href="{{ route('admin.returns.index', ['status' => $filterKey]) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all flex items-center gap-1.5
                   {{ $currentFilter === $filterKey ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                {{ $filterData['label'] }}
                <span class="text-[10px] px-1.5 py-0.5 rounded-md {{ $currentFilter === $filterKey ? 'bg-white/20' : 'bg-slate-800' }}">{{ $filterData['count'] }}</span>
            </a>
        @endforeach
    </div>

    {{-- Returns List --}}
    <div class="space-y-5 animate-fade-slide-up" style="animation-delay: 0.2s;">
        @forelse($returns as $return)
            @include('admin.components.returnCard', ['return' => $return])
        @empty
            <div class="text-center py-20 glass-card rounded-3xl">
                <i class="ri-inbox-archive-line text-6xl text-slate-600 mb-4 inline-block opacity-50"></i>
                <h3 class="text-xl font-bold font-['Plus_Jakarta_Sans'] text-slate-300 mb-2">Tidak Ada Retur</h3>
                <p class="text-slate-500 font-medium">{{ $currentFilter === 'all' ? 'Belum ada pengajuan retur dari customer.' : 'Tidak ada retur dengan status ' . $currentFilter }}</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($returns->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $returns->appends(['status' => $currentFilter])->links() }}
        </div>
    @endif
</div>

{{-- Toast --}}
<div id="toast" class="fixed top-24 right-6 bg-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl transform translate-x-full transition-transform duration-300 z-50 flex items-center gap-3 border border-emerald-500/50">
    <i class="ri-checkbox-circle-fill text-xl"></i>
    <span id="toastMsg" class="font-medium font-['Plus_Jakarta_Sans']"></span>
</div>

<script>
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMsg');
    
    if (type === 'error') {
        toast.className = 'fixed top-24 right-6 bg-red-600 text-white px-6 py-4 rounded-xl shadow-2xl transform transition-transform duration-300 z-50 flex items-center gap-3 border border-red-500/50';
    } else {
        toast.className = 'fixed top-24 right-6 bg-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl transform transition-transform duration-300 z-50 flex items-center gap-3 border border-emerald-500/50';
    }
    toastMsg.textContent = message;
    toast.classList.remove('translate-x-full');
    setTimeout(() => toast.classList.add('translate-x-full'), 3000);
}

@if(session('success'))
    showToast('{{ session('success') }}', 'success');
@endif
@if(session('error'))
    showToast('{{ session('error') }}', 'error');
@endif
</script>
@endsection