@extends('admin.layouts.mainLayout')

@section('title', 'Status Pengiriman')

@section('content')
<div class="space-y-6" x-data="{ searchQuery: '', statusFilter: 'all' }">
    {{-- Search & Filter Bar --}}
    <div class="glass-card rounded-2xl p-4 flex flex-col md:flex-row items-center gap-4 animate-fade-slide-up">
        <div class="relative flex-1 w-full group">
            <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-amber-400 transition-colors"></i>
            <input type="text" 
                   id="searchOrder"
                   x-model="searchQuery"
                   @input="filterOrders()"
                   placeholder="Cari nama pelanggan atau alamat..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-white/10 rounded-xl text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 placeholder-slate-500 transition-all text-sm">
        </div>
        <div class="relative w-full md:w-64">
            <i class="ri-filter-3-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <select id="statusFilter" 
                    x-model="statusFilter"
                    @change="filterOrders()"
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-white/10 rounded-xl text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 appearance-none transition-all text-sm">
                <option value="all" class="bg-slate-800">Semua Status</option>
                @foreach($deliveryStatuses as $status)
                    <option value="{{ $status->nama_status }}" class="bg-slate-800">{{ $status->nama_status }}</option>
                @endforeach
            </select>
            <i class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="glass-card rounded-3xl overflow-hidden animate-fade-slide-up" style="animation-delay: 0.1s;">
        <div class="overflow-x-auto custom-scrollbar pb-2">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-white/5">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-xl font-bold">Detail Pesanan</th>
                        <th class="px-6 py-4 font-bold">Produk</th>
                        <th class="px-6 py-4 font-bold">Total</th>
                        <th class="px-6 py-4 font-bold">Alamat Pengiriman</th>
                        <th class="px-6 py-4 font-bold">Status</th>
                        <th class="px-6 py-4 text-center rounded-tr-xl font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-700/50 transition-colors group order-row" 
                            data-customer="{{ $order->user->full_name ?? $order->user->name }}" 
                            data-address="{{ $order->alamat }}" 
                            data-status="{{ $order->deliveryStatus->nama_status }}">
                            
                            <!-- Customer Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center text-amber-500 shrink-0">
                                        <i class="ri-shopping-bag-3-fill text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-white group-hover:text-amber-400 transition-colors">{{ $order->user->full_name ?? $order->user->name }}</p>
                                        <p class="text-xs text-slate-500 font-mono mt-0.5">ORD-#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Products -->
                            <td class="px-6 py-4 min-w-[200px]">
                                <div class="space-y-2">
                                    @foreach($order->orderItems as $item)
                                        <div class="flex justify-between items-start text-xs">
                                            <p class="text-slate-300 line-clamp-1 max-w-[150px]"><span class="text-amber-500 mr-1">•</span> {{ $item->nama_produk }}</p>
                                            <p class="text-slate-500 whitespace-nowrap ml-2">x{{ $item->qty }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Total -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="font-bold text-emerald-400">Rp {{ number_format($order->total_bayar, 0, ',', '.') }}</p>
                                @if(($order->voucher_discount ?? 0) > 0)
                                    <p class="text-[11px] text-emerald-300 mt-1">Voucher {{ $order->voucher_code }}: -Rp {{ number_format($order->voucher_discount, 0, ',', '.') }}</p>
                                @endif
                                @php
                                    $paymentStatus = $order->payment_status ?? 'waiting_verification';
                                @endphp
                                @can('is-finance')
                                <select class="payment-dropdown mt-3 w-full px-3 py-2 bg-slate-900 border border-white/10 rounded-lg text-xs font-bold focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 cursor-pointer
                                    {{ $paymentStatus === 'verified' ? 'text-emerald-400' : '' }}
                                    {{ $paymentStatus === 'rejected' ? 'text-red-400' : '' }}
                                    {{ $paymentStatus === 'waiting_verification' ? 'text-amber-400' : '' }}"
                                    data-order-id="{{ $order->id }}">
                                    @foreach($paymentStatuses as $value => $label)
                                        <option value="{{ $value }}" @selected($paymentStatus === $value) class="bg-slate-800 text-slate-300">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @else
                                <div class="mt-3 text-xs font-bold uppercase tracking-wider
                                    {{ $paymentStatus === 'verified' ? 'text-emerald-400' : '' }}
                                    {{ $paymentStatus === 'rejected' ? 'text-red-400' : '' }}
                                    {{ $paymentStatus === 'waiting_verification' ? 'text-amber-400' : '' }}">
                                    {{ $paymentStatuses[$paymentStatus] ?? $paymentStatus }}
                                </div>
                                @endcan
                                @if($order->payment_note)
                                    <p class="text-[11px] text-slate-500 mt-1">{{ Str::limit($order->payment_note, 40) }}</p>
                                @endif
                            </td>

                            <!-- Address -->
                            <td class="px-6 py-4 text-xs text-slate-400 max-w-[200px] leading-relaxed">
                                {{ Str::limit($order->alamat, 60) }}
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                @php
                                    $currentStatus = $order->deliveryStatus->nama_status;
                                @endphp
                                @can('is-kasir')
                                <div class="relative group/select">
                                    <select class="status-dropdown w-full px-3 py-2 bg-slate-900 border border-white/10 rounded-lg text-xs font-bold focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 cursor-pointer appearance-none
                                        {{ $currentStatus == 'Pending' ? 'text-slate-400' : '' }}
                                        {{ $currentStatus == 'Diproses' ? 'text-blue-400' : '' }}
                                        {{ $currentStatus == 'Dikemas' ? 'text-purple-400' : '' }}
                                        {{ $currentStatus == 'Dalam Perjalanan' ? 'text-amber-400' : '' }}
                                        {{ $currentStatus == 'Sampai' ? 'text-emerald-400' : '' }}" 
                                            data-order-id="{{ $order->id }}">
                                        @foreach($deliveryStatuses as $status)
                                            <option value="{{ $status->id }}" 
                                                    @if($order->delivery_status_id == $status->id) selected @endif
                                                    class="bg-slate-800 text-slate-300">
                                                {{ $status->nama_status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <i class="ri-arrow-down-s-line absolute right-2 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none"></i>
                                </div>
                                @else
                                <div class="px-3 py-2 text-xs font-bold
                                    {{ $currentStatus == 'Pending' ? 'text-slate-400' : '' }}
                                    {{ $currentStatus == 'Diproses' ? 'text-blue-400' : '' }}
                                    {{ $currentStatus == 'Dikemas' ? 'text-purple-400' : '' }}
                                    {{ $currentStatus == 'Dalam Perjalanan' ? 'text-amber-400' : '' }}
                                    {{ $currentStatus == 'Sampai' ? 'text-emerald-400' : '' }}">
                                    {{ $currentStatus }}
                                </div>
                                @endcan
                                <div class="mt-2 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider
                                    {{ $currentStatus == 'Pending' ? 'text-slate-500' : '' }}
                                    {{ $currentStatus == 'Diproses' ? 'text-blue-500' : '' }}
                                    {{ $currentStatus == 'Dikemas' ? 'text-purple-500' : '' }}
                                    {{ $currentStatus == 'Dalam Perjalanan' ? 'text-amber-500' : '' }}
                                    {{ $currentStatus == 'Sampai' ? 'text-emerald-500' : '' }}">
                                    @if($currentStatus == 'Pending') <i class="ri-time-line text-sm"></i> PENDING
                                    @elseif($currentStatus == 'Diproses') <i class="ri-settings-2-line text-sm"></i> DIPROSES
                                    @elseif($currentStatus == 'Dikemas') <i class="ri-box-3-line text-sm"></i> DIKEMAS
                                    @elseif($currentStatus == 'Dalam Perjalanan') <i class="ri-truck-line text-sm"></i> JALAN
                                    @elseif($currentStatus == 'Sampai') <i class="ri-checkbox-circle-line text-sm"></i> SAMPAI
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @can('is-finance')
                                    <button onclick="showPaymentProof('{{ $order->payment_number }}', '{{ $order->payment_proof ? asset($order->payment_proof) : '' }}')"
                                            class="p-2 bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white rounded-lg transition-all" title="Bukti Pembayaran">
                                        <i class="ri-bank-card-line text-lg"></i>
                                    </button>
                                    <button onclick="updatePaymentStatus({{ $order->id }})"
                                            class="p-2 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-400 hover:text-white rounded-lg transition-all" title="Update Pembayaran">
                                        <i class="ri-shield-check-line text-lg"></i>
                                    </button>
                                    @endcan
                                    @can('is-kasir')
                                    <button onclick="updateStatus({{ $order->id }})"
                                            class="p-2 bg-amber-500/10 hover:bg-amber-500 text-amber-400 hover:text-white rounded-lg transition-all" title="Update Status">
                                        <i class="ri-save-3-line text-lg"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="defaultEmptyRow">
                            <td colspan="6" class="px-6 py-16 text-center text-slate-500">
                                <i class="ri-truck-line text-5xl block mb-3 text-slate-600"></i>
                                <p class="font-medium text-lg text-slate-400">Belum Ada Pesanan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Search Empty State --}}
            <div id="searchEmptyState" class="hidden py-16 text-center text-slate-500">
                <i class="ri-search-line text-5xl block mb-3 text-slate-600"></i>
                <p class="font-medium text-lg text-slate-400">Pesanan tidak ditemukan</p>
            </div>
        </div>
    </div>
<!-- Payment Proof Modal -->
<div id="paymentProofModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="glass-card max-w-lg w-full rounded-3xl overflow-hidden border border-white/10 animate-scale-up" style="background:#1e293b;">
        <div class="flex justify-between items-center px-6 py-4 border-b border-white/5">
            <h3 class="font-bold text-white font-['Plus_Jakarta_Sans']">Bukti Pembayaran</h3>
            <button onclick="closePaymentProof()" class="text-slate-400 hover:text-white text-xl">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="p-6 text-center">
            <div id="modalPaymentInfo" class="mb-4 text-slate-300 text-sm font-semibold font-mono"></div>
            <div class="relative w-full max-h-[400px] overflow-auto rounded-xl bg-slate-950 flex items-center justify-center p-2 border border-white/5">
                <img id="modalPaymentImg" src="" alt="Bukti Pembayaran" class="max-w-full max-h-[380px] object-contain hidden">
                <div id="modalNoImgText" class="text-slate-500 py-12 hidden">
                    <i class="ri-image-line text-5xl block mb-2"></i>
                    <span>Tidak ada unggahan foto bukti pembayaran</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-24 right-6 bg-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl transform translate-x-full transition-transform duration-300 z-50 flex items-center gap-3 border border-emerald-500/50">
    <i class="ri-checkbox-circle-fill text-xl"></i>
    <span id="toastMsg" class="font-medium font-['Plus_Jakarta_Sans']"></span>
</div>

@push('scripts')
<script>
function filterOrders() {
    const component = document.querySelector('[x-data]').__x.$data;
    const searchTerm = component.searchQuery.toLowerCase();
    const selectedStatus = component.statusFilter;
    const rows = document.querySelectorAll('.order-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const customerName = row.getAttribute('data-customer').toLowerCase();
        const address = row.getAttribute('data-address').toLowerCase();
        const status = row.getAttribute('data-status');
        
        const matchSearch = customerName.includes(searchTerm) || address.includes(searchTerm);
        const matchStatus = selectedStatus === 'all' || status === selectedStatus;
        
        if (matchSearch && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const defaultEmptyRow = document.getElementById('defaultEmptyRow');
    const searchEmptyState = document.getElementById('searchEmptyState');
    
    if (defaultEmptyRow) defaultEmptyRow.style.display = 'none';
    
    if (visibleCount === 0 && rows.length > 0) {
        searchEmptyState.classList.remove('hidden');
    } else {
        searchEmptyState.classList.add('hidden');
        if (visibleCount === 0 && rows.length === 0 && defaultEmptyRow) {
            defaultEmptyRow.style.display = '';
        }
    }
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMsg');
    
    if (type === 'error') {
        toast.className = 'fixed top-24 right-6 bg-red-600 text-white px-6 py-4 rounded-xl shadow-2xl transform translate-x-full transition-transform duration-300 z-50 flex items-center gap-3 border border-red-500/50';
        toast.innerHTML = '<i class="ri-error-warning-fill text-xl"></i><span id="toastMsg" class="font-medium font-[\'Plus_Jakarta_Sans\']">' + message + '</span>';
    } else {
        toast.className = 'fixed top-24 right-6 bg-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl transform translate-x-full transition-transform duration-300 z-50 flex items-center gap-3 border border-emerald-500/50';
        toast.innerHTML = '<i class="ri-checkbox-circle-fill text-xl"></i><span id="toastMsg" class="font-medium font-[\'Plus_Jakarta_Sans\']">' + message + '</span>';
    }
    
    setTimeout(() => toast.classList.remove('translate-x-full'), 100);
    setTimeout(() => toast.classList.add('translate-x-full'), 3000);
}

function showPaymentProof(paymentNumber, proofUrl) {
    const modal = document.getElementById('paymentProofModal');
    const info = document.getElementById('modalPaymentInfo');
    const img = document.getElementById('modalPaymentImg');
    const noImgText = document.getElementById('modalNoImgText');
    
    info.textContent = `Nomor Referensi: ${paymentNumber}`;
    
    if (proofUrl) {
        img.src = proofUrl;
        img.classList.remove('hidden');
        noImgText.classList.add('hidden');
    } else {
        img.src = '';
        img.classList.add('hidden');
        noImgText.classList.remove('hidden');
    }
    
    modal.classList.remove('hidden');
}

function closePaymentProof() {
    document.getElementById('paymentProofModal').classList.add('hidden');
}

function updateStatus(orderId) {
    const dropdown = document.querySelector(`.status-dropdown[data-order-id="${orderId}"]`);
    const statusId = dropdown.value;
    
    fetch(`/admin/order-status/${orderId}/update`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            delivery_status_id: statusId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Status berhasil diperbarui!');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Gagal memperbarui status.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan koneksi.', 'error');
    });
}

function updatePaymentStatus(orderId) {
    const dropdown = document.querySelector(`.payment-dropdown[data-order-id="${orderId}"]`);
    const paymentStatus = dropdown.value;
    let paymentNote = '';

    if (paymentStatus === 'rejected') {
        paymentNote = prompt('Catatan penolakan pembayaran:') || '';
        if (!paymentNote.trim()) {
            showToast('Catatan wajib diisi saat pembayaran ditolak.', 'error');
            return;
        }
    }

    fetch(`/admin/order-status/${orderId}/payment`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            payment_status: paymentStatus,
            payment_note: paymentNote
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Status pembayaran berhasil diperbarui!');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast(data.message || 'Gagal memperbarui pembayaran.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan koneksi.', 'error');
    });
}
</script>
@endpush
@endsection
