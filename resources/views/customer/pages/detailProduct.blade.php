@extends('customer.layouts.mainLayout')

@section('container')
<style>
    .related-products-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
        align-items: stretch;
    }

    @media (max-width: 1100px) {
        .related-products-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 820px) {
        .related-products-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }
    }

    @media (max-width: 560px) {
        .related-products-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<div style="min-height:100vh;padding-top:88px;padding-bottom:60px;background:#131313;">
    <div style="max-width:1100px;margin:0 auto;padding:0 24px;">

        @php
            $hargaDiskon = $product['harga_diskon'] ?? $product['harga'];
            $diskonPersen = $product['diskon_persen'] ?? 0;
            $sizeStocks = $product['size_stocks'] ?? [];
            $productCode = $product['sku'] ?: 'BTK-' . str_pad((string) $product['id'], 4, '0', STR_PAD_LEFT);
            if (empty($sizeStocks) && (int) ($product['stok'] ?? 0) > 0) {
                $legacySizes = array_filter(array_map('trim', explode(',', $product['ukuran'] ?? 'M')));
                $legacySizes = $legacySizes ?: ['M'];
                foreach ($legacySizes as $legacySize) {
                    $sizeStocks[strtoupper($legacySize)] = (int) $product['stok'];
                }
            }
        @endphp

        {{-- Main Product Area --}}
        <div style="background:#1e1e1e;border:1px solid rgba(255,255,255,.06);border-radius:24px;padding:36px;margin-bottom:20px;display:flex;gap:48px;">

            {{-- Image --}}
            <div style="flex:0 0 45%;">
                <div style="border-radius:20px;overflow:hidden;border:1px solid rgba(255,255,255,.06);">
                    <img src="{{ $product['gambar_url'] ?? \App\Support\AssetUrl::product($product['gambar'] ?? null) }}" alt="{{ $product['nama'] }}"
                         style="width:100%;height:auto;display:block;object-fit:cover;">
                </div>
            </div>

            {{-- Info --}}
            <div style="flex:1;">
                <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;margin-bottom:16px;">
                    <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);border-radius:999px;padding:4px 14px;">
                        <span style="width:6px;height:6px;background:#c9a84c;border-radius:50%;box-shadow:0 0 5px #c9a84c;display:inline-block;"></span>
                        <span style="font-size:.78rem;font-weight:700;color:#c9a84c;letter-spacing:.15em;">BATIK</span>
                    </div>
                    @if(auth()->check() && auth()->user()->isMemberActive())
                        @php
                            $tierInfo = auth()->user()->membership_tier_info;
                            $tierDiscount = $tierInfo['discount'] * 100;
                        @endphp
                        <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.05);border:1px solid {{ $tierInfo['color'] }};border-radius:999px;padding:4px 14px;">
                            <span style="font-size:.78rem;font-weight:700;color:{{ $tierInfo['color'] }};">{{ $tierInfo['badge'] }}</span>
                            @if($tierDiscount > 0)
                                <span style="font-size:.78rem;font-weight:700;color:#22c55e;margin-left:4px;">(Diskon {{ $tierDiscount }}%)</span>
                            @endif
                        </div>
                    @endif
                </div>

                <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#fff;margin-bottom:16px;line-height:1.3;">{{ $product['nama'] }}</h1>

                {{-- Price --}}
                <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:20px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#c9a84c;">Rp{{ number_format($hargaDiskon, 0, ',', '.') }}</span>
                        @if($diskonPersen > 0)
                            <span style="font-size:.9rem;color:rgba(255,255,255,.35);text-decoration:line-through;">Rp{{ number_format($product['harga'], 0, ',', '.') }}</span>
                            <span style="background:linear-gradient(135deg,#c9a84c,#a8832d);color:#111;font-size:.78rem;font-weight:700;padding:3px 10px;border-radius:999px;">-{{ $diskonPersen }}%</span>
                        @endif
                    </div>
                    @if(auth()->check() && auth()->user()->isMemberActive() && auth()->user()->getTierDiscount() > 0)
                        @php
                            $memberDiscounted = $hargaDiskon - ($hargaDiskon * auth()->user()->getTierDiscount());
                        @endphp
                        <div style="font-size:.875rem;color:#86efac;font-weight:600;display:flex;align-items:center;gap:6px;">
                            <span>✨ Harga Member Anda:</span>
                            <span style="font-size:1.1rem;color:#22c55e;font-weight:800;">Rp{{ number_format($memberDiscounted, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                {{-- Attributes --}}
                <div style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:14px;padding:16px;margin-bottom:20px;display:flex;flex-direction:column;gap:10px;">
                    @foreach([
                        ['label'=>'Material','value'=>$product['material'] ?? 'Sutra'],
                        ['label'=>'Proses','value'=>$product['proses'] ?? 'Print'],
                        ['label'=>'Kategori','value'=>ucfirst($product['kategori'] ?? 'Wanita')],
                        ['label'=>'SKU','value'=>$productCode],
                    ] as $attr)
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:.875rem;">
                        <span style="color:rgba(255,255,255,.4);">{{ $attr['label'] }}</span>
                        <span style="color:rgba(255,255,255,.8);font-weight:500;">{{ $attr['value'] }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Size Selection --}}
                <div style="margin-bottom:24px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <p style="color:rgba(255,255,255,.6);font-size:.82rem;font-weight:600;margin:0;letter-spacing:.05em;text-transform:uppercase;">Pilih Ukuran</p>
                        <button type="button" id="btnOpenSizeGuide" style="background:none;border:none;color:#c9a84c;font-size:.8rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:4px;text-decoration:underline;outline:none;">
                            📐 Cari Ukuran Anda
                        </button>
                    </div>
                    <div style="display:flex;gap:10px;">
                        @foreach(['S','M','L','XL'] as $size)
                        @php $sizeStock = (int) ($sizeStocks[$size] ?? 0); @endphp
                        <label style="cursor:pointer;">
                            <input type="radio" name="size" value="{{ $size }}" style="display:none;" class="size-radio" {{ $sizeStock <= 0 ? 'disabled' : '' }}>
                            <span class="size-btn" data-size="{{ $size }}"
                                  data-stock="{{ $sizeStock }}"
                                  style="display:flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:12px;border:1.5px solid {{ $sizeStock > 0 ? 'rgba(255,255,255,.15)' : 'rgba(255,255,255,.06)' }};color:{{ $sizeStock > 0 ? 'rgba(255,255,255,.7)' : 'rgba(255,255,255,.25)' }};font-weight:600;font-size:.9rem;transition:all .2s;cursor:{{ $sizeStock > 0 ? 'pointer' : 'not-allowed' }};position:relative;"
                                  onclick="{{ $sizeStock > 0 ? "selectSize('{$size}')" : '' }}">{{ $size }}</span>
                        </label>
                        @endforeach
                    </div>
                    <p id="size-stock-hint" style="color:rgba(255,255,255,.38);font-size:.78rem;margin-top:8px;">Pilih ukuran untuk melihat stok tersedia.</p>
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:12px;">
                    <button type="button" id="btnAddToCart"
                            style="flex:1;padding:14px;background:linear-gradient(135deg,#c9a84c,#a8832d);border:none;border-radius:14px;color:#111;font-size:.95rem;font-weight:700;cursor:pointer;letter-spacing:.04em;transition:all .25s;"
                            onmouseenter="this.style.opacity='.85';"
                            onmouseleave="this.style.opacity='1';">
                        🛒 Tambahkan ke Keranjang
                    </button>
                    <button type="button" id="btnWhatsAppBuy"
                            style="padding:14px 20px;background:#25D366;border:none;border-radius:14px;color:#fff;font-size:.95rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition:all .25s;"
                            onmouseenter="this.style.opacity='.85';"
                            onmouseleave="this.style.opacity='1';">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.963C16.59 1.981 14.116.957 11.487.956c-5.436 0-9.86 4.37-9.864 9.8.001 2.012.528 3.979 1.529 5.708L2.094 21.91l5.553-1.456c1.609.878 3.256 1.332 4.908 1.334h.01zm9.387-6.39c-.266-.134-1.57-.775-1.814-.863-.243-.089-.42-.134-.596.134-.176.268-.68.864-.834 1.041-.155.178-.309.2-.575.067-.266-.134-1.125-.415-2.143-1.323-.79-.705-1.324-1.576-1.479-1.844-.155-.268-.016-.413.118-.546.121-.12.266-.312.4-.467.133-.156.177-.268.266-.446.089-.178.044-.334-.022-.467-.067-.134-.596-1.439-.817-1.973-.215-.519-.451-.447-.62-.456-.16-.008-.344-.01-.528-.01-.184 0-.485.069-.74.346-.253.277-.97.95-.97 2.317 0 1.367.99 2.686 1.129 2.875.138.188 1.947 2.974 4.717 4.168.659.284 1.173.454 1.573.582.661.21 1.263.18 1.738.11.53-.079 1.57-.641 1.791-1.261.222-.62.222-1.152.155-1.262-.067-.11-.244-.177-.51-.31z"/></svg>
                        Beli via WA
                    </button>
                    <button type="button" id="btnAddToFavorite"
                            style="width:52px;height:52px;border:1.5px solid rgba(255,255,255,.15);border-radius:14px;background:rgba(255,255,255,.03);color:rgba(255,255,255,.6);cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .25s;"
                            onmouseenter="this.style.borderColor='rgba(239,68,68,.5)';this.style.color='#fca5a5';this.style.background='rgba(239,68,68,.1)';"
                            onmouseleave="this.style.borderColor='rgba(255,255,255,.15)';this.style.color='rgba(255,255,255,.6)';this.style.background='rgba(255,255,255,.03)';">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    </button>
                </div>

                <button type="button" id="btnAskProduct"
                        style="margin-top:12px;width:100%;min-height:48px;padding:12px 16px;background:rgba(255,255,255,.04);border:1px solid rgba(201,168,76,.28);border-radius:14px;color:#d7b957;font-size:.9rem;font-weight:800;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:9px;transition:all .2s;"
                        onmouseenter="this.style.background='rgba(201,168,76,.1)';this.style.borderColor='rgba(201,168,76,.48)';"
                        onmouseleave="this.style.background='rgba(255,255,255,.04)';this.style.borderColor='rgba(201,168,76,.28)';">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 11.5c0 4.142-4.03 7.5-9 7.5a10.3 10.3 0 01-3.57-.62L3 20l1.72-4.09A7.02 7.02 0 013 11.5C3 7.358 7.03 4 12 4s9 3.358 9 7.5z"/></svg>
                    Tanya Admin Tentang Produk Ini
                </button>
            </div>
        </div>

        {{-- Description & Storytelling Tab System --}}
        <div style="background:#1e1e1e;border:1px solid rgba(255,255,255,.06);border-radius:24px;padding:32px;margin-bottom:20px;">
            <div style="display:flex;border-bottom:1px solid rgba(255,255,255,.1);margin-bottom:20px;gap:24px;">
                <button type="button" id="tabDescBtn" onclick="switchTab('desc')" style="background:none;border:none;border-bottom:2px solid #c9a84c;color:#fff;padding:10px 0;font-size:1.1rem;font-weight:700;cursor:pointer;transition:all .2s;outline:none;">Deskripsi Produk</button>
                <button type="button" id="tabFiloBtn" onclick="switchTab('filo')" style="background:none;border:none;border-bottom:2px solid transparent;color:rgba(255,255,255,.4);padding:10px 0;font-size:1.1rem;font-weight:700;cursor:pointer;transition:all .2s;outline:none;">Filosofi Motif</button>
            </div>
            
            {{-- Description Content --}}
            <div id="tabDescContent" style="display:block;">
                <p style="color:rgba(255,255,255,.65);line-height:1.8;font-size:.9rem;">{{ $product['deskripsi'] }}</p>
                <p style="color:rgba(255,255,255,.45);line-height:1.8;font-size:.85rem;margin-top:10px;">Koleksi Batik Premium khas daerah Indonesia</p>
            </div>
            
            {{-- Filosofi Content --}}
            <div id="tabFiloContent" style="display:none;">
                @if(!empty($product['filosofi_motif']))
                    <p style="color:rgba(255,255,255,.65);line-height:1.8;font-size:.9rem;white-space:pre-line;">{{ $product['filosofi_motif'] }}</p>
                @else
                    <div style="text-align:center;padding:24px;color:rgba(255,255,255,.3);">
                        <p style="font-size:.9rem;margin:0;">Filosofi motif untuk batik ini sedang dalam proses penulisan oleh kurator kami.</p>
                        <p style="font-size:.8rem;margin-top:6px;color:rgba(201,168,76,.6);">Nantikan cerita menarik di balik keindahan motif batik tulis eksklusif ini.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Reviews --}}
        <div style="background:#1e1e1e;border:1px solid rgba(255,255,255,.06);border-radius:24px;padding:32px;margin-bottom:20px;">
            <h2 style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;color:#fff;margin-bottom:24px;">Ulasan Pelanggan</h2>

            @if($reviews->count() > 0)
                <div id="reviews-container" style="display:flex;flex-direction:column;gap:20px;">
                    @foreach($reviews->take(3) as $review)
                    <div style="padding:20px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:16px;">
                        <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:10px;">
                            <div style="width:40px;height:40px;border-radius:12px;overflow:hidden;flex-shrink:0;border:1px solid rgba(255,255,255,.08);">
                                @if($review->user->profile_pic ?? false)
                                    <img src="{{ $review->user->profile_pic_url }}" alt="{{ $review->user->name }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <div style="width:100%;height:100%;background:linear-gradient(135deg,#c9a84c,#a8832d);display:flex;align-items:center;justify-content:center;color:#111;font-weight:700;font-size:.95rem;">
                                        {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div style="flex:1;">
                                <div style="display:flex;align-items:center;justify-content:space-between;">
                                    <span style="font-weight:700;color:rgba(255,255,255,.9);font-size:.9rem;">{{ $review->user->name ?? 'User' }}</span>
                                    <span style="color:rgba(255,255,255,.3);font-size:.75rem;">{{ $review->created_at->format('d M Y') }}</span>
                                </div>
                                <div style="display:flex;gap:2px;margin-top:4px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span style="color:{{ $i <= $review->rating ? '#c9a84c' : 'rgba(255,255,255,.15)' }};font-size:.82rem;">★</span>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <p style="color:rgba(255,255,255,.6);font-size:.875rem;line-height:1.6;margin-left:52px;">{{ $review->deskripsi_ulasan }}</p>
                    </div>
                    @endforeach

                    @if($reviews->count() > 3)
                        <div id="more-reviews" style="display:none;flex-direction:column;gap:20px;">
                            @foreach($reviews->skip(3) as $review)
                            <div style="padding:20px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:16px;">
                                <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:10px;">
                                    <div style="width:40px;height:40px;border-radius:12px;overflow:hidden;flex-shrink:0;border:1px solid rgba(255,255,255,.08);">
                                        @if($review->user->profile_pic ?? false)
                                            <img src="{{ $review->user->profile_pic_url }}" alt="{{ $review->user->name }}" style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            <div style="width:100%;height:100%;background:linear-gradient(135deg,#c9a84c,#a8832d);display:flex;align-items:center;justify-content:center;color:#111;font-weight:700;">
                                                {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div style="flex:1;">
                                        <div style="display:flex;align-items:center;justify-content:space-between;">
                                            <span style="font-weight:700;color:rgba(255,255,255,.9);font-size:.9rem;">{{ $review->user->name ?? 'User' }}</span>
                                            <span style="color:rgba(255,255,255,.3);font-size:.75rem;">{{ $review->created_at->format('d M Y') }}</span>
                                        </div>
                                        <div style="display:flex;gap:2px;margin-top:4px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span style="color:{{ $i <= $review->rating ? '#c9a84c' : 'rgba(255,255,255,.15)' }};font-size:.82rem;">★</span>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <p style="color:rgba(255,255,255,.6);font-size:.875rem;line-height:1.6;margin-left:52px;">{{ $review->deskripsi_ulasan }}</p>
                            </div>
                            @endforeach
                        </div>

                        <button id="load-more-btn"
                                style="width:100%;padding:13px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.6);border-radius:12px;cursor:pointer;font-size:.875rem;transition:all .2s;"
                                onmouseenter="this.style.borderColor='rgba(201,168,76,.3)';this.style.color='#c9a84c';"
                                onmouseleave="this.style.borderColor='rgba(255,255,255,.1)';this.style.color='rgba(255,255,255,.6)';">
                            Lihat Lebih Banyak ({{ $reviews->count() - 3 }})
                        </button>
                        <button id="show-less-btn" style="display:none;width:100%;padding:13px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.5);border-radius:12px;cursor:pointer;font-size:.875rem;">
                            Tampilkan Lebih Sedikit
                        </button>
                    @endif
                </div>
            @else
                <div style="text-align:center;padding:40px;color:rgba(255,255,255,.3);">
                    <div style="font-size:2.5rem;margin-bottom:12px;">⭐</div>
                    <p>Belum ada ulasan untuk produk ini</p>
                </div>
            @endif
        </div>

        {{-- Related Products --}}
        @if($relatedProducts->count() > 0)
        <div>
            <h2 style="font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;color:#fff;margin-bottom:20px;">Produk Lainnya</h2>
            <div class="related-products-grid">
                @foreach($relatedProducts as $related)
                    @include('customer.components.product-card', ['product' => $related])
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Ask Product Modal --}}
<div id="askProductModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.72);backdrop-filter:blur(8px);z-index:90;align-items:center;justify-content:center;padding:18px;">
    <div style="width:min(520px,100%);background:#1c1c1c;border:1px solid rgba(201,168,76,.28);border-radius:18px;overflow:hidden;box-shadow:0 24px 70px rgba(0,0,0,.55);">
        <div style="padding:18px 20px;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:space-between;background:linear-gradient(180deg,rgba(201,168,76,.1),rgba(255,255,255,.02));">
            <div>
                <h3 style="font-family:'Playfair Display',serif;color:#fff;font-size:1.25rem;font-weight:800;margin:0;">Tanya Produk</h3>
                <p style="color:rgba(255,255,255,.45);font-size:.78rem;margin-top:3px;">Kode produk akan otomatis dikirim ke admin.</p>
            </div>
            <button type="button" id="btnCloseAskProduct"
                    style="width:36px;height:36px;border-radius:999px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.6);font-size:22px;line-height:1;cursor:pointer;">
                &times;
            </button>
        </div>

        <form action="{{ route('customer.chat.askProduct', $product['id']) }}" method="POST" style="padding:20px;">
            @csrf
            <div style="display:flex;gap:12px;padding:12px;background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.07);border-radius:14px;margin-bottom:14px;">
                <img src="{{ $product['gambar_url'] ?? \App\Support\AssetUrl::product($product['gambar'] ?? null) }}" alt="{{ $product['nama'] }}" style="width:74px;height:74px;object-fit:cover;border-radius:10px;border:1px solid rgba(255,255,255,.08);flex-shrink:0;">
                <div style="min-width:0;">
                    <p style="color:#f5efe2;font-weight:800;font-size:.94rem;margin-bottom:4px;overflow-wrap:anywhere;">{{ $product['nama'] }}</p>
                    <p style="color:#d7b957;font-size:.82rem;font-weight:800;">{{ $productCode }}</p>
                    <p style="color:rgba(255,255,255,.5);font-size:.8rem;margin-top:3px;">Rp{{ number_format($hargaDiskon, 0, ',', '.') }}</p>
                </div>
            </div>

            <label for="productQuestion" style="display:block;color:rgba(255,255,255,.68);font-size:.84rem;font-weight:700;margin-bottom:8px;">Pertanyaan</label>
            <textarea id="productQuestion" name="question" rows="4" maxlength="600" placeholder="Contoh: ukuran M masih tersedia? Bahannya adem tidak?"
                      style="width:100%;padding:12px 14px;background:#141414;border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.88rem;line-height:1.5;outline:none;resize:vertical;font-family:inherit;"
                      onfocus="this.style.borderColor='rgba(201,168,76,.55)';"
                      onblur="this.style.borderColor='rgba(255,255,255,.1)';"></textarea>

            <div style="display:flex;gap:10px;margin-top:16px;">
                <button type="button" id="btnCancelAskProduct"
                        style="flex:1;padding:12px 14px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:rgba(255,255,255,.62);font-weight:800;cursor:pointer;">
                    Batal
                </button>
                <button type="submit"
                        style="flex:1.4;padding:12px 14px;background:linear-gradient(135deg,#c9a84c,#a8832d);border:none;border-radius:12px;color:#111;font-weight:900;cursor:pointer;">
                    Kirim ke Admin
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Size Guide Modal --}}
<div id="sizeGuideModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);backdrop-filter:blur(8px);z-index:100;align-items:center;justify-content:center;padding:18px;">
    <div style="width:min(550px, 100%);background:#1c1c1c;border:1px solid rgba(201,168,76,.28);border-radius:18px;overflow:hidden;box-shadow:0 24px 70px rgba(0,0,0,.65);display:flex;flex-direction:column;max-height:90vh;">
        <div style="padding:18px 20px;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:space-between;background:linear-gradient(180deg,rgba(201,168,76,.1),rgba(255,255,255,.02));">
            <div>
                <h3 style="font-family:'Playfair Display',serif;color:#fff;font-size:1.25rem;font-weight:800;margin:0;">Rekomendasi & Panduan Ukuran</h3>
                <p style="color:rgba(255,255,255,.45);font-size:.78rem;margin-top:3px;">Masukkan tinggi dan berat badan Anda untuk rekomendasi instan.</p>
            </div>
            <button type="button" id="btnCloseSizeGuide"
                    style="width:36px;height:36px;border-radius:999px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.6);font-size:22px;line-height:1;cursor:pointer;border:none;">
                &times;
            </button>
        </div>
        
        <div style="padding:20px;overflow-y:auto;flex:1;display:flex;flex-direction:column;gap:20px;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,0.1) transparent;">
            {{-- Inputs --}}
            <div style="display:flex;gap:16px;">
                <div style="flex:1;">
                    <label style="display:block;color:rgba(255,255,255,.7);font-size:.8rem;font-weight:700;margin-bottom:6px;">Tinggi Badan (cm)</label>
                    <input type="number" id="sgHeight" placeholder="Contoh: 170" style="width:100%;padding:10px 12px;background:#141414;border:1px solid rgba(255,255,255,.1);border-radius:10px;color:#fff;outline:none;">
                </div>
                <div style="flex:1;">
                    <label style="display:block;color:rgba(255,255,255,.7);font-size:.8rem;font-weight:700;margin-bottom:6px;">Berat Badan (kg)</label>
                    <input type="number" id="sgWeight" placeholder="Contoh: 65" style="width:100%;padding:10px 12px;background:#141414;border:1px solid rgba(255,255,255,.1);border-radius:10px;color:#fff;outline:none;">
                </div>
            </div>

            <button type="button" id="btnCalculateSize" style="width:100%;padding:12px;background:linear-gradient(135deg,#c9a84c,#a8832d);border:none;border-radius:12px;color:#111;font-weight:800;cursor:pointer;font-size:.9rem;">
                Hitung Ukuran Terbaik
            </button>

            {{-- Recommendation Result --}}
            <div id="sgResult" style="display:none;padding:16px;background:rgba(201,168,76,.08);border:1px solid rgba(201,168,76,.2);border-radius:12px;text-align:center;">
                <p style="color:rgba(255,255,255,.6);font-size:.8rem;margin-bottom:4px;text-transform:uppercase;font-weight:600;">Ukuran Rekomendasi Anda</p>
                <p id="sgRecSize" style="font-size:2.5rem;font-weight:900;color:#c9a84c;margin:6px 0;font-family:'Playfair Display',serif;"></p>
                <p id="sgRecExplanation" style="color:rgba(255,255,255,.8);font-size:.84rem;margin:0;"></p>
                <button type="button" id="sgApplyBtn" style="margin-top:12px;padding:8px 18px;background:linear-gradient(135deg,#c9a84c,#a8832d);border:none;border-radius:8px;color:#111;font-weight:700;font-size:.8rem;cursor:pointer;">Terapkan Ukuran Ini</button>
            </div>

            {{-- Size Table --}}
            <div>
                <p style="color:rgba(255,255,255,.7);font-size:.85rem;font-weight:700;margin-bottom:10px;text-transform:uppercase;">Tabel Dimensi Ukuran (cm)</p>
                <table style="width:100%;border-collapse:collapse;font-size:.85rem;color:rgba(255,255,255,.7);">
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255,255,255,.1);text-align:left;">
                            <th style="padding:8px 4px;color:#fff;">Ukuran</th>
                            <th style="padding:8px 4px;color:#fff;">Lingkar Dada</th>
                            <th style="padding:8px 4px;color:#fff;">Panjang Baju</th>
                            <th style="padding:8px 4px;color:#fff;">Lebar Bahu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom:1px solid rgba(255,255,255,.05);">
                            <td style="padding:8px 4px;font-weight:700;color:#c9a84c;">S</td>
                            <td style="padding:8px 4px;">100</td>
                            <td style="padding:8px 4px;">70</td>
                            <td style="padding:8px 4px;">42</td>
                        </tr>
                        <tr style="border-bottom:1px solid rgba(255,255,255,.05);">
                            <td style="padding:8px 4px;font-weight:700;color:#c9a84c;">M</td>
                            <td style="padding:8px 4px;">104</td>
                            <td style="padding:8px 4px;">72</td>
                            <td style="padding:8px 4px;">44</td>
                        </tr>
                        <tr style="border-bottom:1px solid rgba(255,255,255,.05);">
                            <td style="padding:8px 4px;font-weight:700;color:#c9a84c;">L</td>
                            <td style="padding:8px 4px;">108</td>
                            <td style="padding:8px 4px;">74</td>
                            <td style="padding:8px 4px;">46</td>
                        </tr>
                        <tr style="border-bottom:1px solid rgba(255,255,255,.05);">
                            <td style="padding:8px 4px;font-weight:700;color:#c9a84c;">XL</td>
                            <td style="padding:8px 4px;">112</td>
                            <td style="padding:8px 4px;">76</td>
                            <td style="padding:8px 4px;">48</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Floating Chat --}}
