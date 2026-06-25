@props(['returnItem', 'isOdd'])

{{-- isOdd digunakan untuk memberi warna selang-seling --}}
<div class="return-row {{ $isOdd ? 'odd-row' : 'even-row' }}">
    <div class="row-col col-id">{{ $returnItem['customer_id'] }}</div>
    <div class="row-col col-name">{{ $returnItem['customer_name'] }}</div>
    <div class="row-col col-product">{{ $returnItem['product_name'] }}</div>
    <div class="row-col col-reason">{{ $returnItem['reason'] }}</div>
    <div class="row-col col-action">
        {{-- Tombol Tindakan (sesuai screenshot) --}}
        <button class="action-button">Tindakan</button>
    </div>
</div>