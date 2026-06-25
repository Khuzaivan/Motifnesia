<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Motifnesia — Koleksi Batik Nusantara Premium</title>
    <link rel="icon" type="image/png" href="{{ asset('images/motifnesia_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"/>

    {{-- Tailwind CSS (via Vite) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        (function () {
            try {
                if (localStorage.getItem('customerTheme') === 'light') {
                    document.documentElement.classList.add('customer-light');
                }
            } catch (e) {}
        })();
    </script>

    {{-- local css (pakai asset) - akan dihapus bertahap saat migrate ke Tailwind --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/partials/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/partials/slideShow.css') }}">
    <link rel="stylesheet" href="{{ asset('css/detailProduk.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shoppingCart.css') }}">
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/paymentConfirmation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/favorites.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications.css') }}">
    <link rel="stylesheet" href="{{ asset('css/userProfile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/editProfile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modals.css') }}">
    <script src="{{ asset('JS/modal.js') }}"></script>
    <style>
        :root {
            --clr-gold: #c9a84c;
            --clr-gold-dark: #a8832d;
            --clr-gold-light: #e8d5a3;
            --clr-ivory: #f5f0e8;
            --clr-dark: #1c1a16;
            --clr-text: #3d3730;
            --clr-muted: #8a7d6b;
            --customer-bg: #111111;
            --customer-surface: #1e1e1e;
            --customer-surface-2: #181818;
            --customer-border: rgba(255,255,255,.08);
            --customer-text: #f5f0e8;
            --customer-muted: rgba(255,255,255,.55);
        }
        html.customer-light {
            color-scheme: light;
            --customer-bg: #f6efe4;
            --customer-surface: #fffaf2;
            --customer-surface-2: #f1e6d6;
            --customer-border: rgba(78, 61, 37, .16);
            --customer-text: #241f18;
            --customer-muted: #746858;
        }
        * { font-family: 'Poppins', sans-serif; }
        .font-serif, h1.font-serif, h2.font-serif { font-family: 'Playfair Display', serif; }
        body {
            background-color: #111111;
            min-height: 100vh;
            color: #f5f0e8;
            position: relative;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }
        /* Ensure content sits above background pseudo-elements */
        main.page-content { position: relative; z-index: 1; flex: 1; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: var(--clr-gold); border-radius: 99px; }
        /* Page fade-in transition */
        .page-content { animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        /* Gold underline hover utility */
        .hover-underline-gold { position: relative; }
        .hover-underline-gold::after {
            content: '';
            position: absolute;
            bottom: -2px; left: 0;
            width: 0; height: 2px;
            background: var(--clr-gold);
            transition: width 0.3s ease;
        }
        .hover-underline-gold:hover::after { width: 100%; }
        /* Gold pill button */
        .btn-gold {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--clr-gold);
            color: #fff;
            padding: 10px 28px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            transition: background 0.25s, transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 15px rgba(201,168,76,0.3);
        }
        .btn-gold:hover {
            background: var(--clr-gold-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(201,168,76,0.4);
            color: #fff;
            text-decoration: none;
        }

        html.customer-light body {
            background:
                radial-gradient(circle at 12% 8%, rgba(201,168,76,.14), transparent 28%),
                radial-gradient(circle at 82% 24%, rgba(168,131,45,.08), transparent 24%),
                var(--customer-bg) !important;
            color: var(--customer-text) !important;
        }
        html.customer-light main.page-content {
            background:
                linear-gradient(rgba(246,239,228,.9), rgba(246,239,228,.96)),
                repeating-linear-gradient(135deg, rgba(168,131,45,.045) 0 1px, transparent 1px 26px);
        }

        .customer-theme-toggle {
            width: 38px;
            height: 38px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.05);
            color: rgba(255,255,255,.72);
            transition: all .25s ease;
            cursor: pointer;
        }
        .customer-theme-toggle:hover {
            color: var(--clr-gold);
            border-color: rgba(201,168,76,.34);
            background: rgba(201,168,76,.1);
            transform: translateY(-1px);
        }
        .customer-theme-toggle .theme-icon-moon { display: none; }
        html.customer-light .customer-theme-toggle {
            background: rgba(36,31,24,.05);
            border-color: rgba(78,61,37,.16);
            color: #5f5548;
        }
        html.customer-light .customer-theme-toggle .theme-icon-sun { display: none; }
        html.customer-light .customer-theme-toggle .theme-icon-moon { display: block; }

        html.customer-light .customer-navbar-shell {
            background: rgba(255,250,242,.94) !important;
            border-color: var(--customer-border) !important;
            box-shadow: 0 14px 38px rgba(78,61,37,.14) !important;
        }
        html.customer-light .customer-navbar-shell [class*="text-white"] { color: var(--customer-text) !important; }
        html.customer-light .customer-navbar-shell [class*="bg-white/5"],
        html.customer-light .customer-navbar-shell form > div {
            background-color: rgba(36,31,24,.04) !important;
            border-color: var(--customer-border) !important;
        }
        html.customer-light .customer-navbar-shell input {
            color: var(--customer-text) !important;
        }
        html.customer-light .customer-navbar-shell input::placeholder { color: rgba(36,31,24,.42) !important; }
        html.customer-light #live-search-dropdown {
            background: #fffaf2 !important;
            border-color: var(--customer-border) !important;
            box-shadow: 0 20px 55px rgba(78,61,37,.2) !important;
        }
        html.customer-light #live-search-dropdown a {
            border-color: rgba(78,61,37,.08) !important;
        }
        html.customer-light #live-search-dropdown [class*="text-white"] { color: var(--customer-text) !important; }

        html.customer-light main.page-content [style*="background:#131313"],
        html.customer-light main.page-content [style*="background: #131313"],
        html.customer-light main.page-content [style*="background:#111"],
        html.customer-light main.page-content [style*="background:#111111"] {
            background: transparent !important;
        }
        html.customer-light main.page-content [style*="background:#1e1e1e"],
        html.customer-light main.page-content [style*="background: #1e1e1e"],
        html.customer-light main.page-content [style*="background:#181818"],
        html.customer-light main.page-content [style*="background: #181818"],
        html.customer-light main.page-content [style*="background:#1a1a1a"],
        html.customer-light main.page-content [style*="background:rgba(255,255,255,.03)"],
        html.customer-light main.page-content [style*="background:rgba(255,255,255,.035)"],
        html.customer-light main.page-content [style*="background:rgba(255,255,255,0.03)"],
        html.customer-light main.page-content [class*="bg-[#181818]"] {
            background: var(--customer-surface) !important;
            border-color: var(--customer-border) !important;
            box-shadow: 0 12px 36px rgba(78,61,37,.08);
        }
        html.customer-light main.page-content [style*="border:1px solid rgba(255,255,255"],
        html.customer-light main.page-content [style*="border-bottom:1px solid rgba(255,255,255"],
        html.customer-light main.page-content [class*="border-white/"] {
            border-color: var(--customer-border) !important;
        }
        html.customer-light main.page-content [style*="color:#fff"],
        html.customer-light main.page-content [style*="color: #fff"],
        html.customer-light main.page-content [style*="color:white"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,.9)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,.92)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,.85)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,.8)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,.75)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,.7)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,0.92)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,0.9)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,0.85)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,0.8)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,0.75)"] {
            color: var(--customer-text) !important;
        }
        html.customer-light main.page-content [style*="color:rgba(255,255,255,.65)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,.6)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,.55)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,.5)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,.45)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,.4)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,.35)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,0.65)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,0.6)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,0.55)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,0.5)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,0.45)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,0.4)"],
        html.customer-light main.page-content [style*="color:rgba(255,255,255,0.35)"] {
            color: var(--customer-muted) !important;
        }
        html.customer-light main.page-content input:not([type="checkbox"]):not([type="radio"]),
        html.customer-light main.page-content select,
        html.customer-light main.page-content textarea {
            background: #fffdf8 !important;
            border-color: var(--customer-border) !important;
            color: var(--customer-text) !important;
            color-scheme: light;
        }
        html.customer-light main.page-content input::placeholder,
        html.customer-light main.page-content textarea::placeholder { color: rgba(36,31,24,.42) !important; }
        html.customer-light main.page-content option {
            background: #fffaf2 !important;
            color: var(--customer-text) !important;
        }

        html.customer-light .trust-badge,
        html.customer-light #filterForm,
        html.customer-light .motif-product-card {
            background: var(--customer-surface) !important;
            border-color: var(--customer-border) !important;
            color: var(--customer-text) !important;
            box-shadow: 0 12px 34px rgba(78,61,37,.09) !important;
        }
        html.customer-light .trust-badge [class*="text-white"],
        html.customer-light #filterForm [class*="text-white"],
        html.customer-light #koleksi [class*="text-white"],
        html.customer-light .motif-product-card h3 {
            color: var(--customer-text) !important;
        }
        html.customer-light .trust-badge [class*="text-white/5"],
        html.customer-light #filterForm [class*="text-white/5"],
        html.customer-light #koleksi [class*="text-white/5"] {
            color: var(--customer-muted) !important;
        }
        html.customer-light #filterForm select {
            background-color: #fffdf8 !important;
            color: var(--customer-text) !important;
            border-color: var(--customer-border) !important;
        }
        html.customer-light #filterForm label:hover span,
        html.customer-light #filterForm .group:hover span { color: var(--clr-gold-dark) !important; }
        html.customer-light .motif-product-card > div:last-child {
            background: var(--customer-surface) !important;
            border-top-color: var(--customer-border) !important;
        }
        html.customer-light .motif-product-card [style*="color:rgba(255,255,255,0.46)"],
        html.customer-light .motif-product-card [style*="color:rgba(255,255,255,0.38)"] {
            color: var(--customer-muted) !important;
        }
        html.customer-light .motif-product-card .pc-fade,
        html.customer-light .motif-product-card .pc-fade [style*="color:white"],
        html.customer-light .motif-product-card [style*="background:rgba(0,0,0"] {
            color: #fff !important;
        }

        html.customer-light footer {
            background: #fff7ec !important;
            color: var(--customer-text) !important;
            border-top: 1px solid var(--customer-border);
        }
        html.customer-light footer svg path { fill: #fff7ec !important; }
        html.customer-light footer [class*="text-white"] { color: var(--customer-text) !important; }
        html.customer-light footer [class*="text-[#8a7d6b]"],
        html.customer-light footer p,
        html.customer-light footer a:not(:hover) { color: var(--customer-muted) !important; }
        html.customer-light footer input {
            background: #fffdf8 !important;
            border-color: var(--customer-border) !important;
            color: var(--customer-text) !important;
        }

        html.customer-light .modal-content,
        html.customer-light .modal-body {
            background: var(--customer-surface) !important;
            color: var(--customer-text) !important;
        }
    </style>
</head>
<body>
    <main class="page-content">
        @include('customer.partials.navbar')

        {{-- content dari setiap halaman --}}
        @yield('container') {{-- JANGAN GANTI INI --}}
    </main>

    {{-- Footer --}}
    @include('customer.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <script>
    // ==================== CUSTOMER THEME ====================
    (function() {
        const root = document.documentElement;

        function applyCustomerTheme(theme) {
            const normalized = theme === 'light' ? 'light' : 'dark';
            root.classList.toggle('customer-light', normalized === 'light');
            root.dataset.customerTheme = normalized;

            document.querySelectorAll('[data-customer-theme-toggle]').forEach(button => {
                button.setAttribute('aria-label', normalized === 'light' ? 'Aktifkan mode gelap' : 'Aktifkan mode terang');
                button.setAttribute('title', normalized === 'light' ? 'Aktifkan mode gelap' : 'Aktifkan mode terang');
            });
        }

        let savedTheme = 'dark';
        try {
            savedTheme = localStorage.getItem('customerTheme') || 'dark';
        } catch (e) {}

        applyCustomerTheme(savedTheme);

        document.querySelectorAll('[data-customer-theme-toggle]').forEach(button => {
            button.addEventListener('click', function() {
                const nextTheme = root.classList.contains('customer-light') ? 'dark' : 'light';
                try {
                    localStorage.setItem('customerTheme', nextTheme);
                } catch (e) {}
                applyCustomerTheme(nextTheme);
            });
        });
    })();

    // ==================== ADD TO FAVORITE ====================
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const FAV_STORE_URL = "{{ route('customer.favorites.store') }}";

    function addToFavorite(btn) {
        const productId = btn.dataset.productId;
        btn.disabled = true;

        fetch(FAV_STORE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ produk_id: productId })
        })
        .then(r => r.json())
        .then(data => {
            // Visual feedback: fill the heart gold
            btn.classList.add('bg-[#c9a84c]', 'border-transparent');
            btn.classList.remove('bg-black/50', 'border-white/10');
            const svg = btn.querySelector('svg path');
            if (svg) svg.setAttribute('fill', 'currentColor');

            // Toast notification
            showToast(data.message ?? 'Ditambahkan ke favorit!', 'success');
        })
        .catch(() => {
            showToast('Gagal menambahkan ke favorit.', 'error');
        })
        .finally(() => {
            btn.disabled = false;
        });
    }

    function showToast(message, type) {
        const existing = document.getElementById('fav-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'fav-toast';
        toast.className = `fixed bottom-6 right-6 z-[9999] flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl text-white text-sm font-semibold backdrop-blur-md border transition-all duration-300 translate-y-10 opacity-0 ${type === 'success' ? 'bg-[#1a1a1a]/90 border-[#c9a84c]/40' : 'bg-red-900/80 border-red-500/40'}`;
        toast.innerHTML = `
            <span class="text-lg">${type === 'success' ? '❤️' : '⚠️'}</span>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-10', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
        });
        setTimeout(() => {
            toast.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }

    // ==================== LIVE SEARCH ====================
    (function() {
        const input     = document.getElementById('live-search-input');
        const dropdown  = document.getElementById('live-search-dropdown');
        const results   = document.getElementById('live-search-results');
        const loading   = document.getElementById('live-search-loading');
        const empty     = document.getElementById('live-search-empty');

        if (!input || !dropdown) return;

        const url = input.dataset.url;
        let timer = null;

        function formatRupiah(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        }

        function showDropdown() { dropdown.classList.remove('hidden'); }
        function hideDropdown() { dropdown.classList.add('hidden'); }

        function renderResults(items) {
            loading.classList.add('hidden');
            if (!items.length) {
                results.innerHTML = '';
                empty.classList.remove('hidden');
                return;
            }
            empty.classList.add('hidden');
            results.innerHTML = items.map(item => `
                <a href="${item.url}"
                   class="flex items-center gap-3 px-4 py-3 hover:bg-white/5 transition-colors duration-150 border-b border-white/5 last:border-0 group">
                    <img src="${item.gambar}" alt="${item.nama}"
                         class="w-12 h-12 rounded-xl object-cover border border-white/10 shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-white/90 text-sm font-semibold truncate group-hover:text-[#c9a84c] transition-colors">${item.nama}</p>
                        <p class="text-[#c9a84c] text-xs font-bold mt-0.5">${formatRupiah(item.harga)}</p>
                    </div>
                    <svg class="w-4 h-4 text-white/20 group-hover:text-[#c9a84c] transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            `).join('');
        }

        input.addEventListener('input', function() {
            const q = this.value.trim();
            clearTimeout(timer);

            if (q.length < 2) {
                hideDropdown();
                return;
            }

            showDropdown();
            results.innerHTML = '';
            empty.classList.add('hidden');
            loading.classList.remove('hidden');

            timer = setTimeout(() => {
                fetch(`${url}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => renderResults(data))
                    .catch(() => { loading.classList.add('hidden'); });
            }, 280);
        });

        // Hide dropdown when click outside
        document.addEventListener('click', function(e) {
            if (!document.getElementById('search-wrapper')?.contains(e.target)) {
                hideDropdown();
            }
        });

        // Show again on focus if has value
        input.addEventListener('focus', function() {
            if (this.value.trim().length >= 2 && results.innerHTML) {
                showDropdown();
            }
        });

        // Keyboard navigation (Escape to close)
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') hideDropdown();
        });
    })();
    </script>
</body>
</html>
