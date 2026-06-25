@extends('admin.layouts.mainLayout')

@section('title', 'Daftar Pelanggan')

@section('content')
<div x-data="{ searchQuery: '' }" class="space-y-6">
    {{-- Success Alert --}}
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl shadow-lg flex items-center gap-3 animate-fade-slide-up">
            <i class="ri-checkbox-circle-fill text-2xl"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Toolbar --}}
    <div class="glass-card rounded-2xl p-4 flex flex-col md:flex-row items-center justify-between gap-4 animate-fade-slide-up">
        <h2 class="text-xl font-bold font-['Plus_Jakarta_Sans'] text-white">Data Pelanggan</h2>
        
        <div class="relative w-full md:w-80 group">
            <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-amber-400 transition-colors"></i>
            <input type="text" 
                   id="searchCustomer"
                   x-model="searchQuery"
                   @input="filterCustomers()"
                   placeholder="Cari username / nama / email..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-white/10 rounded-xl text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 placeholder-slate-500 transition-all text-sm">
        </div>
    </div>

    {{-- Customer Table --}}
    <div class="glass-card rounded-3xl overflow-hidden animate-fade-slide-up" style="animation-delay: 0.1s;">
        <div class="overflow-x-auto custom-scrollbar pb-2">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-white/5">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-xl font-bold">Pelanggan</th>
                        <th class="px-6 py-4 font-bold">Kontak</th>
                        <th class="px-6 py-4 text-center font-bold">Total Pembelian</th>
                        <th class="px-6 py-4 text-center font-bold">Status</th>
                        <th class="px-6 py-4 text-center rounded-tr-xl font-bold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5" id="customerTableBody">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-700/50 transition-colors group customer-row" data-customer='@json($customer)'>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg shrink-0 border border-white/10">
                                        {{ strtoupper(substr($customer['username'] ?? $customer['email'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-white group-hover:text-amber-400 transition-colors">{{ $customer['full_name'] ?? 'N/A' }}</p>
                                        <p class="text-xs text-slate-500">@ {{ $customer['username'] ?? 'unknown' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-slate-400">
                                    <i class="ri-mail-line"></i> {{ $customer['email'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-lg text-xs font-bold inline-flex items-center gap-1.5">
                                    <i class="ri-shopping-bag-3-line"></i> {{ $customer['total_products'] ?? 0 }} Produk
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(($customer['account_status'] ?? 'active') === 'active')
                                    <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full text-xs font-bold">Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 bg-red-500/10 text-red-400 border border-red-500/20 rounded-full text-xs font-bold">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button class="w-8 h-8 rounded-lg bg-blue-500/10 hover:bg-blue-500 text-blue-400 hover:text-white flex items-center justify-center transition-all" title="Detail">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <form action="{{ route('admin.customers.destroy', $customer['id']) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('{{ ($customer['account_status'] ?? 'active') === 'active' ? 'Nonaktifkan customer ini?' : 'Aktifkan customer ini kembali?' }}')"
                                                class="w-8 h-8 rounded-lg {{ ($customer['account_status'] ?? 'active') === 'active' ? 'bg-red-500/10 hover:bg-red-500 text-red-400' : 'bg-emerald-500/10 hover:bg-emerald-500 text-emerald-400' }} hover:text-white flex items-center justify-center transition-all"
                                                title="{{ ($customer['account_status'] ?? 'active') === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                            <i class="{{ ($customer['account_status'] ?? 'active') === 'active' ? 'ri-user-forbid-line' : 'ri-user-follow-line' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="defaultEmptyRow">
                            <td colspan="5" class="px-6 py-16 text-center text-slate-500">
                                <i class="ri-group-line text-5xl block mb-3 text-slate-600"></i>
                                <p class="font-medium text-lg text-slate-400">Belum Ada Pelanggan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            {{-- Search Empty State --}}
            <div id="searchEmptyState" class="hidden py-16 text-center text-slate-500">
                <i class="ri-search-line text-5xl block mb-3 text-slate-600"></i>
                <p class="font-medium text-lg text-slate-400">Tidak ada pelanggan yang cocok</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function filterCustomers() {
        const component = document.querySelector('[x-data]').__x.$data;
        const searchTerm = component.searchQuery.toLowerCase();
        const rows = document.querySelectorAll('.customer-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const customer = JSON.parse(row.getAttribute('data-customer'));
            const searchableText = [
                customer.username || '',
                customer.full_name || '',
                customer.email || ''
            ].join(' ').toLowerCase();
            
            if (searchableText.includes(searchTerm)) {
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
</script>
@endpush
@endsection
