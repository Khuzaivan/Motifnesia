@extends('admin.layouts.mainLayout')

@section('title', 'Notifikasi Sistem')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-card rounded-2xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 animate-fade-slide-up">
        <h2 class="text-xl font-bold font-['Plus_Jakarta_Sans'] text-white flex items-center gap-2">
            <i class="ri-notification-3-line text-amber-500"></i> Notifikasi Sistem
        </h2>
        <div class="flex gap-3 w-full sm:w-auto">
            <button onclick="markAllRead()" class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-400 hover:text-white font-semibold py-2 px-4 rounded-xl transition-all shadow-sm text-sm">
                <i class="ri-check-double-line"></i> Tandai Dibaca
            </button>
            <button onclick="clearRead()" class="flex-1 sm:flex-none flex items-center justify-center gap-2 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white font-semibold py-2 px-4 rounded-xl transition-all shadow-sm text-sm">
                <i class="ri-delete-bin-line"></i> Hapus Dibaca
            </button>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-slide-up" style="animation-delay: 0.1s;">
        <div class="glass-card rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-red-500/10 text-red-400 rounded-xl flex items-center justify-center text-2xl shrink-0">
                <i class="ri-notification-badge-line"></i>
            </div>
            <div>
                <p class="text-slate-400 text-sm font-medium mb-1">Belum Dibaca</p>
                <h3 class="text-2xl font-bold text-white font-['Plus_Jakarta_Sans']">{{ $unreadCount }}</h3>
            </div>
        </div>
        
        <div class="glass-card rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-500/10 text-blue-400 rounded-xl flex items-center justify-center text-2xl shrink-0">
                <i class="ri-calendar-event-line"></i>
            </div>
            <div>
                <p class="text-slate-400 text-sm font-medium mb-1">Hari Ini</p>
                <h3 class="text-2xl font-bold text-white font-['Plus_Jakarta_Sans']">{{ $todayCount }}</h3>
            </div>
        </div>
        
        <div class="glass-card rounded-2xl p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-500/10 text-amber-500 rounded-xl flex items-center justify-center text-2xl shrink-0">
                <i class="ri-bar-chart-box-line"></i>
            </div>
            <div>
                <p class="text-slate-400 text-sm font-medium mb-1">Total Notifikasi</p>
                <h3 class="text-2xl font-bold text-white font-['Plus_Jakarta_Sans']">{{ $totalCount }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter Tabs & Search -->
    <div class="glass-card rounded-2xl p-2 animate-fade-slide-up flex flex-col md:flex-row justify-between" style="animation-delay: 0.2s;">
        <div class="flex overflow-x-auto custom-scrollbar gap-1 p-1">
            <a href="{{ route('admin.notifications.index', ['filter' => 'all']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap {{ $currentFilter === 'all' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                Semua <span class="bg-slate-900/50 px-1.5 py-0.5 rounded-md text-[10px] ml-1">{{ $totalCount }}</span>
            </a>
            <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $currentFilter === 'unread' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i class="ri-mail-unread-line"></i> Belum Dibaca <span class="bg-red-500 px-1.5 py-0.5 rounded-md text-[10px] text-white ml-1">{{ $unreadCount }}</span>
            </a>
            <a href="{{ route('admin.notifications.index', ['filter' => 'order']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $currentFilter === 'order' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i class="ri-shopping-cart-2-line"></i> Pesanan
            </a>
            <a href="{{ route('admin.notifications.index', ['filter' => 'stock']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $currentFilter === 'stock' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i class="ri-box-3-line"></i> Stok
            </a>
            <a href="{{ route('admin.notifications.index', ['filter' => 'review']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $currentFilter === 'review' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i class="ri-star-line"></i> Review
            </a>
            <a href="{{ route('admin.notifications.index', ['filter' => 'return']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $currentFilter === 'return' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i class="ri-arrow-go-back-line"></i> Retur
            </a>
            <a href="{{ route('admin.notifications.index', ['filter' => 'membership']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $currentFilter === 'membership' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i class="ri-vip-crown-line"></i> Membership
            </a>
            <a href="{{ route('admin.notifications.index', ['filter' => 'system']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-semibold transition-all whitespace-nowrap flex items-center gap-1.5 {{ $currentFilter === 'system' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i class="ri-settings-3-line"></i> Sistem
            </a>
        </div>
        
        <form action="{{ route('admin.notifications.index') }}" method="GET" class="flex items-center gap-2 mt-2 md:mt-0 p-1 md:w-64">
            <input type="hidden" name="filter" value="{{ $currentFilter }}">
            <div class="relative w-full">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" placeholder="Cari..." value="{{ $currentSearch }}"
                       class="w-full pl-9 pr-4 py-2 bg-slate-900 border border-white/10 rounded-xl text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm">
            </div>
            @if($currentSearch)
                <a href="{{ route('admin.notifications.index', ['filter' => $currentFilter]) }}" class="p-2 bg-slate-800 text-slate-400 hover:text-white rounded-xl">
                    <i class="ri-close-line"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Notifications List -->
    <div class="space-y-4 animate-fade-slide-up" style="animation-delay: 0.3s;">
        @forelse($notifications as $notification)
            @include('admin.components.notificationItem', ['notification' => $notification])
        @empty
            <div class="text-center py-20 glass-card rounded-3xl">
                <i class="ri-mail-check-line text-6xl text-slate-600 mb-4 inline-block"></i>
                <h3 class="text-xl font-bold font-['Plus_Jakarta_Sans'] text-slate-300 mb-2">Semua Bersih!</h3>
                <p class="text-slate-500 font-medium">{{ $currentFilter === 'unread' ? 'Semua notifikasi sudah dibaca.' : 'Belum ada notifikasi untuk ditampilkan.' }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $notifications->appends(['filter' => $currentFilter, 'search' => $currentSearch])->links() }}
        </div>
    @endif
