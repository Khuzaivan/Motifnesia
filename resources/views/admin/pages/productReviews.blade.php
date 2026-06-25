@extends('admin.layouts.mainLayout')

@section('title', 'Ulasan Produk')

@section('content')
<div x-data="{ currentFilter: 'all', searchQuery: '' }" class="space-y-6">
    {{-- Header & Filters --}}
    <div class="glass-card rounded-2xl p-4 flex flex-col md:flex-row items-center justify-between gap-4 animate-fade-slide-up">
        <h2 class="text-xl font-bold font-['Plus_Jakarta_Sans'] text-white">Ulasan Pelanggan</h2>
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
            <div class="relative w-full sm:w-64 group">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-amber-400 transition-colors"></i>
                <input type="text" 
                       x-model="searchQuery"
                       @input="filterReviews()"
                       placeholder="Cari ulasan atau produk..." 
                       class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-white/10 rounded-xl text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 placeholder-slate-500 transition-all text-sm">
            </div>
            
            <div class="relative w-full sm:w-48">
                <i class="ri-filter-3-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <select x-model="currentFilter"
                        @change="filterReviews()"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-white/10 rounded-xl text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 appearance-none transition-all text-sm">
                    <option value="all" class="bg-slate-800">Semua Bintang</option>
                    <option value="5" class="bg-slate-800">Bintang 5</option>
                    <option value="4" class="bg-slate-800">Bintang 4</option>
                    <option value="3" class="bg-slate-800">Bintang 3</option>
                    <option value="2" class="bg-slate-800">Bintang 2</option>
                    <option value="1" class="bg-slate-800">Bintang 1</option>
                </select>
                <i class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
            </div>
        </div>
    </div>

    {{-- Reviews Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 animate-fade-slide-up" style="animation-delay: 0.1s;">
        @php
            $hasAnyReview = false;
        @endphp

        @foreach($products as $product)
            @if($product['has_reviews'])
                @foreach($product['reviews'] as $review)
                    @php $hasAnyReview = true; @endphp
                    <div class="glass-card rounded-2xl p-6 relative group hover:border-amber-500/50 transition-all hover:scale-[1.02] hover:shadow-xl hover:shadow-amber-500/10 review-card flex flex-col" 
                         data-rating="{{ $review['rating'] }}"
                         data-search="{{ strtolower($review['customer_name'] . ' ' . ($review['comment'] ?? '') . ' ' . $product['nama_produk']) }}">
                        
                        {{-- Review Header: User & Rating --}}
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg border border-white/10 shrink-0">
                                    {{ strtoupper(substr($review['customer_name'] ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-200 text-sm group-hover:text-amber-400 transition-colors">{{ $review['customer_name'] }}</h4>
                                    <p class="text-xs text-slate-500">{{ $review['date'] }}</p>
                                    <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold border
                                        @if(($review['moderation_status'] ?? 'approved') === 'approved') bg-emerald-500/10 text-emerald-400 border-emerald-500/20
                                        @elseif(($review['moderation_status'] ?? 'approved') === 'hidden') bg-slate-500/10 text-slate-400 border-slate-500/20
                                        @else bg-red-500/10 text-red-400 border-red-500/20 @endif">
                                        {{ ucfirst($review['moderation_status'] ?? 'approved') }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-0.5 bg-slate-900/50 px-2 py-1 rounded-lg border border-white/5">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review['rating'])
                                        <i class="ri-star-fill text-amber-500 text-sm drop-shadow-sm"></i>
                                    @else
                                        <i class="ri-star-line text-slate-600 text-sm"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>

                        {{-- Review Comment --}}
                        <div class="flex-1 mb-4">
                            <p class="text-slate-300 text-sm leading-relaxed italic">
                                "{{ $review['comment'] ?? 'Tidak ada teks ulasan yang diberikan.' }}"
                            </p>
                        </div>

                        {{-- Product Info Footer --}}
                        <div class="pt-4 border-t border-white/5 flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0 border border-white/10 bg-slate-900">
                                <img src="{{ $product['gambar_url'] ?? \App\Support\AssetUrl::product($product['gambar'] ?? null) }}" alt="{{ $product['nama_produk'] }}" class="w-full h-full object-cover">
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-xs text-slate-500 font-medium mb-0.5">Produk yang diulas:</p>
                                <p class="font-bold text-slate-200 text-sm line-clamp-1 font-['Plus_Jakarta_Sans']">{{ $product['nama_produk'] }}</p>
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-t border-white/5">
                            <form action="{{ route('admin.reviews.moderate', $review['id']) }}" method="POST" class="space-y-3">
                                @csrf
                                <select name="moderation_status" class="w-full px-3 py-2 bg-slate-900 border border-white/10 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-amber-500">
                                    @foreach(['approved' => 'Tampilkan', 'hidden' => 'Sembunyikan', 'rejected' => 'Tolak'] as $value => $label)
                                        <option value="{{ $value }}" {{ ($review['moderation_status'] ?? 'approved') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="moderation_note" value="{{ $review['moderation_note'] ?? '' }}" placeholder="Catatan admin opsional" class="w-full px-3 py-2 bg-slate-900 border border-white/10 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-amber-500">
                                <button type="submit" class="w-full px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition-colors">
                                    Simpan Moderasi
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif
        @endforeach
    </div>

    {{-- Empty States --}}
    @if(!$hasAnyReview)
        <div class="text-center py-20 glass-card rounded-3xl mt-6 animate-fade-slide-up">
            <i class="ri-star-smile-line text-6xl text-amber-500/50 mb-4 inline-block"></i>
            <h3 class="text-2xl font-bold font-['Plus_Jakarta_Sans'] text-slate-200 mb-2">Belum Ada Ulasan</h3>
            <p class="text-slate-400 font-medium">Belum ada pelanggan yang memberikan ulasan untuk produk Anda.</p>
        </div>
    @endif

    <div id="searchEmptyState" class="hidden text-center py-20 glass-card rounded-3xl mt-6">
        <i class="ri-search-line text-6xl text-slate-600 mb-4 inline-block"></i>
        <h3 class="text-xl font-bold font-['Plus_Jakarta_Sans'] text-slate-300 mb-2">Ulasan Tidak Ditemukan</h3>
        <p class="text-slate-500 font-medium">Tidak ada ulasan yang cocok dengan pencarian atau filter.</p>
    </div>
</div>

@push('scripts')
<script>
    function filterReviews() {
        const component = document.querySelector('[x-data]').__x.$data;
        const searchTerm = component.searchQuery.toLowerCase();
        const filter = component.currentFilter;
        const cards = document.querySelectorAll('.review-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const rating = card.getAttribute('data-rating');
            const searchData = card.getAttribute('data-search');

            const matchesSearch = searchData.includes(searchTerm);
            const matchesFilter = filter === 'all' || rating === filter;

            if (matchesSearch && matchesFilter) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const searchEmptyState = document.getElementById('searchEmptyState');
        if (visibleCount === 0 && cards.length > 0) {
            searchEmptyState.classList.remove('hidden');
        } else {
            searchEmptyState.classList.add('hidden');
        }
    }
</script>
@endpush
@endsection
