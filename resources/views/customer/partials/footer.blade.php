{{-- Footer Customer --}}
<footer class="bg-[#1c1a16] mt-32 relative z-10 w-full">
    {{-- Decorative Wavy Top --}}
    <div class="w-full overflow-hidden leading-[0] absolute bottom-full left-0 pointer-events-none">
        <svg class="relative block w-full h-[80px] lg:h-[120px]" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C59.71,118.06,130.83,115.1,192.74,95.8c42.85-13.35,85.25-30.86,128.65-39.36Z" fill="#1c1a16"></path>
        </svg>
    </div>

    {{-- Main Footer Content --}}
    <div class="text-[#f5f0e8] pt-12 pb-8">
        <div class="max-w-[1400px] mx-auto px-6 relative z-10">
            
            {{-- Top Columns --}}
            <div class="flex flex-col lg:flex-row justify-between gap-12 lg:gap-8 mb-16 text-left">
                
                {{-- Column 1: Newsletter & Logo --}}
                <div class="flex flex-col gap-6 text-left lg:w-1/3">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/motifnesia_logo.png') }}" alt="Motifnesia Logo" class="w-10 h-10 rounded-xl shadow-md border border-[#c9a84c]/30 object-cover">
                        <span class="text-2xl font-bold tracking-wide" style="font-family:'Playfair Display',serif">Motifnesia<span class="text-[#c9a84c]">.</span></span>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-semibold mb-3 text-white tracking-wider text-left">Info Koleksi Batik Terbaru</h4>
                        <form action="#" class="flex flex-col sm:flex-row gap-3">
                            <input type="email" placeholder="Alamat email Anda" class="w-full bg-white/5 border border-white/10 rounded-md px-4 py-2.5 text-sm text-white placeholder:text-[#8a7d6b] focus:outline-none focus:border-[#c9a84c] transition-colors text-left">
                            <button type="submit" class="shrink-0 bg-[#c9a84c] hover:bg-[#a8832d] text-white text-xs font-bold px-6 py-3 rounded-md uppercase tracking-wider transition-colors w-full sm:w-auto">
                                Daftar
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Column 2: Layanan & Sosial Media --}}
                <div class="flex flex-col gap-8 text-left">
                    <div>
                        <h4 class="text-sm font-semibold mb-4 text-white tracking-wider text-left">Layanan Pelanggan</h4>
                        <ul class="space-y-3 text-left">
                            <li><a href="#" class="text-sm text-[#8a7d6b] hover:text-[#c9a84c] transition-colors">Gratis Ongkir Seluruh Indonesia</a></li>
                            <li><a href="#" class="text-sm text-[#8a7d6b] hover:text-[#c9a84c] transition-colors">Garansi Retur 7 Hari</a></li>
                            <li><a href="#" class="text-sm text-[#8a7d6b] hover:text-[#c9a84c] transition-colors">100% Batik Original</a></li>
                        </ul>
                    </div>
                    
                    <div class="flex flex-col gap-3">
                        <h4 class="text-sm font-semibold text-white tracking-wider mb-0">Ikuti Kami</h4>
                        <div class="flex items-center gap-4">
                            <a href="#" class="text-[#8a7d6b] hover:text-[#c9a84c] transition-colors"><i class="fab fa-facebook-f text-lg"></i></a>
                            <a href="#" class="text-[#8a7d6b] hover:text-[#c9a84c] transition-colors"><i class="fab fa-instagram text-lg"></i></a>
                            <a href="#" class="text-[#8a7d6b] hover:text-[#c9a84c] transition-colors"><i class="fab fa-twitter text-lg"></i></a>
                        </div>
                    </div>
                </div>

                {{-- Column 3: Informasi --}}
                <div class="flex flex-col gap-6 text-left">
                    <div>
                        <h4 class="text-sm font-semibold mb-4 text-white tracking-wider text-left">Informasi</h4>
                        <ul class="space-y-3 text-left">
                            <li><a href="#" class="text-sm text-[#8a7d6b] hover:text-[#c9a84c] transition-colors">FAQ & Bantuan</a></li>
                            <li><a href="#" class="text-sm text-[#8a7d6b] hover:text-[#c9a84c] transition-colors">Tentang Motifnesia</a></li>
                            <li><a href="#" class="text-sm text-[#8a7d6b] hover:text-[#c9a84c] transition-colors">Cara Pembayaran</a></li>
                        </ul>
                    </div>
                    
                    <div class="text-left">
                        <p class="text-sm text-[#8a7d6b] mb-1"><a href="https://wa.me/6281234567890" target="_blank" class="hover:text-[#c9a84c] transition-colors"><i class="fab fa-whatsapp text-emerald-500 mr-1"></i> WA: 0812-3456-7890</a></p>
                        <a href="mailto:halo@motifnesia.id" class="text-sm text-[#8a7d6b] hover:text-[#c9a84c] transition-colors">halo@motifnesia.id</a>
                    </div>
                </div>

                {{-- Column 4: Kontak --}}
                <div class="flex flex-col gap-6 text-left">
                    <div class="text-left">
                        <h4 class="text-sm font-semibold mb-4 text-white tracking-wider text-left">Kontak Kami</h4>
                        <p class="text-sm text-[#8a7d6b] mb-1">PT Motifnesia Karya Nusantara</p>
                        <p class="text-sm text-[#8a7d6b]">NIB: 1234567890123</p>
                    </div>
                    
                    <div class="text-left">
                        <h4 class="text-sm font-semibold mb-2 text-white tracking-wider text-left">Kantor Pusat:</h4>
                        <p class="text-sm text-[#8a7d6b] leading-relaxed">
                            Gedung Batik Nusantara, Lt. 3<br>
                            Jl. Jend. Sudirman No. 123<br>
                            Jakarta Selatan, 12190
                        </p>
                    </div>
                </div>
                
            </div>

            {{-- Bottom Footer: Trust Badges, Payment & Copyright --}}
            <div class="border-t border-white/10 pt-8 mt-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    
                    {{-- Trust Badges --}}
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-shield-alt text-2xl text-[#c9a84c]"></i>
                            <div class="text-xs font-bold leading-tight uppercase text-white/80">Trusted<br>Shop</div>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-lock text-xl text-white/50"></i>
                            <div class="text-xs font-bold leading-tight uppercase text-white/80">Secure<br>Checkout</div>
                        </div>
                    </div>
                    
                    {{-- Copyright & Small Text --}}
                    <div class="text-center">
                        <p class="text-xs text-[#8a7d6b] mb-2">Bagian dari Motifnesia Group, berdedikasi melestarikan budaya Nusantara.<br>halo@motifnesia.id, 0800 123 4567</p>
                        <p class="text-xs text-white/50">&copy; {{ date('Y') }} Motifnesia. Hak cipta dilindungi undang-undang.</p>
                    </div>
                    
                    {{-- Payment Methods (Indonesian Localized) --}}
                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <!-- BCA -->
                        <div class="h-8 bg-white rounded flex items-center justify-center px-3 shadow-sm border border-white/20">
                            <span class="text-[#00529C] font-black italic text-sm tracking-tighter">BCA</span>
                        </div>
                        <!-- Mandiri -->
                        <div class="h-8 bg-white rounded flex items-center justify-center px-3 shadow-sm border border-white/20">
                            <span class="text-[#003D79] font-bold text-xs tracking-tight">mandiri</span>
                        </div>
                        <!-- BNI -->
                        <div class="h-8 bg-white rounded flex items-center justify-center px-3 shadow-sm border border-white/20">
                            <span class="text-[#F15A23] font-bold text-xs tracking-tight">BNI</span>
                        </div>
                        <!-- GoPay -->
                        <div class="h-8 bg-white rounded flex items-center justify-center px-3 shadow-sm border border-white/20 gap-1">
                            <div class="w-3 h-3 bg-[#00AED6] rounded-full"></div>
                            <span class="text-[#00AED6] font-bold text-xs">gopay</span>
                        </div>
                        <!-- QRIS -->
                        <div class="h-8 bg-white rounded flex items-center justify-center px-3 shadow-sm border border-white/20">
                            <span class="text-[#E30613] font-bold text-xs italic">QRIS</span>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</footer>
