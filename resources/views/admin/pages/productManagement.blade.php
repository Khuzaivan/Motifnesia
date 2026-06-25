@extends('admin.layouts.mainLayout')

@section('title', 'Kelola Produk')

@section('content')
@php
    $showcaseProducts = $products->take(3)->values();
    $featuredProduct = $showcaseProducts->first();
    $featuredImage = $featuredProduct?->image_url ?? asset('placeholder_image.jpg');
    $featuredPrice = $featuredProduct ? number_format((float) ($featuredProduct->harga_diskon ?: $featuredProduct->harga), 0, ',', '.') : '0';
    $discountCount = $products->filter(fn ($product) => (int) ($product->diskon_persen ?? 0) > 0)->count();
    $lowStockCount = $products->filter(fn ($product) => (int) ($product->stok ?? 0) < 10)->count();
@endphp

<style>
    .motif-showcase {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        border-radius: 30px;
        min-height: 360px;
        background:
            linear-gradient(135deg, rgba(8, 11, 20, 0.98), rgba(18, 18, 17, 0.96) 48%, rgba(36, 25, 12, 0.92)),
            radial-gradient(circle at 72% 24%, rgba(245, 158, 11, 0.18), transparent 32%),
            radial-gradient(circle at 18% 76%, rgba(201, 168, 76, 0.1), transparent 28%);
        border: 1px solid rgba(245, 158, 11, 0.18);
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.38), inset 0 1px 0 rgba(255, 255, 255, 0.06);
        transform: translateY(18px);
        opacity: 0;
        transition: opacity .8s ease, transform .8s ease;
    }

    .motif-showcase.is-visible {
        transform: translateY(0);
        opacity: 1;
    }

    .motif-showcase::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: -2;
        opacity: .42;
        background-image:
            linear-gradient(45deg, rgba(201, 168, 76, 0.08) 1px, transparent 1px),
            linear-gradient(135deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px),
            repeating-radial-gradient(circle at 50% 50%, transparent 0 16px, rgba(201, 168, 76, 0.055) 17px 18px, transparent 19px 34px);
        background-size: 32px 32px, 28px 28px, 130px 130px;
        transform: translate3d(calc(var(--mx, 0) * -0.12px), calc(var(--my, 0) * -0.1px), 0);
        transition: transform .18s ease-out;
    }

    .motif-showcase::after {
        content: '';
        position: absolute;
        inset: 16px;
        z-index: -1;
        border-radius: 24px;
        border: 1px solid rgba(245, 158, 11, 0.1);
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.04), transparent 42%, rgba(245, 158, 11, 0.05));
        pointer-events: none;
    }

    .motif-showcase__rocks {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: -1;
        transform-style: preserve-3d;
    }

    .motif-showcase__rocks span {
        position: absolute;
        display: block;
        width: 150px;
        height: 92px;
        background: linear-gradient(135deg, rgba(70, 64, 53, .36), rgba(16, 19, 27, .18));
        clip-path: polygon(12% 12%, 72% 0, 100% 34%, 82% 86%, 24% 100%, 0 48%);
        border: 1px solid rgba(255, 255, 255, .06);
        filter: blur(.1px);
        opacity: .58;
        transform: translate3d(calc(var(--mx, 0) * .08px), calc(var(--my, 0) * .08px), 0) rotate(var(--r, -8deg));
    }

    .motif-showcase__rocks span:nth-child(1) { left: -34px; bottom: 22px; --r: -12deg; }
    .motif-showcase__rocks span:nth-child(2) { right: 22%; top: 24px; width: 110px; height: 70px; --r: 18deg; opacity: .36; }
    .motif-showcase__rocks span:nth-child(3) { right: -26px; bottom: 34px; width: 190px; height: 108px; --r: 8deg; opacity: .46; }

    .showcase-kicker {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid rgba(245, 158, 11, .22);
        background: rgba(245, 158, 11, .08);
        color: #f6d56f;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .showcase-kicker span {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: #f6d56f;
        box-shadow: 0 0 18px rgba(246, 213, 111, .75);
    }

    .showcase-stat {
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, .08);
        background: rgba(255, 255, 255, .045);
        padding: 14px 16px;
        backdrop-filter: blur(10px);
    }

    .showcase-device-wrap {
        perspective: 1200px;
        min-height: 330px;
    }

    .showcase-device {
        position: relative;
        transform-style: preserve-3d;
        transform: rotateX(10deg) rotateY(-18deg) translate3d(calc(var(--mx, 0) * .22px), calc(var(--my, 0) * .16px), 0);
        transition: transform .18s ease-out;
        animation: deviceFloat 6s ease-in-out infinite;
    }

    @keyframes deviceFloat {
        0%, 100% { translate: 0 0; }
        50% { translate: 0 -10px; }
    }

    .showcase-device__screen {
        position: relative;
        width: min(100%, 520px);
        margin-left: auto;
        border-radius: 28px;
        padding: 14px;
        background: linear-gradient(145deg, rgba(255,255,255,.18), rgba(255,255,255,.03));
        border: 1px solid rgba(255, 255, 255, .16);
        box-shadow: 0 28px 80px rgba(0, 0, 0, .42), 0 0 52px rgba(201, 168, 76, .16);
    }

    .showcase-device__screen::before {
        content: '';
        position: absolute;
        inset: 10px;
        border-radius: 22px;
        border: 1px solid rgba(245, 158, 11, .16);
        pointer-events: none;
    }

    .showcase-ui {
        overflow: hidden;
        border-radius: 20px;
        background: #111315;
        border: 1px solid rgba(255,255,255,.08);
    }

    .showcase-ui__topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        background: rgba(255, 255, 255, .04);
        border-bottom: 1px solid rgba(255, 255, 255, .06);
    }

    .showcase-ui__dots {
        display: flex;
        gap: 6px;
    }

    .showcase-ui__dots span {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .24);
    }

    .showcase-ui__search {
        flex: 1;
        max-width: 190px;
        height: 8px;
        border-radius: 999px;
        background: linear-gradient(90deg, rgba(201,168,76,.52), rgba(255,255,255,.12));
    }

    .showcase-main-product {
        display: grid;
        grid-template-columns: 1fr 1.1fr;
        gap: 16px;
        padding: 18px;
    }

    .showcase-main-product img {
        width: 100%;
        aspect-ratio: 4 / 5;
        object-fit: cover;
        object-position: top;
        border-radius: 16px;
        background: rgba(255,255,255,.04);
        box-shadow: 0 12px 32px rgba(0,0,0,.28);
    }

    .showcase-lines span {
        display: block;
        height: 10px;
        margin-bottom: 10px;
        border-radius: 999px;
        background: rgba(255,255,255,.1);
    }

    .showcase-lines span:nth-child(1) { width: 82%; background: rgba(255,255,255,.2); }
    .showcase-lines span:nth-child(2) { width: 60%; }
    .showcase-lines span:nth-child(3) { width: 72%; background: rgba(201,168,76,.28); }

    .showcase-mini-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        padding: 0 18px 18px;
    }

    .showcase-mini-card {
        border-radius: 14px;
        padding: 8px;
        background: rgba(255,255,255,.045);
        border: 1px solid rgba(255,255,255,.07);
    }

    .showcase-mini-card img {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        object-position: top;
        border-radius: 10px;
        background: rgba(255,255,255,.04);
    }

    .showcase-floating-product {
        position: absolute;
        right: -18px;
        bottom: -28px;
        width: 132px;
        padding: 10px;
        border-radius: 22px;
        background: rgba(18, 18, 17, .84);
        border: 1px solid rgba(245, 158, 11, .24);
        box-shadow: 0 22px 60px rgba(0,0,0,.42), 0 0 34px rgba(201,168,76,.18);
        transform: translateZ(70px) rotateY(18deg);
    }

    .showcase-floating-product img {
        width: 100%;
        aspect-ratio: 1 / 1.18;
        object-fit: cover;
        object-position: top;
        border-radius: 16px;
    }

    .showcase-gold-line {
        width: 64px;
        height: 2px;
        border-radius: 99px;
        background: linear-gradient(90deg, #f6d56f, transparent);
        box-shadow: 0 0 18px rgba(246, 213, 111, .56);
    }

    .light-mode .motif-showcase {
        background:
            linear-gradient(135deg, rgba(15, 23, 42, 0.98), rgba(24, 24, 24, 0.96) 55%, rgba(62, 38, 12, 0.9)),
            radial-gradient(circle at 72% 24%, rgba(245, 158, 11, 0.18), transparent 32%);
        color: #fff;
    }

    .light-mode .motif-showcase .text-white { color: #fff !important; }
    .light-mode .motif-showcase .text-slate-300,
    .light-mode .motif-showcase .text-slate-300\/90 { color: #cbd5e1 !important; }
    .light-mode .motif-showcase .text-slate-400 { color: #94a3b8 !important; }

    @media (max-width: 1024px) {
        .showcase-device {
            transform: rotateX(7deg) rotateY(-8deg);
        }
        .showcase-device__screen {
            margin: 0 auto;
        }
    }

    @media (max-width: 640px) {
        .motif-showcase {
            border-radius: 24px;
            min-height: auto;
        }
        .showcase-main-product {
            grid-template-columns: 1fr;
        }
        .showcase-floating-product {
            display: none;
        }
        .showcase-mini-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .motif-showcase,
        .showcase-device {
            animation: none;
            transition: none;
            transform: none;
        }
    }
</style>

<div x-data="{ currentFilter: 'semua', searchQuery: '' }">
    {{-- Success Alert --}}
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 mb-6 rounded-xl shadow-lg flex items-center gap-3 animate-fade-slide-up">
            <i class="ri-checkbox-circle-fill text-2xl"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Cinematic 3D Showcase --}}
    <section class="motif-showcase mb-8" id="motifDashboardShowcase" aria-label="Showcase dashboard Motifnesia">
        <div class="motif-showcase__rocks" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[0.92fr_1.08fr] gap-8 items-center p-6 md:p-8 xl:p-10">
            <div class="relative z-10">
                <div class="showcase-kicker mb-5"><span></span>3D Website Mockup Showcase</div>
                <h2 class="text-3xl md:text-4xl xl:text-5xl font-extrabold text-white leading-tight font-['Plus_Jakarta_Sans'] tracking-tight">
                    Kelola etalase batik dengan visual premium yang terasa hidup.
                </h2>
                <p class="mt-4 text-sm md:text-base text-slate-300/90 leading-relaxed max-w-xl">
                    Dashboard produk Motifnesia dibuat untuk melihat koleksi, promo, stok, dan tampilan katalog dalam satu preview sinematik.
                </p>

                <div class="showcase-gold-line mt-6 mb-6"></div>

                <div class="grid grid-cols-3 gap-3 max-w-xl">
                    <div class="showcase-stat">
                        <div class="text-2xl font-black text-amber-300">{{ $products->count() }}</div>
                        <div class="text-[11px] uppercase tracking-[0.12em] text-slate-400 font-bold mt-1">Produk</div>
                    </div>
                    <div class="showcase-stat">
                        <div class="text-2xl font-black text-amber-300">{{ $discountCount }}</div>
                        <div class="text-[11px] uppercase tracking-[0.12em] text-slate-400 font-bold mt-1">Promo</div>
                    </div>
                    <div class="showcase-stat">
                        <div class="text-2xl font-black text-amber-300">{{ $lowStockCount }}</div>
                        <div class="text-[11px] uppercase tracking-[0.12em] text-slate-400 font-bold mt-1">Stok Rendah</div>
                    </div>
                </div>
            </div>

            <div class="showcase-device-wrap relative z-10 flex items-center justify-center">
                <div class="showcase-device" id="motifShowcaseDevice">
                    <div class="showcase-device__screen">
                        <div class="showcase-ui">
                            <div class="showcase-ui__topbar">
                                <div class="showcase-ui__dots"><span></span><span></span><span></span></div>
                                <div class="showcase-ui__search"></div>
                                <div class="text-[10px] text-amber-200 font-black tracking-[0.18em] uppercase">Motifnesia</div>
                            </div>

                            <div class="showcase-main-product">
                                <img src="{{ $featuredImage }}" alt="{{ $featuredProduct->nama_produk ?? 'Preview Produk Motifnesia' }}">
                                <div class="flex flex-col justify-center min-w-0">
                                    <div class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-300 mb-3">Featured Product</div>
                                    <h3 class="text-white text-xl md:text-2xl font-extrabold leading-tight line-clamp-2">
                                        {{ $featuredProduct->nama_produk ?? 'Koleksi Batik Premium' }}
                                    </h3>
                                    <p class="mt-3 text-amber-200 font-black">Rp {{ $featuredPrice }}</p>
                                    <div class="showcase-lines mt-5">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="showcase-mini-grid">
                                @forelse($showcaseProducts as $product)
                                    <div class="showcase-mini-card">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->nama_produk }}">
                                    </div>
                                @empty
                                    @for($i = 0; $i < 3; $i++)
                                        <div class="showcase-mini-card">
                                            <img src="{{ asset('placeholder_image.jpg') }}" alt="Preview Motifnesia">
                                        </div>
                                    @endfor
                                @endforelse
                            </div>
                        </div>

                        <div class="showcase-floating-product">
                            <img src="{{ $featuredImage }}" alt="Floating preview produk">
                            <div class="mt-2 h-2 rounded-full bg-amber-300/50"></div>
                            <div class="mt-2 h-2 w-2/3 rounded-full bg-white/20"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Toolbar --}}
    <div class="mb-8">
        <div class="glass-card rounded-2xl p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            
            {{-- Tabs / Filter --}}
            <div class="flex bg-slate-900/50 p-1 rounded-xl w-full md:w-auto">
                <button @click="currentFilter = 'semua'; filterProducts()" 
                        :class="currentFilter === 'semua' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-white/5'"
                        class="px-4 py-2 rounded-lg text-sm font-semibold transition-all w-1/3 md:w-auto">Semua</button>
                <button @click="currentFilter = 'diskon'; filterProducts()" 
                        :class="currentFilter === 'diskon' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-white/5'"
                        class="px-4 py-2 rounded-lg text-sm font-semibold transition-all w-1/3 md:w-auto">Diskon</button>
                <button @click="currentFilter = 'stok_rendah'; filterProducts()" 
                        :class="currentFilter === 'stok_rendah' ? 'bg-red-500 text-white shadow-md' : 'text-slate-400 hover:text-white hover:bg-white/5'"
                        class="px-4 py-2 rounded-lg text-sm font-semibold transition-all w-1/3 md:w-auto flex items-center justify-center gap-2">
                        Stok Rendah <span class="hidden md:inline">(&lt;10)</span>
                </button>
            </div>

            {{-- Search & Add --}}
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <input type="text" 
                           id="searchProduct"
                           x-model="searchQuery"
                           @input="filterProducts()"
                           placeholder="Cari nama / harga..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-800 border border-white/10 rounded-xl text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 placeholder-slate-500 transition-all text-sm">
                    <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                </div>
                @can('is-owner')
                <a href="{{ route('admin.products.create') }}" 
                   class="flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 hover:scale-[1.02] text-white font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-amber-500/20 transition-all duration-200 whitespace-nowrap text-sm">
                    <i class="ri-add-line text-lg"></i>
                    <span class="hidden sm:inline">Tambah Produk</span>
                </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="productGrid">
        @foreach($products as $product)
            @include('admin.components.productCardManagement', ['product' => $product])
        @endforeach
    </div>

    {{-- Empty State --}}
    <div id="emptyState" class="hidden text-center py-20 glass-card rounded-3xl mt-6">
        <i class="ri-inbox-archive-line text-6xl text-slate-600 mb-4 inline-block"></i>
        <h3 class="text-xl font-bold font-['Plus_Jakarta_Sans'] text-slate-300 mb-2">Produk Tidak Ditemukan</h3>
        <p class="text-slate-500 mb-6 font-medium">Tidak ada produk yang cocok dengan pencarian atau filter.</p>
        <button @click="currentFilter = 'semua'; searchQuery = ''; filterProducts()" class="bg-white/10 hover:bg-white/20 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">Reset Filter</button>
    </div>

    @if($products->isEmpty())
        <div class="text-center py-20 glass-card rounded-3xl mt-6">
            <i class="ri-shopping-bag-3-line text-6xl text-amber-500/50 mb-4 inline-block"></i>
            <h3 class="text-2xl font-bold font-['Plus_Jakarta_Sans'] text-slate-200 mb-2">Belum Ada Produk</h3>
            <p class="text-slate-400 mb-6 font-medium">Mulai tambahkan koleksi produk batik Anda sekarang!</p>
            @can('is-owner')
            <a href="{{ route('admin.products.create') }}" 
               class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:scale-[1.02] text-white font-bold px-8 py-3.5 rounded-xl shadow-xl shadow-amber-500/20 transition-all">
                <i class="ri-add-line text-xl"></i> Tambah Produk Pertama
            </a>
            @endcan
        </div>
    @endif
