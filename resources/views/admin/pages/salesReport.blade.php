@extends('admin.layouts.mainLayout')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="space-y-6">
    {{-- Header & Filters --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 glass-card p-4 rounded-2xl animate-fade-slide-up">
        <h2 class="text-xl font-bold font-['Plus_Jakarta_Sans'] text-white">Ringkasan Penjualan</h2>
        
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <select id="period-filter" 
                    class="bg-slate-900 border border-white/10 rounded-xl px-4 py-2 text-slate-300 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm w-full sm:w-auto"
                    onchange="applyPeriodFilter(this.value)">
                <option value="today" {{ $currentPeriod == 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="7" {{ $currentPeriod == '7' ? 'selected' : '' }}>7 Hari Terakhir</option>
                <option value="30" {{ $currentPeriod == '30' ? 'selected' : '' }}>30 Hari Terakhir</option>
                <option value="month" {{ $currentPeriod == 'month' ? 'selected' : '' }}>Bulan Ini</option>
            </select>
            
            <a href="{{ route('admin.reports.export') }}?period={{ $currentPeriod }}" 
               class="flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 hover:shadow-lg hover:shadow-emerald-500/20 hover:scale-[1.02] text-white font-semibold rounded-xl transition-all text-sm whitespace-nowrap">
                <i class="ri-file-excel-2-line text-lg"></i>
                <span class="hidden sm:inline">Export Excel</span>
            </a>
        </div>
    </div>

    {{-- Metrics Cards (Alpine.js Count Up) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Revenue Card --}}
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:border-amber-500/50 transition-colors animate-fade-slide-up" style="animation-delay: 0.1s;">
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div>
                    <p class="text-slate-400 text-sm font-medium mb-1">Total Revenue</p>
                    <h3 class="text-2xl font-bold text-white font-['Plus_Jakarta_Sans']" x-data="{ count: 0 }" x-init="let target = {{ $totalRevenue }}; let interval = setInterval(() => { count += target/30; if(count >= target) { count = target; clearInterval(interval); } }, 50)">
                        Rp <span x-text="Math.round(count).toLocaleString('id-ID')">0</span>
                    </h3>
                </div>
                <div class="w-12 h-12 bg-amber-500/10 text-amber-500 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-white transition-all">
                    <i class="ri-money-dollar-circle-line"></i>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 text-9xl text-white/5 group-hover:text-amber-500/5 transition-colors pointer-events-none">
                <i class="ri-money-dollar-circle-fill"></i>
            </div>
        </div>
        
        {{-- Total Orders Card --}}
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:border-blue-500/50 transition-colors animate-fade-slide-up" style="animation-delay: 0.2s;">
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div>
                    <p class="text-slate-400 text-sm font-medium mb-1">Total Orders</p>
                    <h3 class="text-2xl font-bold text-white font-['Plus_Jakarta_Sans']" x-data="{ count: 0 }" x-init="let target = {{ $totalOrders }}; let interval = setInterval(() => { count += Math.ceil(target/30); if(count >= target) { count = target; clearInterval(interval); } }, 50)">
                        <span x-text="count">0</span>
                    </h3>
                </div>
                <div class="w-12 h-12 bg-blue-500/10 text-blue-400 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-blue-500 group-hover:text-white transition-all">
                    <i class="ri-shopping-cart-2-line"></i>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 text-9xl text-white/5 group-hover:text-blue-500/5 transition-colors pointer-events-none">
                <i class="ri-shopping-cart-2-fill"></i>
            </div>
        </div>

        {{-- Products Sold Card --}}
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:border-purple-500/50 transition-colors animate-fade-slide-up" style="animation-delay: 0.3s;">
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div>
                    <p class="text-slate-400 text-sm font-medium mb-1">Products Sold</p>
                    <h3 class="text-2xl font-bold text-white font-['Plus_Jakarta_Sans']" x-data="{ count: 0 }" x-init="let target = {{ $totalProductsSold }}; let interval = setInterval(() => { count += Math.ceil(target/30); if(count >= target) { count = target; clearInterval(interval); } }, 50)">
                        <span x-text="count">0</span>
                    </h3>
                </div>
                <div class="w-12 h-12 bg-purple-500/10 text-purple-400 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-purple-500 group-hover:text-white transition-all">
                    <i class="ri-shopping-bag-3-line"></i>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 text-9xl text-white/5 group-hover:text-purple-500/5 transition-colors pointer-events-none">
                <i class="ri-shopping-bag-3-fill"></i>
            </div>
        </div>

        {{-- Avg Order Value Card --}}
        <div class="glass-card rounded-2xl p-6 relative overflow-hidden group hover:border-emerald-500/50 transition-colors animate-fade-slide-up" style="animation-delay: 0.4s;">
            <div class="flex justify-between items-start mb-4 relative z-10">
                <div>
                    <p class="text-slate-400 text-sm font-medium mb-1">Avg Order Value</p>
                    <h3 class="text-2xl font-bold text-white font-['Plus_Jakarta_Sans']" x-data="{ count: 0 }" x-init="let target = {{ $averageOrderValue }}; let interval = setInterval(() => { count += target/30; if(count >= target) { count = target; clearInterval(interval); } }, 50)">
                        Rp <span x-text="Math.round(count).toLocaleString('id-ID')">0</span>
                    </h3>
                </div>
                <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                    <i class="ri-bar-chart-box-line"></i>
                </div>
            </div>
            <div class="absolute -bottom-6 -right-6 text-9xl text-white/5 group-hover:text-emerald-500/5 transition-colors pointer-events-none">
                <i class="ri-bar-chart-box-fill"></i>
            </div>
        </div>
    </div>

    {{-- Membership Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="glass-card rounded-2xl p-5 border border-amber-500/10">
            <p class="text-slate-400 text-sm font-medium mb-1">Member Aktif</p>
            <h3 class="text-2xl font-bold text-amber-400 font-['Plus_Jakarta_Sans']">{{ number_format($activeMembers, 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-emerald-500/10">
            <p class="text-slate-400 text-sm font-medium mb-1">Poin Earned / Redeemed</p>
            <h3 class="text-xl font-bold text-white font-['Plus_Jakarta_Sans']">{{ number_format($pointsEarned, 0, ',', '.') }} / {{ number_format($pointsRedeemed, 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-blue-500/10">
            <p class="text-slate-400 text-sm font-medium mb-1">Voucher Redeemed</p>
            <h3 class="text-2xl font-bold text-blue-400 font-['Plus_Jakarta_Sans']">{{ number_format($vouchersRedeemed, 0, ',', '.') }}</h3>
        </div>
        <div class="glass-card rounded-2xl p-5 border border-emerald-500/10">
            <p class="text-slate-400 text-sm font-medium mb-1">Diskon Voucher Terpakai</p>
            <h3 class="text-xl font-bold text-emerald-400 font-['Plus_Jakarta_Sans']">Rp {{ number_format($voucherDiscountTotal, 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-500 mt-1">{{ number_format($memberOrders, 0, ',', '.') }} order memakai voucher</p>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-slide-up" style="animation-delay: 0.5s;">
        {{-- Area Chart --}}
        <div class="lg:col-span-2 glass-card rounded-3xl p-6">
            <h2 class="text-lg font-bold font-['Plus_Jakarta_Sans'] text-white mb-6 flex items-center gap-2">
                <i class="ri-line-chart-line text-amber-500"></i> Tren Penjualan
            </h2>
            <div id="salesAreaChart" class="h-80 w-full"></div>
        </div>

        {{-- Bar Chart Top Products --}}
        <div class="glass-card rounded-3xl p-6 flex flex-col">
            <h2 class="text-lg font-bold font-['Plus_Jakarta_Sans'] text-white mb-6 flex items-center gap-2">
                <i class="ri-fire-line text-orange-500"></i> Top 5 Produk Terlaris
            </h2>
            <div id="topProductsBarChart" class="flex-1 w-full min-h-[300px]"></div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="glass-card rounded-3xl p-6 overflow-hidden animate-fade-slide-up" style="animation-delay: 0.6s;">
        <h2 class="text-lg font-bold font-['Plus_Jakarta_Sans'] text-white mb-6 flex items-center gap-2">
            <i class="ri-list-check-2 text-blue-400"></i> Detail Transaksi Terakhir
        </h2>
        
        <div class="overflow-x-auto custom-scrollbar pb-2">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="text-xs uppercase bg-slate-800/50 text-slate-400 border-b border-white/5">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-xl">Order ID</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4 text-center">Items</th>
                        <th class="px-6 py-4 text-right">Voucher</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4 rounded-tr-xl">Metode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($orders as $order)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 font-semibold text-amber-400 group-hover:text-amber-300">#{{ $order->order_number }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 font-medium text-slate-200">{{ $order->user->full_name ?? $order->user->name }}</td>
                            <td class="px-6 py-4 text-center"><span class="bg-slate-800 px-2.5 py-1 rounded-lg text-xs font-semibold">{{ $order->orderItems->count() }}</span></td>
                            <td class="px-6 py-4 text-right">
                                @if(($order->voucher_discount ?? 0) > 0)
                                    <div class="text-emerald-400 font-bold">-Rp {{ number_format($order->voucher_discount, 0, ',', '.') }}</div>
                                    <div class="text-[10px] text-slate-500">{{ $order->voucher_code }}</div>
                                @else
                                    <span class="text-slate-600">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-white">Rp {{ number_format($order->total_bayar, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                    {{ $order->metodePembayaran->nama_pembayaran ?? $order->metodePembayaran->nama ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <i class="ri-inbox-archive-line text-4xl block mb-2 opacity-50"></i>
                                Belum ada transaksi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function applyPeriodFilter(period) {
        window.location.href = "{{ route('admin.reports.sales') }}?period=" + period;
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Data for Area Chart
        const chartData = @json($chartData);
        const areaLabels = chartData.map(item => item.date);
        const areaData = chartData.map(item => item.total);

        const areaOptions = {
            series: [{ name: 'Revenue', data: areaData }],
            chart: {
                type: 'area',
                height: '100%',
                fontFamily: 'Nunito, sans-serif',
                toolbar: { show: false },
                background: 'transparent',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: { enabled: true, delay: 150 },
                    dynamicAnimation: { enabled: true, speed: 350 }
                }
            },
            colors: ['#f59e0b'], // Amber 500
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [0, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: areaLabels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#94a3b8' } } // slate 400
            },
            yaxis: {
                labels: {
                    style: { colors: '#94a3b8' },
                    formatter: (value) => { return "Rp " + value.toLocaleString('id-ID'); }
                }
            },
            grid: {
                borderColor: 'rgba(255,255,255,0.05)',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                xaxis: { lines: { show: false } },
            },
            theme: { mode: 'dark' },
            tooltip: {
                theme: 'dark',
                y: { formatter: function (val) { return "Rp " + val.toLocaleString('id-ID') } }
            }
        };

        const areaChart = new ApexCharts(document.querySelector("#salesAreaChart"), areaOptions);
        areaChart.render();

        // Data for Bar Chart Top Products
        const topProductsData = @json($topProducts);
        // Only take top 5
        const top5 = topProductsData.slice(0, 5);
        const barLabels = top5.map(item => {
            let name = item.produk ? item.produk.nama_produk : 'Unknown';
            return name.length > 20 ? name.substring(0,20) + '...' : name;
        });
        const barData = top5.map(item => item.total_sold);

        const barOptions = {
            series: [{ name: 'Terjual', data: barData }],
            chart: {
                type: 'bar',
                height: '100%',
                fontFamily: 'Nunito, sans-serif',
                toolbar: { show: false },
                background: 'transparent',
                animations: { enabled: true, easing: 'easeinout', speed: 800 }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 6,
                    barHeight: '50%',
                    dataLabels: { position: 'bottom' }
                }
            },
            colors: ['#f97316'], // Orange 500
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: { colors: ['#fff'] },
                formatter: function (val, opt) { return val + " pcs" },
                offsetX: 0,
            },
            xaxis: {
                categories: barLabels,
                labels: { show: false },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#e2e8f0', fontSize: '12px', fontWeight: 600 }
                }
            },
            grid: { show: false },
            theme: { mode: 'dark' },
            tooltip: {
                theme: 'dark',
                y: { formatter: function (val) { return val + " items" } }
            }
        };

        const barChart = new ApexCharts(document.querySelector("#topProductsBarChart"), barOptions);
        barChart.render();
    });
</script>
@endpush
@endsection
