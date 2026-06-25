<header id="adminNavbar" class="h-20 border-b flex items-center justify-between px-6 z-20 sticky top-0 shadow-sm transition-colors duration-300 admin-navbar">
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="admin-icon-btn transition-colors p-2 rounded-lg">
            <i class="ri-menu-2-line text-2xl"></i>
        </button>
        <h2 class="text-xl font-bold font-['Plus_Jakarta_Sans'] admin-title tracking-wide">
            @yield('title', 'Dashboard')
        </h2>
    </div>

    <div class="flex items-center gap-4">
        {{-- Search Bar --}}
        <div class="relative hidden md:block group">
            <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-amber-400 transition-colors"></i>
            <input type="text" placeholder="Cari di dashboard..." 
                   class="admin-search-input rounded-full py-2.5 pl-10 pr-4 text-sm focus:outline-none focus:ring-1 focus:ring-amber-500/50 w-64 transition-all placeholder-slate-500 shadow-inner">
        </div>

        {{-- Theme Toggle --}}
        <button id="themeToggleBtn" class="admin-icon-btn transition-colors p-2 rounded-lg" title="Toggle Theme">
            <i id="themeIcon" class="text-2xl"></i>
        </button>

        {{-- Notification --}}
        <a href="{{ route('admin.notifications.index') }}" class="relative admin-icon-btn transition-colors p-2 rounded-lg">
            <i class="ri-notification-3-line text-2xl"></i>
            <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full animate-pulse border-2 admin-notif-border">3</span>
        </a>

        {{-- User Menu --}}
        <div class="flex items-center gap-3 cursor-pointer group relative" x-data="{ userMenu: false }" @click.away="userMenu = false">
            <div class="text-right hidden sm:block" @click="userMenu = !userMenu">
                <p class="text-sm font-bold admin-title group-hover:text-amber-400 transition-colors">{{ auth()->user()->full_name ?? 'Admin' }}</p>
                <p class="text-xs text-slate-400 font-medium">Administrator</p>
            </div>
            <div @click="userMenu = !userMenu" class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white font-bold text-lg shadow-lg ring-2 ring-transparent group-hover:ring-amber-500/50 transition-all">
                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
            </div>

            {{-- Dropdown --}}
            <div x-show="userMenu" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 class="absolute right-0 top-14 w-56 admin-dropdown border rounded-xl shadow-2xl py-2 z-50 overflow-hidden" style="display:none;">
                <div class="px-4 py-3 border-b admin-border sm:hidden">
                    <p class="text-sm font-bold admin-title">{{ auth()->user()->full_name ?? 'Admin' }}</p>
                    <p class="text-xs text-slate-400">Administrator</p>
                </div>
                <a href="#" class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium admin-menu-item hover:text-white transition-colors">
                    <i class="ri-user-settings-line text-lg text-slate-400"></i> Pengaturan Profil
                </a>
                <div class="border-t admin-border my-1"></div>
                <a href="{{ route('auth.logout') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
                    <i class="ri-logout-box-line text-lg"></i> Logout
                </a>
            </div>
        </div>
    </div>
</header>

<style>
    /* ===== DARK MODE (default) ===== */
    .admin-navbar {
        background: rgba(30, 41, 59, 0.92);
        backdrop-filter: blur(14px);
        border-color: rgba(255,255,255,0.08);
    }
    .admin-icon-btn { color: #94a3b8; }
    .admin-icon-btn:hover { color: #f59e0b; background: rgba(255,255,255,0.05); }
    .admin-title { color: #f1f5f9; }
    .admin-search-input {
        background: rgba(30, 41, 59, 0.8);
        border: 1px solid rgba(255,255,255,0.1);
        color: #e2e8f0;
    }
    .admin-notif-border { border-color: #1e293b; }
    .admin-dropdown {
        background: #1e293b;
        border-color: rgba(255,255,255,0.1);
    }
    .admin-border { border-color: rgba(255,255,255,0.05); }
    .admin-menu-item { color: #94a3b8; }
    .admin-menu-item:hover { background: rgba(255,255,255,0.05); }

    /* ===== LIGHT MODE overrides ===== */
    .light-mode .admin-navbar {
        background: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(14px);
        border-color: rgba(0, 0, 0, 0.08) !important;
        box-shadow: 0 1px 16px -4px rgba(0,0,0,0.10) !important;
    }
    .light-mode .admin-icon-btn { color: #64748b !important; }
    .light-mode .admin-icon-btn:hover { color: #f59e0b !important; background: rgba(0,0,0,0.05) !important; }
    .light-mode .admin-title { color: #0f172a !important; }
    .light-mode .admin-search-input {
        background: #f1f5f9 !important;
        border: 1px solid #e2e8f0 !important;
        color: #1e293b !important;
    }
    .light-mode .admin-search-input::placeholder { color: #94a3b8 !important; }
    .light-mode .admin-notif-border { border-color: #ffffff !important; }
    .light-mode .admin-dropdown {
        background: #ffffff !important;
        border-color: rgba(0, 0, 0, 0.08) !important;
        box-shadow: 0 8px 32px -8px rgba(0,0,0,0.15) !important;
    }
    .light-mode .admin-border { border-color: rgba(0,0,0,0.06) !important; }
    .light-mode .admin-menu-item { color: #475569 !important; }
    .light-mode .admin-menu-item:hover { background: rgba(0,0,0,0.04) !important; }
    .light-mode .text-slate-400 { color: #64748b !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const themeBtn = document.getElementById('themeToggleBtn');
        const themeIcon = document.getElementById('themeIcon');
        const htmlElement = document.documentElement;
        
        function updateIcon() {
            if (htmlElement.classList.contains('light-mode')) {
                themeIcon.className = 'ri-sun-line text-2xl text-amber-500';
            } else {
                themeIcon.className = 'ri-moon-line text-2xl text-slate-400';
            }
        }
        
        updateIcon();
        
        themeBtn.addEventListener('click', () => {
            htmlElement.classList.toggle('light-mode');
            localStorage.setItem('adminTheme', htmlElement.classList.contains('light-mode') ? 'light' : 'dark');
            updateIcon();
        });
    });
</script>
