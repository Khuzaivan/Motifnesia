@props(['item'])

<div class="best-seller-row">
    <div class="row-col seller-col-name">{{ $item['product_name'] }}</div>
    <div class="row-col seller-col-unit">{{ $item['units_sold'] }}</div>
    <div class="row-col seller-col-revenue">Rp {{ $item['revenue'] }}</div>
</div>