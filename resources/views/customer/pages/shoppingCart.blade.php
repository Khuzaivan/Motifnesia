@extends('customer.layouts.mainLayout')

@section('container')
    <div class="shopping-cart-container">
        <h1>Shopping Cart</h1> 
        
        {{-- HEADER TABEL --}}
        <div class="cart-header">
            <span class="header-col header-product">
                <input type="checkbox" class="select-all-checkbox"> PRODUK
            </span>
            <span class="header-col header-price">HARGA</span>
            <span class="header-col header-qty">JUMLAH</span>
            <span class="header-col header-subtotal">SUBTOTAL</span>
        </div>

        {{-- DAFTAR PRODUK (LOOPING COMPONENT DI SINI) --}}
        <div class="cart-items-list">
            @include('customer.components.componentShoppingCart')
            {{-- Kita duplikat komponen untuk simulasi banyak item --}}
            @include('customer.components.componentShoppingCart') 
        </div>

        {{-- TOTAL KERANJANG BELANJA --}}
        <div class="cart-summary-box">
            <h2 class="summary-title">TOTAL KERANJANG BELANJA</h2>
            
            <div class="summary-row">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value">Rp0</span>
            </div>
            
            <div class="summary-row total-row">
                <span class="summary-label">Total</span>
                <span class="summary-value final-total">Rp0</span>
            </div>
            
            <div class="summary-checkout">
                <button class="btn-checkout" onclick="window.location='{{ route('customer.checkout.index') }}'">CHECKOUT</button>
            </div>
        </div>
    </div>
@endsection