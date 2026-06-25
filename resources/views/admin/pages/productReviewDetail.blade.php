@extends('admin.layouts.mainLayout')

@section('title', 'Detail Ulasan Produk')

@section('content')
    <div class="p-6">
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('admin.reviews.index') }}" 
               class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Ulasan
            </a>
        </div>

        {{-- Product Info Card --}}
        <div class="glass-card rounded-2xl p-6 mb-6">
            <div class="flex items-start gap-6">
                <img src="{{ $product['gambar_url'] ?? \App\Support\AssetUrl::product($product['gambar'] ?? null) }}" 
                     alt="{{ $product['nama_produk'] }}" 
                     class="w-32 h-32 object-cover rounded-lg border-2 border-white/10">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-white mb-2">{{ $product['nama_produk'] }}</h1>
                    <p class="text-slate-400 mb-4">SKU: {{ $product['sku'] ?? 'N/A' }}</p>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-6 h-6 {{ $i <= floor($product['average_rating']) ? 'text-amber-400' : 'text-slate-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-2xl font-bold text-white">{{ number_format($product['average_rating'], 1) }}</span>
                        </div>
                        <span class="px-4 py-2 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-full text-sm font-semibold">
                            {{ $product['total_reviews'] }} Ulasan
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reviews List --}}
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 px-6 py-4 border-b border-white/5">
                <h2 class="text-xl font-bold text-white">Semua Ulasan</h2>
            </div>

            <div class="divide-y divide-white/5">
                @forelse($product['reviews'] as $review)
                    <div class="p-6 hover:bg-white/5 transition-colors duration-150">
                        <div class="flex items-start gap-4">
                            {{-- Avatar --}}
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-500 rounded-full flex items-center justify-center text-white font-semibold text-lg shadow-md flex-shrink-0">
                                {{ strtoupper(substr($review['customer_name'], 0, 1)) }}
                            </div>

                            {{-- Review Content --}}
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <h3 class="font-semibold text-white">{{ $review['customer_name'] }}</h3>
                                        <p class="text-sm text-slate-400">{{ $review['customer_email'] }}</p>
                                        <span class="inline-flex mt-1 px-2.5 py-0.5 rounded-full text-xs font-bold
                                            @if(($review['moderation_status'] ?? 'approved') === 'approved') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                            @elseif(($review['moderation_status'] ?? 'approved') === 'hidden') bg-slate-800 text-slate-400 border border-white/5
                                            @else bg-red-500/10 text-red-400 border border-red-500/20 @endif">
                                            {{ ucfirst($review['moderation_status'] ?? 'approved') }}
                                        </span>
                                    </div>
                                    <span class="text-sm text-slate-400">{{ $review['date'] }}</span>
                                </div>

                                {{-- Rating Stars --}}
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= $review['rating'] ? 'text-amber-400' : 'text-slate-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-sm font-semibold text-slate-300">{{ $review['rating'] }}.0</span>
                                </div>

                                {{-- Comment --}}
                                <p class="text-slate-300 leading-relaxed mb-3">{{ $review['comment'] }}</p>

                                {{-- Order Number --}}
                                <div class="flex items-center gap-2 text-sm text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                    <span>Order: {{ $review['order_number'] }}</span>
                                </div>

                                <form action="{{ route('admin.reviews.moderate', $review['id']) }}" method="POST" class="mt-4 grid grid-cols-1 md:grid-cols-[180px_1fr_auto] gap-3">
                                    @csrf
                                    <select name="moderation_status" class="px-3 py-2 border border-white/10 rounded-lg text-sm bg-slate-900 text-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                        @foreach(['approved' => 'Tampilkan', 'hidden' => 'Sembunyikan', 'rejected' => 'Tolak'] as $value => $label)
                                            <option value="{{ $value }}" {{ ($review['moderation_status'] ?? 'approved') === $value ? 'selected' : '' }} class="bg-slate-900">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="moderation_note" value="{{ $review['moderation_note'] ?? '' }}" placeholder="Catatan admin opsional" class="px-3 py-2 border border-white/10 rounded-lg text-sm bg-slate-900 text-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    <button type="submit" class="btn-magnetic px-5 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-sm font-bold shadow-md hover:shadow-lg transition-all">Simpan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20">
                        <div class="text-6xl mb-4">💬</div>
                        <h3 class="text-xl font-semibold text-white mb-2">Belum Ada Ulasan</h3>
                        <p class="text-slate-400">Produk ini belum memiliki ulasan dari pelanggan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