</div>

<!-- Toast -->
<div id="toast" class="fixed top-24 right-6 bg-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl transform translate-x-full transition-transform duration-300 z-50 flex items-center gap-3 border border-emerald-500/50">
    <i class="ri-information-line text-xl"></i>
    <span id="toastMsg" class="font-medium font-['Plus_Jakarta_Sans']"></span>
</div>

<script>
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMsg');
    
    if (type === 'error') {
        toast.className = 'fixed top-24 right-6 bg-red-600 text-white px-6 py-4 rounded-xl shadow-2xl transform translate-x-full transition-transform duration-300 z-50 flex items-center gap-3 border border-red-500/50';
    } else {
        toast.className = 'fixed top-24 right-6 bg-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl transform translate-x-full transition-transform duration-300 z-50 flex items-center gap-3 border border-emerald-500/50';
    }
    toastMsg.textContent = message;
    
    toast.classList.remove('translate-x-full');
    setTimeout(() => {
        toast.classList.add('translate-x-full');
    }, 3000);
}

function toggleRead(notificationId) {
    fetch(`/admin/notifications/${notificationId}/toggle-read`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(r => r.json()).then(data => { if (data.success) location.reload(); })
    .catch(() => showToast('Terjadi kesalahan', 'error'));
}

function deleteNotification(notificationId) {
    if (!confirm('Hapus notifikasi ini?')) return;
    fetch(`/admin/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(r => r.json()).then(data => {
        if (data.success) { showToast(data.message, 'success'); setTimeout(() => location.reload(), 1000); }
    }).catch(() => showToast('Terjadi kesalahan', 'error'));
}

function markAllRead() {
    if (!confirm('Tandai semua dibaca?')) return;
    fetch('{{ route('admin.notifications.markAllRead') }}', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => location.reload()).catch(() => showToast('Terjadi kesalahan', 'error'));
}

function clearRead() {
    if (!confirm('Hapus semua yang sudah dibaca?')) return;
    fetch('{{ route('admin.notifications.clearRead') }}', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => location.reload()).catch(() => showToast('Terjadi kesalahan', 'error'));
}

@if(session('success'))
    showToast('{{ session('success') }}', 'success');
@endif
</script>
@endsection