<a href="{{ route('customer.chat.index') }}"
   style="position:fixed;bottom:24px;right:24px;width:56px;height:56px;background:linear-gradient(135deg,#c9a84c,#a8832d);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 32px rgba(201,168,76,.35);z-index:50;transition:transform .25s;"
   onmouseenter="this.style.transform='scale(1.1)';"
   onmouseleave="this.style.transform='';">
    <svg width="24" height="24" fill="none" stroke="#111" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
    <span style="position:absolute;top:-3px;right:-3px;width:12px;height:12px;background:#6ee7b7;border-radius:50%;border:2px solid #131313;animation:pulse 2s infinite;"></span>
</a>

<style>@keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}</style>

<script>
function selectSize(size) {
    document.querySelectorAll('.size-btn').forEach(btn => {
        const isSelected = btn.getAttribute('data-size') === size;
        const stock = parseInt(btn.getAttribute('data-stock') || '0', 10);
        if (stock <= 0) return;
        btn.style.borderColor = isSelected ? '#c9a84c' : 'rgba(255,255,255,.15)';
        btn.style.color = isSelected ? '#c9a84c' : 'rgba(255,255,255,.7)';
        btn.style.background = isSelected ? 'rgba(201,168,76,.1)' : 'transparent';
        if (isSelected) {
            const radio = btn.previousElementSibling;
            if (radio) radio.checked = true;
        }
    });

    const selectedBtn = document.querySelector(`.size-btn[data-size="${size}"]`);
    const hint = document.getElementById('size-stock-hint');
    if (selectedBtn && hint) {
        hint.textContent = `Stok ukuran ${size}: ${selectedBtn.getAttribute('data-stock')} pcs`;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const askProductModal = document.getElementById('askProductModal');
    const openAskProduct = () => {
        askProductModal.style.display = 'flex';
        document.getElementById('productQuestion')?.focus();
    };
    const closeAskProduct = () => {
        askProductModal.style.display = 'none';
    };

    document.getElementById('btnAskProduct')?.addEventListener('click', openAskProduct);
    document.getElementById('btnCloseAskProduct')?.addEventListener('click', closeAskProduct);
    document.getElementById('btnCancelAskProduct')?.addEventListener('click', closeAskProduct);
    askProductModal?.addEventListener('click', function(event) {
        if (event.target === askProductModal) closeAskProduct();
    });

    document.getElementById('btnAddToCart').addEventListener('click', function() {
        const ukuran = document.querySelector('input[name="size"]:checked');
        if (!ukuran) { alert('Pilih ukuran terlebih dahulu!'); return; }
        fetch("{{ route('customer.cart.add') }}", {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({product_id: {{ $product['id'] }}, ukuran: ukuran.value})
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) { alert(data.message); window.location.href = "{{ route('customer.cart.index') }}"; }
            else alert(data.message || 'Gagal menambah ke keranjang!');
        })
        .catch(() => alert('Terjadi kesalahan.'));
    });

    document.getElementById('btnAddToFavorite').addEventListener('click', function() {
        fetch("{{ route('customer.favorites.store') }}", {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({produk_id: {{ $product['id'] }}})
        })
        .then(r => r.json())
        .then(data => alert(data.message))
        .catch(() => alert('Terjadi kesalahan.'));
    });

    const loadMoreBtn = document.getElementById('load-more-btn');
    const showLessBtn = document.getElementById('show-less-btn');
    const moreReviews = document.getElementById('more-reviews');

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            moreReviews.style.display = 'flex';
            loadMoreBtn.style.display = 'none';
            if (showLessBtn) showLessBtn.style.display = 'block';
        });
    }
    if (showLessBtn) {
        showLessBtn.addEventListener('click', function() {
            moreReviews.style.display = 'none';
            showLessBtn.style.display = 'none';
            if (loadMoreBtn) loadMoreBtn.style.display = 'block';
            document.getElementById('reviews-container').scrollIntoView({behavior:'smooth'});
        });
    }

    // Size Guide Modal Event Listeners
    const sizeGuideModal = document.getElementById('sizeGuideModal');
    const btnOpenSizeGuide = document.getElementById('btnOpenSizeGuide');
    const btnCloseSizeGuide = document.getElementById('btnCloseSizeGuide');
    
    btnOpenSizeGuide?.addEventListener('click', () => {
        sizeGuideModal.style.display = 'flex';
    });
    
    btnCloseSizeGuide?.addEventListener('click', () => {
        sizeGuideModal.style.display = 'none';
    });
    
    sizeGuideModal?.addEventListener('click', (e) => {
        if (e.target === sizeGuideModal) {
            sizeGuideModal.style.display = 'none';
        }
    });

    document.getElementById('btnCalculateSize')?.addEventListener('click', () => {
        const height = parseFloat(document.getElementById('sgHeight').value);
        const weight = parseFloat(document.getElementById('sgWeight').value);

        if (!height || !weight) {
            alert('Silakan masukkan tinggi dan berat badan terlebih dahulu!');
            return;
        }

        // BMI logic
        const bmi = weight / ((height / 100) ** 2);
        let recSize = 'M';

        if (bmi < 18.5) {
            recSize = height < 160 ? 'S' : 'M';
        } else if (bmi >= 18.5 && bmi < 23) {
            recSize = height < 170 ? 'M' : 'L';
        } else if (bmi >= 23 && bmi < 27.5) {
            recSize = height < 175 ? 'L' : 'XL';
        } else {
            recSize = 'XL';
        }

        document.getElementById('sgRecSize').textContent = recSize;
        document.getElementById('sgRecExplanation').innerHTML = `Berdasarkan BMI Anda (<strong>${bmi.toFixed(1)}</strong>), ukuran <strong>${recSize}</strong> adalah rekomendasi terbaik untuk fitting yang nyaman.`;
        
        const sizeBtn = document.querySelector(`.size-btn[data-size="${recSize}"]`);
        const isAvailable = sizeBtn && parseInt(sizeBtn.getAttribute('data-stock') || '0', 10) > 0;
        
        const applyBtn = document.getElementById('sgApplyBtn');
        if (isAvailable) {
            applyBtn.style.display = 'inline-block';
            applyBtn.onclick = () => {
                selectSize(recSize);
                sizeGuideModal.style.display = 'none';
            };
        } else {
            applyBtn.style.display = 'none';
            document.getElementById('sgRecExplanation').innerHTML += `<br><span style="color:#ef4444;font-size:0.75rem;margin-top:6px;display:block;">Maaf, stok untuk ukuran ${recSize} saat ini kosong.</span>`;
        }

        document.getElementById('sgResult').style.display = 'block';
    });

    // WhatsApp Buy Event Listener
    document.getElementById('btnWhatsAppBuy')?.addEventListener('click', function() {
        const ukuran = document.querySelector('input[name="size"]:checked');
        if (!ukuran) { alert('Pilih ukuran terlebih dahulu!'); return; }
        
        const productName = "{{ $product['nama'] }}";
        const productSize = ukuran.value;
        const productPrice = "Rp{{ number_format($hargaDiskon, 0, ',', '.') }}";
        const productLink = window.location.href;
        
        const text = `Halo Motifnesia! 👋\nSaya tertarik dengan produk berikut:\n\n🛍️ *${productName}*\n📏 Ukuran: ${productSize}\n💰 Harga: ${productPrice}\n🔗 Link: ${productLink}\n\nMohon informasi ketersediaan dan cara pemesanan. Terima kasih! 🙏`;
        
        const waUrl = `https://wa.me/6281234567890?text=${encodeURIComponent(text)}`;
        window.open(waUrl, '_blank');
    });
});

function switchTab(tab) {
    const descBtn = document.getElementById('tabDescBtn');
    const filoBtn = document.getElementById('tabFiloBtn');
    const descContent = document.getElementById('tabDescContent');
    const filoContent = document.getElementById('tabFiloContent');
    
    if (tab === 'desc') {
        descBtn.style.borderBottomColor = '#c9a84c';
        descBtn.style.color = '#fff';
        filoBtn.style.borderBottomColor = 'transparent';
        filoBtn.style.color = 'rgba(255,255,255,.4)';
        descContent.style.display = 'block';
        filoContent.style.display = 'none';
    } else {
        filoBtn.style.borderBottomColor = '#c9a84c';
        filoBtn.style.color = '#fff';
        descBtn.style.borderBottomColor = 'transparent';
        descBtn.style.color = 'rgba(255,255,255,.4)';
        descContent.style.display = 'none';
        filoContent.style.display = 'block';
    }
}
</script>

@endsection
