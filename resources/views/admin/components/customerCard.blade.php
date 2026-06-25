@props(['customer', 'isOdd'])

{{-- isOdd digunakan untuk memberi warna selang-seling --}}
<div class="customer-card {{ $isOdd ? 'odd-row' : 'even-row' }}">
    {{-- Kolom Username --}}
    <div class="card-column username-col">
        <span class="user-icon">👤</span>
        <span class="customer-username">{{ $customer['username'] }}</span>
    </div>

    {{-- Kolom Nama Lengkap --}}
    <div class="card-column fullname-col">
        <span class="customer-fullname">{{ $customer['full_name'] }}</span>
    </div>

    {{-- Kolom Email --}}
    <div class="card-column email-col">
        <span class="customer-email">{{ $customer['email'] }}</span>
    </div>

    {{-- Kolom Total Produk --}}
    <div class="card-column products-col">
        <span class="total-products">{{ $customer['total_products'] }}</span>
    </div>

    {{-- Kolom Aksi --}}
    <div class="card-column actions-col">
        {{-- Button Detail Pesanan dengan Dropdown --}}
        <div class="dropdown">
            <button class="btn-detail" onclick="toggleDropdown({{ $customer['id'] }})">
                Detail Pesanan ▼
            </button>
            <div id="dropdown-{{ $customer['id'] }}" class="dropdown-content">
                @foreach($customer['orders'] as $order)
                    <div class="order-detail">
                        <strong>Order #{{ $order->order_number }}</strong>
                        <small>{{ $order->created_at->format('d M Y') }}</small>
                        <ul class="order-items-list">
                            @foreach($order->orderItems as $item)
                                <li>
                                    {{ $item->nama_produk }} 
                                    ({{ $item->ukuran }}) 
                                    x{{ $item->qty }} 
                                    - Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </li>
                            @endforeach
                        </ul>
                        <div class="order-total">
                            <strong>Total: Rp {{ number_format($order->total_bayar, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <hr>
                @endforeach
            </div>
        </div>

        {{-- Button Hapus --}}
        <form action="{{ route('admin.customers.destroy', $customer['id']) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-delete">🗑️ Hapus</button>
        </form>
    </div>
</div>

<script>
function toggleDropdown(customerId) {
    const dropdown = document.getElementById('dropdown-' + customerId);
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
window.onclick = function(event) {
    if (!event.target.matches('.btn-detail')) {
        const dropdowns = document.getElementsByClassName('dropdown-content');
        for (let i = 0; i < dropdowns.length; i++) {
            const openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}
</script>