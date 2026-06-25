<div class="b-card-product" style="
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    overflow: hidden;
    width: 160px;
    transition: transform 0.2s;
    cursor: pointer;
">
    @php
        $g = $gambar ?? null;
        $src = \App\Support\AssetUrl::product($g);
    @endphp

    <img src="{{ $src }}" 
         alt="{{ $nama ?? 'Produk' }}" 
         style="width: 100%; height: 180px; object-fit: cover;">
    <div style="
        background-color: #7B4B34;
        color: white;
        text-align: center;
        padding: 8px 0;
        font-weight: 500;
        font-size: 14px;
    ">
        {{ $nama ?? 'Batik Batik Batik' }}
    </div>
</div>
