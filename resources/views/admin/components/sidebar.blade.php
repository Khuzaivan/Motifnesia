<aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="admin-sidebar h-screen border-r transition-all duration-300 flex flex-col relative z-30 shrink-0">
    <div class="h-20 flex items-center justify-center border-b admin-sidebar-border px-4">
        <h1 class="text-amber-500 font-bold text-2xl font-['Plus_Jakarta_Sans'] whitespace-nowrap overflow-hidden transition-opacity duration-300" x-show="sidebarOpen" style="display:none;">
            M <span class="admin-sidebar-title text-lg ml-1">- Motifnesia</span>
        </h1>
        <h1 class="text-amber-500 font-bold text-2xl font-['Plus_Jakarta_Sans'] transition-opacity duration-300" x-show="!sidebarOpen" style="display:none;">
            M
        </h1>
    </div>

    <div class="flex-1 overflow-y-auto py-6 px-3 flex flex-col gap-2 custom-scrollbar">
        @php
            $allMenus = [
                ['page' => 'productManagement', 'icon' => 'ri-store-2-line', 'label' => 'Manajemen Produk', 'url' => route('admin.product.management.index')],
                ['page' => 'suppliers', 'icon' => 'ri-building-4-line', 'label' => 'Supplier', 'url' => route('admin.suppliers.index')],
                ['page' => 'stock-procurements', 'icon' => 'ri-clipboard-line', 'label' => 'Pengadaan Stok', 'url' => route('admin.stock-procurements.index')],
                ['page' => 'warehouse', 'icon' => 'ri-archive-stack-line', 'label' => 'Gudang', 'url' => route('admin.warehouse.index')],
                ['page' => 'stock-opname', 'icon' => 'ri-file-list-3-line', 'label' => 'Stock Opname', 'url' => route('admin.stock-opname.index')],
                ['page' => 'productReviews', 'icon' => 'ri-star-line', 'label' => 'Ulasan Produk', 'url' => route('admin.reviews.index')],
                ['page' => 'orderStatus', 'icon' => 'ri-truck-line', 'label' => 'Status Pengiriman', 'url' => route('admin.orders.status')],
                ['page' => 'returns', 'icon' => 'ri-arrow-go-back-line', 'label' => 'Kelola Retur', 'url' => route('admin.returns.index')],
                ['page' => 'salesReport', 'icon' => 'ri-bar-chart-line', 'label' => 'Laporan Penjualan', 'url' => route('admin.reports.sales')],
                ['page' => 'customers', 'icon' => 'ri-group-line', 'label' => 'Daftar Pelanggan', 'url' => route('admin.customers.index')],
                ['page' => 'memberships', 'icon' => 'ri-vip-crown-line', 'label' => 'Daftar Member', 'url' => route('admin.memberships.index')],
                ['page' => 'membership-rewards', 'icon' => 'ri-gift-line', 'label' => 'Reward Membership', 'url' => route('admin.membership-rewards.index')],
                ['page' => 'membership-broadcast', 'icon' => 'ri-megaphone-line', 'label' => 'Broadcast Member', 'url' => route('admin.membership-broadcast.index')],
                ['page' => 'chat', 'icon' => 'ri-chat-3-line', 'label' => 'Live Chat Support', 'url' => route('admin.chat.index')],
                ['page' => 'notifications', 'icon' => 'ri-notification-3-line', 'label' => 'Notifikasi Sistem', 'url' => route('admin.notifications.index')],
                ['page' => 'kontenStatis', 'icon' => 'ri-pages-line', 'label' => 'Kelola Konten Statis', 'url' => route('admin.konten.index')],
            ];

            $menus = [];
            foreach ($allMenus as $m) {
                $allowed = true;
                if ($m['page'] === 'productManagement') {
                    $allowed = Gate::allows('is-kasir');
                } elseif ($m['page'] === 'suppliers') {
                    $allowed = Gate::allows('is-owner');
                } elseif (in_array($m['page'], ['stock-procurements', 'warehouse', 'stock-opname'])) {
                    $allowed = Gate::allows('is-gudang');
                } elseif ($m['page'] === 'productReviews') {
                    $allowed = Gate::allows('is-kasir');
                } elseif ($m['page'] === 'returns') {
                    $allowed = Gate::allows('is-kasir');
                } elseif ($m['page'] === 'salesReport') {
                    $allowed = Gate::allows('is-finance');
                } elseif ($m['page'] === 'customers') {
                    $allowed = Gate::allows('is-owner');
                } elseif (in_array($m['page'], ['memberships', 'membership-rewards', 'membership-broadcast'])) {
                    $allowed = Gate::allows('is-owner');
                } elseif ($m['page'] === 'chat') {
                    $allowed = Gate::allows('is-kasir');
                } elseif ($m['page'] === 'kontenStatis') {
                    $allowed = Gate::allows('is-owner');
                }
                
                if ($allowed) {
                    $menus[] = $m;
                }
            }
            
            $currentRoute = request()->route()->getName();
            $detectedActivePage = $activePage ?? 'default';
            if ($detectedActivePage === 'default') {
                foreach($menus as $m) {
                    if (strpos($currentRoute, str_replace('admin.', '', $m['url'])) !== false || request()->url() === $m['url']) {
                        $detectedActivePage = $m['page'];
                        break;
                    }
                }
            }
        @endphp

        @foreach($menus as $menu)
            @php $isActive = ($detectedActivePage === $menu['page']); @endphp
            <a href="{{ $menu['url'] }}" 
               class="flex items-center gap-4 px-3 py-3 rounded-xl transition-all duration-200 group relative overflow-hidden admin-menu-link {{ $isActive ? 'admin-menu-active' : 'admin-menu-inactive' }}"
               title="{{ $menu['label'] }}">
                @if($isActive)
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-400 rounded-r-full"></div>
                @endif
                <i class="{{ $menu['icon'] }} text-xl shrink-0 z-10 transition-transform group-hover:scale-110 {{ $isActive ? 'text-amber-400' : '' }}"></i>
                <span class="font-semibold text-sm whitespace-nowrap z-10" x-show="sidebarOpen" style="display:none;">{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </div>

    <div class="p-4 border-t admin-sidebar-border">
        <a href="{{ route('auth.logout') }}" class="flex items-center gap-4 px-3 py-3 rounded-xl text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all duration-200 group relative">
            <i class="ri-logout-box-line text-xl shrink-0 group-hover:scale-110 transition-transform"></i>
            <span class="font-semibold text-sm whitespace-nowrap" x-show="sidebarOpen" style="display:none;">Logout</span>
        </a>
    </div>
</aside>

<style>
    /* ===== DARK (default) ===== */
    .admin-sidebar {
        background-color: #0a0f1e;
        border-color: rgba(255,255,255,0.08);
    }
    .admin-sidebar-border { border-color: rgba(255,255,255,0.08); }
    .admin-sidebar-title { color: #f1f5f9; }
    .admin-menu-link { }
    .admin-menu-active { background: rgba(245,158,11,0.10); color: #fbbf24; }
    .admin-menu-inactive { color: #94a3b8; }
    .admin-menu-inactive:hover { background: rgba(255,255,255,0.05); color: #e2e8f0; }

    /* ===== LIGHT MODE overrides ===== */
    .light-mode .admin-sidebar {
        background-color: #ffffff !important;
        border-color: #e2e8f0 !important;
        box-shadow: 4px 0 24px -8px rgba(0,0,0,0.08) !important;
    }
    .light-mode .admin-sidebar-border { border-color: #e2e8f0 !important; }
    .light-mode .admin-sidebar-title { color: #1e293b !important; }
    .light-mode .admin-menu-active {
        background: rgba(245,158,11,0.12) !important;
        color: #d97706 !important;
    }
    .light-mode .admin-menu-active i { color: #d97706 !important; }
    .light-mode .admin-menu-inactive { color: #64748b !important; }
    .light-mode .admin-menu-inactive:hover {
        background: rgba(0,0,0,0.05) !important;
        color: #0f172a !important;
    }
</style>
