{{-- Navbar dengan Tailwind - Fixed Top --}}
<div class="fixed top-0 left-0 right-0 z-50 px-4 md:px-6 py-3 pointer-events-none">
    <header class="customer-navbar-shell w-full max-w-[1400px] mx-auto bg-[#181818]/95 backdrop-blur-md border border-white/10 shadow-[0_4px_30px_rgba(0,0,0,0.5)] rounded-2xl transition-all duration-300 pointer-events-auto">
        <div class="flex items-center justify-between h-16 px-4 md:px-6">
            {{-- Logo --}}
            <a href="{{ route('customer.home') }}" class="flex items-center gap-2 md:gap-3 group transition-all duration-300">
                <img src="{{ asset('images/motifnesia_logo.png') }}" alt="Motifnesia Logo" class="w-8 h-8 md:w-10 md:h-10 rounded-xl shadow-md group-hover:scale-105 transition-transform duration-300 object-cover border border-[#c9a84c]/30">
                <span class="text-lg md:text-xl font-bold tracking-wide text-white" style="font-family:'Playfair Display',serif">Motifnesia<span class="text-[#c9a84c]">.</span></span>
            </a>

            {{-- Live Search Bar (Hidden on Mobile) --}}
            <div class="hidden md:flex flex-1 max-w-lg mx-6 lg:mx-10 relative" id="search-wrapper">
                <form action="{{ route('customer.home') }}" method="GET" class="w-full" id="search-form" autocomplete="off">
                    <div class="flex items-center w-full bg-white/5 border border-white/10 rounded-full overflow-hidden focus-within:border-[#c9a84c] focus-within:bg-white/10 transition-all duration-300">
                        <div class="pl-4 pr-2 text-white/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text"
                               id="live-search-input"
                               name="search"
                               placeholder="Cari koleksi batik..."
                               value="{{ request('search') }}"
                               class="flex-1 px-2 py-2.5 text-sm bg-transparent text-white placeholder-white/40 focus:outline-none w-full"
                               data-url="{{ route('customer.search.live') }}">
                        <button type="submit"
                                class="px-6 py-2.5 bg-gradient-to-r from-[#c9a84c] to-[#a8832d] hover:from-[#d4b45c] hover:to-[#b8933d] text-white text-xs font-semibold uppercase tracking-wider transition-all duration-300 shadow-[0_0_15px_rgba(201,168,76,0.3)]">
                            Cari
                        </button>
                    </div>
                </form>

                {{-- Live Search Dropdown --}}
                <div id="live-search-dropdown"
                     class="absolute top-full left-0 right-0 mt-2 bg-[#1a1a1a] border border-white/10 rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.6)] overflow-hidden z-50 hidden">
                    <div id="live-search-results"></div>
                    <div id="live-search-loading" class="hidden px-4 py-3 text-white/40 text-sm text-center">
                        <svg class="w-4 h-4 animate-spin inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Mencari...
                    </div>
                    <div id="live-search-empty" class="hidden px-4 py-4 text-white/40 text-sm text-center">
                        <svg class="w-8 h-8 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Produk tidak ditemukan
                    </div>
                </div>
            </div>

            {{-- Navigation Menu --}}
            <nav class="flex items-center gap-3 md:gap-5">
                <a href="{{ route('customer.home') }}" 
                   class="hidden lg:block text-sm font-medium text-white/70 hover:text-[#c9a84c] transition-colors duration-300 uppercase tracking-wider">
                    Home
                </a>
                @if (Auth::check())
                    <a href="{{ route('customer.membership.index') }}"
                       class="hidden lg:block text-sm font-medium text-white/70 hover:text-[#c9a84c] transition-colors duration-300 uppercase tracking-wider">
                        Membership
                    </a>
                @endif
                
                {{-- Mobile Search Icon --}}
                <a href="{{ route('customer.home') }}" 
                   class="md:hidden text-white/70 hover:text-[#c9a84c] hover:scale-110 transition-all duration-300">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </a>

                <a href="{{ route('customer.chat.index') }}" 
                   class="text-white/70 hover:text-[#c9a84c] hover:scale-110 transition-all duration-300">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </a>
                <a href="{{ route('customer.notifications.index') }}" 
                   class="text-white/70 hover:text-[#c9a84c] hover:scale-110 transition-all duration-300">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </a>
                <a href="{{ route('customer.cart.index') }}" 
                   class="text-white/70 hover:text-[#c9a84c] hover:scale-110 transition-all duration-300">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </a>
                <a href="{{ route('customer.favorites.index') }}" 
                   class="hidden sm:block text-white/70 hover:text-[#c9a84c] hover:scale-110 transition-all duration-300">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </a>

                <button type="button"
                        class="customer-theme-toggle"
                        data-customer-theme-toggle
                        title="Ganti mode tampilan"
                        aria-label="Ganti mode tampilan">
                    <svg class="theme-icon-sun w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 3v2.25m0 13.5V21m8.25-9h-2.25M6 12H3.75m14.78-6.53-1.59 1.59M7.06 16.94l-1.59 1.59m13.06 0-1.59-1.59M7.06 7.06 5.47 5.47M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
                    </svg>
                    <svg class="theme-icon-moon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M21 12.79A8.25 8.25 0 1 1 11.21 3 6.75 6.75 0 0 0 21 12.79Z"/>
                    </svg>
                </button>

                <div class="h-8 w-px bg-white/20 mx-1 hidden md:block"></div>

                @if (!Auth::check())
                    <a href="{{ route('auth.login') }}" 
                       class="bg-[#c9a84c] hover:bg-[#a8832d] text-white rounded-full no-underline text-xs md:text-sm px-4 py-2 md:px-6 md:py-2.5 font-semibold transition-all shadow-[0_0_10px_rgba(201,168,76,0.3)]">
                        Masuk
                    </a>
                @else
                    <a href="{{ route('customer.profile.index') }}" 
                       class="block hover:scale-105 transition-transform duration-300 group ml-1 md:ml-0">
                        <img src="{{ Auth::user()->profile_pic_url }}" 
                             alt="Profile" 
                             class="w-8 h-8 md:w-10 md:h-10 rounded-full object-cover border-2 border-white/20 group-hover:border-[#c9a84c] shadow-[0_0_10px_rgba(255,255,255,0.1)] transition-colors duration-300">
                    </a>
                @endif
            </nav>
        </div>
    </header>
</div>