</div>
    
{{-- Modal Edit Produk (Dark Theme Updated) --}}
<div id="editProductModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-slate-800 border border-white/10 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col">
        {{-- Header --}}
        <div class="bg-slate-900/50 px-6 py-5 flex items-center justify-between border-b border-white/5">
            <h2 class="text-xl font-bold font-['Plus_Jakarta_Sans'] text-white flex items-center gap-2">
                <i class="ri-edit-box-line text-amber-500"></i> Edit Produk
            </h2>
            <button id="modalClose" class="text-slate-400 hover:text-white hover:bg-white/10 rounded-lg p-2 transition-colors">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
            <form id="editProductForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" id="edit_product_id" name="product_id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Nama Produk --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Nama Produk</label>
                        <input type="text" id="edit_name" name="name" 
                               class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                    </div>

                    {{-- Harga --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Harga Asli (Rp)</label>
                        <input type="number" id="edit_price" name="price" step="0.01" min="0"
                               class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                    </div>

                    {{-- Stok --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Total Stok</label>
                        <input type="number" id="edit_stock" name="stock" 
                               readonly
                               class="w-full px-4 py-3 bg-slate-900/70 border border-white/10 rounded-xl text-slate-300 focus:outline-none cursor-not-allowed">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Stok Per Ukuran</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach(['S','M','L','XL'] as $size)
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">{{ $size }}</label>
                                    <input type="number" class="edit-size-stock w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                           name="size_stocks[{{ $size }}]" data-size="{{ $size }}" min="0" value="0">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Diskon --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Diskon (%)</label>
                        <div class="relative">
                            <input type="number" id="edit_diskon_persen" name="diskon_persen" step="1" min="0" max="100"
                                   class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                            <i class="ri-percent-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                        </div>
                    </div>

                    {{-- Total Harga --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Total Harga Setelah Diskon</label>
                        <input type="text" id="edit_total_harga_display" readonly
                               class="w-full px-4 py-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-amber-400 font-bold focus:outline-none cursor-not-allowed">
                    </div>

                    {{-- Material --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Material</label>
                        <select id="edit_material" name="material" 
                                class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors appearance-none">
                            <option value="" class="bg-slate-800">-- Pilih Material --</option>
                            <option value="Katun" class="bg-slate-800">Katun</option>
                            <option value="Sutra" class="bg-slate-800">Sutra</option>
                        </select>
                    </div>

                    {{-- Proses --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Proses</label>
                        <select id="edit_process" name="process" 
                                class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors appearance-none">
                            <option value="" class="bg-slate-800">-- Pilih Proses --</option>
                            <option value="Press" class="bg-slate-800">Press</option>
                            <option value="Tulis" class="bg-slate-800">Tulis</option>
                        </select>
                    </div>

                    {{-- Kategori & Ukuran --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Kategori</label>
                        <select id="edit_category" name="category" 
                                class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors appearance-none">
                            <option value="" class="bg-slate-800">-- Pilih Kategori --</option>
                            <option value="Pria" class="bg-slate-800">Pria</option>
                            <option value="Wanita" class="bg-slate-800">Wanita</option>
                            <option value="Anak-anak" class="bg-slate-800">Anak-anak</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Ukuran</label>
                        <select id="edit_ukuran" name="ukuran" 
                                class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors appearance-none">
                            <option value="" class="bg-slate-800">-- Pilih Ukuran --</option>
                            <option value="S" class="bg-slate-800">S</option>
                            <option value="M" class="bg-slate-800">M</option>
                            <option value="L" class="bg-slate-800">L</option>
                            <option value="XL" class="bg-slate-800">XL</option>
                        </select>
                    </div>

                    {{-- SKU & Jenis Lengan --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">SKU</label>
                        <input type="text" id="edit_sku" name="sku" 
                               class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Jenis Lengan</label>
                        <select id="edit_jenis_lengan" name="jenis_lengan" 
                                class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors appearance-none">
                            <option value="" class="bg-slate-800">-- Pilih Jenis Lengan --</option>
                            <option value="Pendek" class="bg-slate-800">Pendek</option>
                            <option value="Panjang" class="bg-slate-800">Panjang</option>
                        </select>
                    </div>

                    {{-- Tags & Gambar --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Tags</label>
                        <input type="text" id="edit_tags" name="tags" placeholder="Pisahkan dengan koma"
                               class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Ganti Gambar</label>
                        <input type="file" id="edit_image" name="image" 
                               class="w-full px-4 py-2.5 bg-slate-900/50 border border-white/10 rounded-xl text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-500/20 file:text-amber-400 hover:file:bg-amber-500/30 transition-colors">
                    </div>

                    {{-- Deskripsi --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Deskripsi</label>
                        <textarea id="edit_description" name="description" rows="3" 
                                  class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors resize-none custom-scrollbar"></textarea>
                    </div>

                    {{-- Filosofi Motif --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Filosofi & Cerita Motif</label>
                        <textarea id="edit_filosofi_motif" name="filosofi_motif" rows="3" 
                                  class="w-full px-4 py-3 bg-slate-900/50 border border-white/10 rounded-xl text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors resize-none custom-scrollbar"
                                  placeholder="Ceritakan filosofi atau kisah di balik motif batik ini (opsional)..."></textarea>
                    </div>

                    <label class="md:col-span-2 flex items-start gap-3 rounded-xl border border-amber-500/20 bg-amber-500/10 p-4 cursor-pointer">
                        <input type="checkbox" id="edit_notify_members" name="notify_members" value="1" class="mt-1 h-4 w-4 rounded border-amber-400 text-amber-500 focus:ring-amber-500">
                        <span>
                            <span class="block text-sm font-semibold text-amber-300">Kirim promo/update ke member</span>
                            <span class="block text-xs text-slate-400 mt-1">Member aktif akan menerima notifikasi internal tentang update produk ini.</span>
                        </span>
                    </label>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="bg-slate-900/50 px-6 py-4 flex items-center justify-between border-t border-white/5">
            <button type="button" id="deleteProductBtn" 
                    class="flex items-center gap-2 bg-red-600/10 hover:bg-red-600/20 text-red-500 hover:text-red-400 border border-red-500/20 font-semibold px-6 py-2.5 rounded-xl transition-all duration-200">
                <i class="ri-delete-bin-line"></i> Hapus
            </button>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('modalClose').click()"
                        class="bg-white/5 hover:bg-white/10 text-white font-semibold px-6 py-2.5 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="button" id="saveChangesBtn" 
                        class="flex items-center gap-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-all duration-200 shadow-lg shadow-amber-500/20 hover:scale-[1.02]">
                    <i class="ri-save-line"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Filtering Logic mapped to AlpineJS x-data context globally accessible via filterProducts()
    function filterProducts() {
        const component = document.querySelector('[x-data]').__x.$data;
        const term = component.searchQuery.toLowerCase();
        const filter = component.currentFilter;
        const cards = document.querySelectorAll('.product-card-item');
        let visibleCount = 0;

        cards.forEach(card => {
            const product = JSON.parse(card.getAttribute('data-product'));
            const nama = (product.nama_produk || '').toLowerCase();
            const harga = (product.harga || 0).toString();
            const diskon = product.diskon_persen || 0;
            const stok = product.stok || 0;

            const matchesSearch = nama.includes(term) || harga.includes(term);
            let matchesTab = true;

            if (filter === 'diskon') matchesTab = diskon > 0;
            if (filter === 'stok_rendah') matchesTab = stok < 10;

            if (matchesSearch && matchesTab) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        document.getElementById('emptyState').style.display = visibleCount === 0 && cards.length > 0 ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const showcase = document.getElementById('motifDashboardShowcase');
        const showcaseDevice = document.getElementById('motifShowcaseDevice');

        if (showcase) {
            const reveal = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        reveal.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            reveal.observe(showcase);

            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                showcase.addEventListener('pointermove', function (event) {
                    const rect = showcase.getBoundingClientRect();
                    const x = event.clientX - rect.left - rect.width / 2;
                    const y = event.clientY - rect.top - rect.height / 2;

                    showcase.style.setProperty('--mx', (x / rect.width * 100).toFixed(2));
                    showcase.style.setProperty('--my', (y / rect.height * 100).toFixed(2));
                });

                showcase.addEventListener('pointerleave', function () {
                    showcase.style.setProperty('--mx', '0');
                    showcase.style.setProperty('--my', '0');
                    if (showcaseDevice) {
                        showcaseDevice.style.transform = '';
                    }
                });
            }
        }

        const modal = document.getElementById('editProductModal');
        const modalClose = document.getElementById('modalClose');
        const editForm = document.getElementById('editProductForm');
        const saveBtn = document.getElementById('saveChangesBtn');
        const deleteBtn = document.getElementById('deleteProductBtn');
        const editSizeStockInputs = document.querySelectorAll('.edit-size-stock');

        function updateEditTotalStock() {
            let total = 0;
            editSizeStockInputs.forEach(input => total += parseInt(input.value || '0', 10));
            document.getElementById('edit_stock').value = total;
        }

        function openModalWithProduct(product) {
            document.getElementById('edit_product_id').value = product.id;
            document.getElementById('edit_name').value = product.nama_produk || '';
            document.getElementById('edit_price').value = product.harga || '';
            document.getElementById('edit_material').value = product.material || '';
            document.getElementById('edit_process').value = product.proses || '';
            document.getElementById('edit_sku').value = product.sku || '';
            document.getElementById('edit_category').value = product.kategori || '';
            document.getElementById('edit_tags').value = product.tags || '';
            document.getElementById('edit_description').value = product.deskripsi || '';
            document.getElementById('edit_filosofi_motif').value = product.filosofi_motif || '';
            document.getElementById('edit_ukuran').value = product.ukuran || '';
            document.getElementById('edit_jenis_lengan').value = product.jenis_lengan || '';
            document.getElementById('edit_stock').value = product.stok || '';
            editSizeStockInputs.forEach(input => {
                const size = input.getAttribute('data-size');
                input.value = (product.size_stocks && product.size_stocks[size]) ? product.size_stocks[size] : 0;
            });
            document.getElementById('edit_diskon_persen').value = product.diskon_persen || 0;
            document.getElementById('edit_notify_members').checked = false;
            modal.style.display = 'flex';
            updateEditTotalStock();
            calculateEditTotalHarga();
        }

        function calculateEditTotalHarga() {
            const harga = parseFloat(document.getElementById('edit_price').value) || 0;
            const diskon = parseFloat(document.getElementById('edit_diskon_persen').value) || 0;
            const totalHarga = harga - (harga * (diskon / 100));
            document.getElementById('edit_total_harga_display').value = 'Rp ' + totalHarga.toLocaleString('id-ID');
        }

        document.getElementById('edit_price').addEventListener('input', calculateEditTotalHarga);
        editSizeStockInputs.forEach(input => input.addEventListener('input', updateEditTotalStock));
        document.getElementById('edit_diskon_persen').addEventListener('input', function() {
            const val = parseFloat(this.value) || 0;
            if (val < 0) this.value = 0;
            if (val > 100) this.value = 100;
            calculateEditTotalHarga();
        });

        modalClose.addEventListener('click', function () { modal.style.display = 'none'; });

        document.querySelectorAll('.edit-button').forEach(btn => {
            btn.addEventListener('click', function () {
                const card = btn.closest('.product-card-item');
                const product = JSON.parse(card.getAttribute('data-product'));
                openModalWithProduct(product);
            });
        });

        deleteBtn.addEventListener('click', function () {
            const id = document.getElementById('edit_product_id').value;
            if (!confirm('Yakin hapus produk ini?')) return;
            fetch(`{{ url('admin/products') }}/${id}/delete`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
            }).then(r => r.json()).then(res => {
                if (res.success) location.reload();
                else alert('Gagal menghapus');
            }).catch(()=> alert('Gagal menghapus'));
        });

        saveBtn.addEventListener('click', function () {
            const id = document.getElementById('edit_product_id').value;
            const fd = new FormData(editForm);
            fetch(`{{ url('admin/products') }}/${id}/update`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
                body: fd
            }).then(r => r.json()).then(res => {
                if (res.success) location.reload();
                else alert('Gagal menyimpan');
            }).catch(()=> alert('Gagal menyimpan'));
        });

        document.querySelectorAll('.delete-button').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = btn.getAttribute('data-id');
                if (!confirm('Yakin hapus produk ini?')) return;
                fetch(`{{ url('admin/products') }}/${id}/delete`, { 
                    method: 'POST', 
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' } 
                }).then(r => r.json()).then(res => {
                    if (res.success) location.reload();
                    else alert('Gagal menghapus');
                }).catch(()=> alert('Gagal menghapus'));
            });
        });
    });
</script>
@endpush
@endsection
