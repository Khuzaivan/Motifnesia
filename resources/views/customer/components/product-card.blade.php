@php
  $img = $product['gambar_url'] ?? \App\Support\AssetUrl::product($product['gambar'] ?? null);
  $rating = $product['rating'] ?? 5.0;
  $hargaDiskon = $product['harga_diskon'] ?? $product['harga'];
  $diskonPersen = $product['diskon_persen'] ?? 0;
  $detailUrl = route('customer.product.detail', ['id' => $product['id']]);
@endphp

<article
   class="motif-product-card"
   style="display:flex;flex-direction:column;width:100%;min-height:430px;background:#181818;border-radius:20px;border:1px solid rgba(255,255,255,0.06);overflow:hidden;text-decoration:none;transition:transform 0.25s ease,box-shadow 0.25s ease,border-color 0.25s ease;position:relative;color:inherit;"
   onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 18px 42px -24px rgba(201,168,76,0.55)';this.style.borderColor='rgba(201,168,76,0.18)';this.querySelector('.pc-img-el').style.transform='scale(1.06)';this.querySelectorAll('.pc-fade').forEach(el=>el.style.opacity='1');"
   onmouseleave="this.style.transform='';this.style.boxShadow='';this.style.borderColor='rgba(255,255,255,0.06)';this.querySelector('.pc-img-el').style.transform='scale(1)';this.querySelectorAll('.pc-fade').forEach(el=>el.style.opacity='0');">

    <div style="position:relative;width:100%;aspect-ratio:1/1;overflow:hidden;background:#222;flex-shrink:0;">
        <a href="{{ $detailUrl }}" style="display:block;width:100%;height:100%;text-decoration:none;color:inherit;">
            <img class="pc-img-el"
                 src="{{ $img }}"
                 alt="{{ $product['nama'] }}"
                 loading="lazy"
                 style="display:block;width:100%;height:100%;object-fit:cover;object-position:top;transition:transform 0.55s ease;">

            <div class="pc-fade" style="position:absolute;inset:0;background:linear-gradient(to top,#181818 0%,rgba(24,24,24,0.28) 48%,transparent 100%);opacity:0;transition:opacity 0.3s ease;pointer-events:none;"></div>
        </a>

        <div style="position:absolute;top:12px;left:12px;right:12px;display:flex;justify-content:space-between;align-items:flex-start;z-index:10;pointer-events:none;">
            <span style="background:rgba(0,0,0,0.62);backdrop-filter:blur(6px);padding:5px 11px;border-radius:999px;font-size:9px;font-weight:700;color:#c9a84c;letter-spacing:0.18em;display:flex;align-items:center;gap:5px;border:1px solid rgba(255,255,255,0.1);">
                <span style="width:6px;height:6px;background:#c9a84c;border-radius:50%;box-shadow:0 0 5px #c9a84c;display:inline-block;"></span>
                BATIK
            </span>
            @if($diskonPersen > 0)
                <span style="background:linear-gradient(135deg,#c9a84c,#a8832d);color:white;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:700;">-{{ $diskonPersen }}%</span>
            @endif
        </div>

        <div class="pc-fade" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;gap:12px;opacity:0;transition:opacity 0.3s ease;z-index:20;pointer-events:none;">
            @auth
                <button type="button"
                        data-product-id="{{ $product['id'] }}"
                        onclick="event.preventDefault();event.stopPropagation();addToFavorite(this)"
                        style="width:44px;height:44px;border-radius:50%;background:rgba(0,0,0,0.58);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.16);color:white;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 0.2s,transform 0.2s;pointer-events:auto;"
                        onmouseenter="this.style.background='#c9a84c';this.style.borderColor='transparent';this.style.transform='scale(1.08)';"
                        onmouseleave="this.style.background='rgba(0,0,0,0.58)';this.style.borderColor='rgba(255,255,255,0.16)';this.style.transform='';"
                        aria-label="Tambah ke favorit">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </button>
            @else
                <a href="{{ route('auth.login') }}"
                   style="width:44px;height:44px;border-radius:50%;background:rgba(0,0,0,0.58);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.16);color:white;display:flex;align-items:center;justify-content:center;transition:background 0.2s,transform 0.2s;pointer-events:auto;"
                   onmouseenter="this.style.background='#c9a84c';this.style.borderColor='transparent';this.style.transform='scale(1.08)';"
                   onmouseleave="this.style.background='rgba(0,0,0,0.58)';this.style.borderColor='rgba(255,255,255,0.16)';this.style.transform='';"
                   aria-label="Login untuk tambah favorit">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </a>
            @endauth

            <a href="{{ $detailUrl }}"
               style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#c9a84c,#a8832d);color:white;display:flex;align-items:center;justify-content:center;text-decoration:none;transition:transform 0.2s,opacity 0.2s;pointer-events:auto;"
               onmouseenter="this.style.opacity='0.88';this.style.transform='scale(1.08)';"
               onmouseleave="this.style.opacity='1';this.style.transform='';"
               aria-label="Lihat detail produk">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </a>
        </div>

        <div class="pc-fade" style="position:absolute;bottom:12px;left:0;right:0;text-align:center;pointer-events:none;z-index:15;opacity:0;transition:opacity 0.3s ease;">
            <span style="display:inline-block;padding:4px 14px;border:1px solid rgba(201,168,76,0.4);background:rgba(0,0,0,0.45);backdrop-filter:blur(6px);border-radius:999px;color:#c9a84c;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;font-weight:600;">Lihat Koleksi</span>
        </div>
    </div>

    <div style="padding:18px 18px 16px;display:flex;flex-direction:column;flex:1;justify-content:space-between;text-align:center;background:#181818;border-top:1px solid rgba(255,255,255,0.05);">
        <div>
            <a href="{{ $detailUrl }}" style="text-decoration:none;color:inherit;">
                <h3 style="color:rgba(255,255,255,0.92);font-size:1rem;font-weight:700;margin:0 0 10px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:48px;font-family:'Playfair Display',serif;line-height:1.45;transition:color 0.25s;">
                    {{ $product['nama'] }}
                </h3>
            </a>
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;margin-bottom:12px;min-height:42px;">
                @if($diskonPersen > 0)
                    <span style="font-size:11px;color:rgba(255,255,255,0.38);text-decoration:line-through;">Rp {{ number_format($product['harga'], 0, ',', '.') }}</span>
                @endif
                <span style="color:#c9a84c;font-size:15px;font-weight:700;letter-spacing:0.03em;">Rp {{ number_format($hargaDiskon, 0, ',', '.') }}</span>
            </div>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;font-size:12px;color:rgba(255,255,255,0.46);font-weight:600;padding-top:12px;border-top:1px solid rgba(255,255,255,0.06);">
            <div style="display:flex;align-items:center;gap:5px;min-width:0;">
                <svg width="13" height="13" fill="#c9a84c" viewBox="0 0 20 20" style="flex-shrink:0;"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                <span>{{ number_format($rating, 1) }}</span>
            </div>
            <span style="white-space:nowrap;">Terjual {{ $product['terjual'] ?? 0 }}</span>
        </div>
    </div>
</article>
