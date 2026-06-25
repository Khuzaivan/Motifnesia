@props(['product'])

@php
    $gambar = data_get($product, 'gambar', data_get($product, 'image', null));
    $defaultPath = 'assets/photoProduct/default_batik.svg';
    if (!$gambar || !file_exists(public_path($gambar))) {
        $gambar = $defaultPath;
    }
    $terjual = data_get($product, 'terjual', 0);
    $stok = data_get($product, 'stok', 0);
    $name = data_get($product, 'nama_produk', data_get($product, 'name', 'Produk'));
    $harga = data_get($product, 'harga', 0);
    $id = data_get($product, 'id');
    $diskonPersen = data_get($product, 'diskon_persen', 0);
    $hargaDiskon = data_get($product, 'harga_diskon', $harga);
    $sizeStocks = collect(data_get($product, 'sizeStocks', []))
        ->mapWithKeys(fn ($stock) => [data_get($stock, 'ukuran') => (int) data_get($stock, 'stok', 0)])
        ->filter(fn ($qty, $size) => filled($size))
        ->all();
    $productJson = json_encode([
        'id' => $id,
        'nama_produk' => $name,
        'gambar' => $gambar,
        'terjual' => $terjual,
        'stok' => $stok,
        'harga' => $harga,
        'diskon_persen' => $diskonPersen,
        'harga_diskon' => $hargaDiskon,
        'material' => data_get($product, 'material'),
        'proses' => data_get($product, 'proses'),
        'sku' => data_get($product, 'sku'),
        'kategori' => data_get($product, 'kategori'),
        'tags' => data_get($product, 'tags'),
        'deskripsi' => data_get($product, 'deskripsi'),
        'jenis_lengan' => data_get($product, 'jenis_lengan'),
        'ukuran' => data_get($product, 'ukuran'),
        'filosofi_motif' => data_get($product, 'filosofi_motif'),
        'size_stocks' => $sizeStocks,
    ]);
@endphp

<div class="bg-slate-800 rounded-2xl shadow-lg hover:shadow-amber-500/20 hover:scale-[1.02] transition-all duration-300 overflow-hidden border border-white/10 group product-card-item relative" 
     data-product='{{ $productJson }}'>
    {{-- Product Image --}}
    <div class="relative overflow-hidden aspect-square bg-slate-900">
        <img src="{{ \App\Support\AssetUrl::product($gambar) }}" 
             alt="{{ $name }}" 
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
        
        {{-- Discount Badge --}}
        @if($diskonPersen > 0)
        <div class="absolute top-3 left-3 px-2.5 py-1 bg-orange-500 text-white text-[10px] font-bold rounded-lg shadow-lg z-10 uppercase tracking-wide border border-white/20">
            -{{ $diskonPersen }}%
        </div>
        @endif

        {{-- Stock Badge --}}
        <div class="absolute top-3 right-3 z-10">
            @if($stok > 20)
                <span class="px-2.5 py-1 bg-emerald-500 text-white text-[10px] font-bold rounded-lg shadow-lg border border-white/20 flex items-center gap-1">
                    <i class="ri-checkbox-circle-line"></i> Stok: {{ $stok }}
                </span>
            @elseif($stok >= 10)
                <span class="px-2.5 py-1 bg-amber-500 text-white text-[10px] font-bold rounded-lg shadow-lg border border-white/20 flex items-center gap-1">
                    <i class="ri-error-warning-line"></i> Stok: {{ $stok }}
                </span>
            @else
                <span class="px-2.5 py-1 bg-red-500 text-white text-[10px] font-bold rounded-lg shadow-lg border border-white/20 flex items-center gap-1 animate-pulse">
                    <i class="ri-alarm-warning-line"></i> Kritis: {{ $stok }}
                </span>
            @endif
        </div>
        
        {{-- Hover overlay gradient --}}
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
    </div>

    {{-- Product Info --}}
    <div class="p-5">
        <h3 class="font-semibold text-white font-['Plus_Jakarta_Sans'] text-base mb-1 line-clamp-2 min-h-[3rem] group-hover:text-amber-400 transition-colors" title="{{ $name }}">{{ $name }}</h3>
        
        <div class="mb-5">
            <div class="flex items-center gap-2">
                <span class="text-xl font-bold text-amber-400">
                    Rp {{ number_format($hargaDiskon, 0, ',', '.') }}
                </span>
                @if($diskonPersen > 0)
                    <span class="text-xs text-slate-500 line-through decoration-slate-500 font-semibold">
                        Rp {{ number_format($harga, 0, ',', '.') }}
                    </span>
                @endif
            </div>
            <span class="text-sm text-slate-400 flex items-center gap-1.5 mt-1 font-medium">
                <i class="ri-eye-line text-slate-500"></i> {{ $terjual }} Terjual
            </span>
        </div>

        {{-- Action Buttons --}}
        @can('is-owner')
        <div class="flex gap-3">
            <button class="edit-button flex-1 bg-blue-600 hover:bg-blue-500 hover:brightness-110 hover:scale-[1.02] text-white font-semibold py-2.5 px-4 rounded-xl transition-all duration-200 shadow-lg shadow-blue-500/20 flex items-center justify-center gap-2 text-sm" 
                    data-id="{{ $id }}">
                <i class="ri-edit-line text-lg"></i> Edit
            </button>
            <button class="delete-button bg-red-600 hover:bg-red-500 hover:brightness-110 hover:scale-[1.02] text-white font-semibold py-2.5 px-4 rounded-xl transition-all duration-200 shadow-lg shadow-red-500/20 flex items-center justify-center gap-2 text-sm" 
                    data-id="{{ $id }}">
                <i class="ri-delete-bin-line text-lg"></i>
            </button>
        </div>
        @endcan
    </div>
</div>
