@props(['product', 'isOdd'])

{{-- Card untuk menampilkan produk dengan reviews --}}
<div class="product-review-card {{ $isOdd ? 'odd-row' : 'even-row' }}">
    {{-- Kolom Foto Produk --}}
    <div class="card-column product-image-col">
        <img src="{{ $product['gambar_url'] ?? \App\Support\AssetUrl::product($product['gambar'] ?? null) }}" alt="{{ $product['nama_produk'] }}" class="product-image">
    </div>

    {{-- Kolom Nama Produk --}}
    <div class="card-column product-name-col">
        <span class="product-name">{{ $product['nama_produk'] }}</span>
    </div>

    {{-- Kolom Rating Rata-rata --}}
    <div class="card-column rating-col">
        <div class="rating-display">
            @php
                $fullStars = floor($product['average_rating']);
                $hasHalfStar = ($product['average_rating'] - $fullStars) >= 0.5;
            @endphp
            
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= $fullStars)
                    <span class="star filled">★</span>
                @elseif ($i == $fullStars + 1 && $hasHalfStar)
                    <span class="star half">★</span>
                @else
                    <span class="star empty">☆</span>
                @endif
            @endfor
            
            <span class="rating-value">{{ $product['average_rating'] }}/5</span>
        </div>
    </div>

    {{-- Kolom Total Ulasan --}}
    <div class="card-column total-reviews-col">
        <span class="total-reviews">{{ $product['total_reviews'] }} ulasan</span>
    </div>

    {{-- Kolom Aksi (Dropdown Detail Ulasan) --}}
    <div class="card-column actions-col">
        @if($product['has_reviews'])
            <div class="dropdown">
                <button class="btn-view-reviews" onclick="toggleReviewsDropdown({{ $product['id'] }})">
                    Lihat Ulasan ▼
                </button>
                <div id="reviews-dropdown-{{ $product['id'] }}" class="reviews-dropdown-content">
                    @foreach($product['reviews'] as $review)
                        <div class="review-item">
                            <div class="review-header">
                                <strong>{{ $review['customer_name'] }}</strong>
                                <small>{{ $review['date'] }}</small>
                            </div>
                            <div class="review-rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review['rating'])
                                        <span class="star filled">★</span>
                                    @else
                                        <span class="star empty">☆</span>
                                    @endif
                                @endfor
                                <span class="rating-value">{{ $review['rating'] }}/5</span>
                            </div>
                            <div class="review-comment">
                                <p>{{ $review['comment'] ?? 'Tidak ada komentar' }}</p>
                            </div>
                            <div class="review-order">
                                <small>Order: #{{ $review['order_number'] }}</small>
                            </div>
                        </div>
                        @if (!$loop->last)
                            <hr class="review-divider">
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <button class="btn-view-reviews btn-no-reviews" onclick="showNoReviewsNotification()">
                Lihat Ulasan ▼
            </button>
        @endif
    </div>
</div>
