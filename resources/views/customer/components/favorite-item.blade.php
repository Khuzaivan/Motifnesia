<div class="favorite-item-card">
    <div class="product-details-favorite">
        <img src="{{ $item['gambar_url'] ?? \App\Support\AssetUrl::product($item['gambar'] ?? null) }}" alt="{{ $item['nama'] }}" class="product-thumb-fav">
        <div class="product-info-fav">
            <p class="product-name-fav">{{ $item['nama'] }}</p>
            <p class="product-price-fav">Rp{{ number_format($item['harga'], 0, ',', '.') }}</p>
        </div>
    </div>
    
    {{-- Tombol Hapus (ikon tempat sampah) --}}
    <button class="remove-fav-btn">
        <i class="fa-solid fa-trash-can"></i> 
    </button>
</div>
