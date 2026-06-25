@props(['order', 'isOdd'])

{{-- isOdd digunakan untuk memberi warna selang-seling --}}
<div class="order-status-row {{ $isOdd ? 'odd-row' : 'even-row' }}">
    {{-- Kolom Customer & Produk --}}
    <div class="row-col col-detail">
        <span class="user-icon">👤</span> 
        <div class="customer-product-info">
            <p class="customer-name-sm">{{ $order['customer_name'] }}</p>
            <p class="product-detail-sm">{{ $order['product_detail'] }}</p>
        </div>
    </div>

    {{-- Kolom Alamat --}}
    <div class="row-col col-address">
        {{ $order['address'] }}
    </div>

    {{-- Kolom Status (Dropdown HARDCODE) --}}
    <div class="row-col col-status">
        <select class="status-dropdown" data-order-id="{{ $order['id'] }}">
            <option value="Diproses" {{ $order['status'] == 'Diproses' ? 'selected' : '' }}>Diproses</option>
            <option value="Dikemas" {{ $order['status'] == 'Dikemas' ? 'selected' : '' }}>Dikemas</option>
            <option value="Dalam perjalanan" {{ $order['status'] == 'Dalam perjalanan' ? 'selected' : '' }}>Dalam perjalanan</option>
            <option value="Sampai" {{ $order['status'] == 'Sampai' ? 'selected' : '' }}>Sampai</option>
        </select>
        <span class="dropdown-arrow"></span>
    </div>
</div>