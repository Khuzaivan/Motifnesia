@props(['review', 'isOdd'])

{{-- isOdd digunakan untuk memberi warna selang-seling --}}
<div class="review-card {{ $isOdd ? 'odd-row' : 'even-row' }}">
    {{-- Kolom Nama --}}
    <div class="card-column review-name-col">
        <span class="user-icon">👤</span> {{-- Menggantikan icon user di foto --}}
        <span class="customer-name">{{ $review['user_name'] }}</span>
    </div>

    {{-- Kolom Email --}}
    <div class="card-column review-email-col">
        <span class="customer-email">{{ $review['user_email'] }}</span>
    </div>

    {{-- Kolom Rating --}}
    <div class="card-column review-rating-col">
        <span class="star-icon">⭐</span>
        <span class="review-rating-value">{{ number_format($review['rating'], 1) }}</span>
    </div>
</div>