@extends('customer.layouts.mainLayout')

@section('container')
{{-- Load CSS for AOS --}}
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />

<style>
    /* Glassmorphism card styling */
    .glass-card {
        background: rgba(255, 255, 255, 0.02) !important;
        backdrop-filter: blur(16px) saturate(120%) !important;
        -webkit-backdrop-filter: blur(16px) saturate(120%) !important;
        border: 1px solid rgba(255, 255, 255, 0.06) !important;
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    
    html.customer-light .glass-card {
        background: rgba(78, 61, 37, 0.02) !important;
        border: 1px solid rgba(78, 61, 37, 0.08) !important;
    }

    .checkout-card:hover {
        transform: translateY(-4px);
        border-color: rgba(201, 168, 76, 0.35) !important;
        box-shadow: 0 12px 30px rgba(201, 168, 76, 0.1);
    }
    
    .btn-magnetic {
        transition: transform 0.2s cubic-bezier(0.25, 1, 0.5, 1);
    }
</style>

<div style="position:relative;min-height:100vh;padding-top:88px;padding-bottom:60px;background:#131313;overflow:hidden;">
    {{-- Three.js Background Canvas --}}
    <canvas id="three-particles-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>

    <div style="position:relative;z-index:1;max-width:800px;margin:0 auto;padding:0 24px;">

        <div style="margin-bottom:28px;" class="checkout-header-section">
            <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#fff;margin-bottom:4px;">Checkout</h1>
            <p style="color:rgba(255,255,255,.4);font-size:.875rem;">Lengkapi informasi pengiriman dan pembayaran Anda</p>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            {{-- Alamat Pengiriman --}}
            <div class="checkout-step-wrapper">
                <div class="glass-card checkout-card" style="padding:24px;">
                    <h3 style="font-family:'Playfair Display',serif;color:#c9a84c;font-size:1.1rem;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Alamat Pengiriman
                    </h3>
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <input type="radio" name="alamat_radio" id="alamat_default" checked style="width:18px;height:18px;accent-color:#c9a84c;margin-top:3px;flex-shrink:0;">
                        <label for="alamat_default" style="color:rgba(255,255,255,.75);font-size:.9rem;line-height:1.6;cursor:pointer;">{{ $defaultAddress }}</label>
                    </div>
                    <input type="hidden" id="alamat_input" value="{{ $defaultAddress }}">
                </div>
            </div>

            {{-- Produk --}}
            <div class="checkout-step-wrapper">
                <div class="glass-card checkout-card" style="padding:24px;">
                    <h3 style="font-family:'Playfair Display',serif;color:#c9a84c;font-size:1.1rem;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Produk ({{ count($products) }} item)
                    </h3>
                    @foreach($products as $product)
                    @php
                        $hargaDiskon = $product['harga_diskon'] ?? $product['harga'];
                        $diskonPersen = $product['diskon_persen'] ?? 0;
                    @endphp
                    <div style="display:flex;align-items:center;gap:16px;padding:12px 0;border-bottom:1px solid rgba(255,255,255,.05);{{ $loop->last ? 'border-bottom:none;' : '' }}">
                        <img src="{{ $product['gambar_url'] ?? \App\Support\AssetUrl::product($product['gambar'] ?? null) }}" alt="{{ $product['nama'] }}" style="width:64px;height:64px;object-fit:cover;border-radius:10px;border:1px solid rgba(255,255,255,.08);flex-shrink:0;">
                        <div style="flex:1;">
                            <p style="color:rgba(255,255,255,.9);font-weight:600;font-size:.9rem;margin-bottom:4px;">{{ $product['nama'] }} <span style="color:rgba(255,255,255,.4);">— {{ $product['ukuran'] }}</span></p>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="color:rgba(255,255,255,.5);font-size:.82rem;">{{ $product['qty'] }}x Rp {{ number_format($hargaDiskon, 0, ',', '.') }}</span>
                                @if($diskonPersen > 0)
                                    <span style="background:rgba(201,168,76,.15);color:#c9a84c;font-size:9px;font-weight:700;padding:2px 7px;border-radius:999px;">-{{ $diskonPersen }}%</span>
                                @endif
                            </div>
                        </div>
                        <span style="color:#c9a84c;font-weight:700;font-size:.95rem;flex-shrink:0;">Rp {{ number_format($hargaDiskon * $product['qty'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Metode Pengiriman --}}
            <div class="checkout-step-wrapper">
                <div class="glass-card checkout-card" style="padding:24px;">
                    <h3 style="font-family:'Playfair Display',serif;color:#c9a84c;font-size:1.1rem;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10M21 16V10a2 2 0 00-2-2h-3V5a1 1 0 00-1-1H9v12m12 0h-3m3 0a2 2 0 012 2v1H17M9 16H5"/></svg>
                        Metode Pengiriman
                    </h3>
                    @foreach($metodePengiriman as $pengiriman)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid rgba(255,255,255,.05);{{ $loop->last ? 'border-bottom:none;' : '' }}">
                        <div style="display:flex;align-items:flex-start;gap:12px;">
                            <input type="radio" name="metode_pengiriman" value="{{ $pengiriman->id }}" data-harga="{{ $pengiriman->harga }}"
                                   id="pengiriman_{{ $pengiriman->id }}" style="width:18px;height:18px;accent-color:#c9a84c;margin-top:3px;cursor:pointer;"
                                   @if($loop->first) checked @endif>
                            <label for="pengiriman_{{ $pengiriman->id }}" style="cursor:pointer;">
                                <span style="display:block;color:rgba(255,255,255,.9);font-weight:600;font-size:.875rem;">{{ $pengiriman->nama_pengiriman }}</span>
                                <span style="color:rgba(255,255,255,.45);font-size:.8rem;">{{ $pengiriman->deskripsi_pengiriman }}</span>
                            </label>
                        </div>
                        <span style="color:#c9a84c;font-weight:700;font-size:.9rem;flex-shrink:0;">Rp {{ number_format($pengiriman->harga, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Voucher Membership --}}
            @if(isset($memberVouchers) && $memberVouchers->count() > 0)
            <div class="checkout-step-wrapper">
                <div class="glass-card checkout-card" style="padding:24px;border:1px solid rgba(201,168,76,.18) !important;">
                    <h3 style="font-family:'Playfair Display',serif;color:#c9a84c;font-size:1.1rem;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        Voucher Membership
                    </h3>
                    <select id="voucher_code" style="width:100%;padding:13px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.9rem;outline:none;">
                        <option value="" style="background:#1e1e1e;color:#fff;">Tidak pakai voucher</option>
                        @foreach($memberVouchers as $voucher)
                            <option value="{{ $voucher->voucher_code }}" style="background:#1e1e1e;color:#fff;">
                                {{ $voucher->voucher_code }} - {{ $voucher->reward->title ?? 'Voucher Member' }} ({{ $voucher->reward->discount_label ?? 'Promo Member' }})
                            </option>
                        @endforeach
                    </select>
                    <p id="voucher_hint" style="color:rgba(255,255,255,.42);font-size:.78rem;margin-top:10px;">Voucher akan otomatis ditandai terpakai setelah pembayaran/order berhasil.</p>
                </div>
            </div>
            @else
            <input type="hidden" id="voucher_code" value="">
            @endif

            {{-- Metode Pembayaran --}}
            <div class="checkout-step-wrapper">
                <div class="glass-card checkout-card" style="padding:24px;">
                    <h3 style="font-family:'Playfair Display',serif;color:#c9a84c;font-size:1.1rem;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Metode Pembayaran
                    </h3>
                    @foreach($metodePembayaran as $pembayaran)
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid rgba(255,255,255,.05);{{ $loop->last ? 'border-bottom:none;' : '' }}">
                        <input type="radio" name="metode_pembayaran" value="{{ $pembayaran->id }}" id="pembayaran_{{ $pembayaran->id }}"
                               style="width:18px;height:18px;accent-color:#c9a84c;cursor:pointer;" @if($loop->first) checked @endif>
                        <label for="pembayaran_{{ $pembayaran->id }}" style="color:rgba(255,255,255,.8);font-size:.875rem;cursor:pointer;">{{ $pembayaran->nama_pembayaran }}</label>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Rincian Belanja --}}
            <div class="checkout-step-wrapper">
                <div class="glass-card checkout-card" style="padding:24px;">
                    <h3 style="font-family:'Playfair Display',serif;color:#c9a84c;font-size:1.1rem;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px;">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        Rincian Belanja
                    </h3>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);">
                        <span style="color:rgba(255,255,255,.55);font-size:.875rem;">Total Produk ({{ count($products) }} barang):</span>
                        <span id="subtotal_display" style="color:rgba(255,255,255,.8);font-size:.875rem;">Rp {{ number_format($subtotalProduk, 0, ',', '.') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);">
                        <span style="color:rgba(255,255,255,.55);font-size:.875rem;">Ongkos Kirim:</span>
                        <span id="ongkir_display" style="color:rgba(255,255,255,.8);font-size:.875rem;">Rp 0</span>
                    </div>
                    @if(isset($tierDiscount) && $tierDiscount > 0)
                    <div id="tier_discount_row" style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);">
                        <span style="color:rgba(255,255,255,.55);font-size:.875rem;display:flex;align-items:center;gap:6px;">
                            @php
                                $userTier = $user->membership_tier ?? 'bronze';
                                $tierBadge = match($userTier) {
                                    'gold' => '🥇',
                                    'silver' => '🥈',
                                    default => '🥉',
                                };
                            @endphp
                            {{ $tierBadge }} Diskon Member {{ ucfirst($userTier) }} ({{ $tierDiscountPercent * 100 }}%):
                        </span>
                        <span style="color:#86efac;font-size:.875rem;font-weight:700;">- Rp {{ number_format($tierDiscount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div id="voucher_row" style="display:none;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);">
                        <span style="color:rgba(255,255,255,.55);font-size:.875rem;">Voucher Membership:</span>
                        <span id="voucher_discount_display" style="color:#86efac;font-size:.875rem;font-weight:700;">- Rp 0</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:12px 0 0;">
                        <span style="color:#fff;font-weight:700;font-size:1rem;">Total Bayar:</span>
                        <span id="total_display" style="color:#c9a84c;font-weight:700;font-size:1.2rem;">Rp {{ number_format($subtotalProduk, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="checkout-step-wrapper" style="margin-top:8px;">
                <button id="btn_bayar" class="btn-magnetic" style="width:100%;padding:15px;background:linear-gradient(135deg,#c9a84c,#a8832d);border:none;border-radius:999px;color:#111;font-size:1rem;font-weight:700;cursor:pointer;letter-spacing:.04em;transition:all .25s;"
                        onmouseenter="this.style.opacity='.85';this.style.transform='translateY(-1px)';"
                        onmouseleave="this.style.opacity='1';this.style.transform='';">
                    Konfirmasi & Bayar →
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@php
    $checkoutVoucherOptions = ($memberVouchers ?? collect())->map(function ($voucher) {
        return [
            'code' => $voucher->voucher_code,
            'title' => $voucher->reward->title ?? 'Voucher Member',
            'discount_type' => $voucher->reward->discount_type ?? '',
            'discount_value' => (int) ($voucher->reward->discount_value ?? 0),
        ];
    })->values()->all();
@endphp

@push('scripts')
{{-- Load Animation Libraries --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==================== 1. INITIALIZE AOS ====================
    AOS.init({
        once: true,
        duration: 800,
        easing: 'ease-out-cubic'
    });

    // ==================== 2. GSAP ENTRANCE ANIMATION ====================
    gsap.from('.checkout-header-section', {
        opacity: 0,
        y: -30,
        duration: 1,
        ease: 'power3.out'
    });

    gsap.from('.checkout-step-wrapper', {
        opacity: 0,
        y: 40,
        stagger: 0.08,
        duration: 0.8,
        ease: 'power3.out'
    });

    // ==================== 3. MAGNETIC BUTTONS ====================
    document.querySelectorAll('.btn-magnetic').forEach(btn => {
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            btn.style.transform = `translate(${x * 0.25}px, ${y * 0.25}px)`;
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.transform = '';
        });
    });

    // ==================== 4. THREE.JS BG PARTICLES ====================
    const canvas = document.getElementById('three-particles-canvas');
    if (canvas) {
        const container = canvas.parentElement;
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(60, container.clientWidth / container.clientHeight, 0.1, 100);
        camera.position.z = 5;

        const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        const particleCount = 80;
        const geometry = new THREE.BufferGeometry();
        const positions = new Float32Array(particleCount * 3);
        const velocities = new Float32Array(particleCount * 3);

        for (let i = 0; i < particleCount * 3; i += 3) {
            positions[i] = (Math.random() - 0.5) * 8;
            positions[i+1] = (Math.random() - 0.5) * 8;
            positions[i+2] = (Math.random() - 0.5) * 5;

            velocities[i] = (Math.random() - 0.5) * 0.003;
            velocities[i+1] = (Math.random() - 0.5) * 0.003 + 0.002;
            velocities[i+2] = (Math.random() - 0.5) * 0.002;
        }

        geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

        const material = new THREE.PointsMaterial({
            size: 0.035,
            color: 0xc9a84c,
            transparent: true,
            opacity: 0.4,
            blending: THREE.AdditiveBlending
        });

        const points = new THREE.Points(geometry, material);
        scene.add(points);

        function resizeCanvas() {
            const width = container.clientWidth;
            const height = container.clientHeight;
            camera.aspect = width / height;
            camera.updateProjectionMatrix();
            renderer.setSize(width, height);
        }
        window.addEventListener('resize', resizeCanvas);

        const resizeObserver = new ResizeObserver(() => resizeCanvas());
        resizeObserver.observe(container);

        function animate() {
            requestAnimationFrame(animate);
            
            const pos = geometry.attributes.position.array;
            for (let i = 1; i < particleCount * 3; i += 3) {
                pos[i-1] += velocities[i-1];
                pos[i] += velocities[i];
                pos[i+1] += velocities[i+1];

                if (pos[i] > 4) pos[i] = -4;
                if (pos[i-1] > 4) pos[i-1] = -4;
                if (pos[i-1] < -4) pos[i-1] = 4;
            }
            geometry.attributes.position.needsUpdate = true;

            points.rotation.y += 0.0006;
            points.rotation.x += 0.0002;

            renderer.render(scene, camera);
        }
        animate();
    }

    // ==================== 5. CHECKOUT CALCULATIONS & FETCH ====================
    const subtotal = @json((int) $subtotalProduk);
    const tierDiscount = @json((int) ($tierDiscount ?? 0));
    const vouchers = @json($checkoutVoucherOptions);

    function formatRupiah(value) {
        return 'Rp ' + Math.max(0, Math.round(value)).toLocaleString('id-ID');
    }

    function selectedVoucher() {
        const code = document.getElementById('voucher_code')?.value || '';
        return vouchers.find(voucher => voucher.code === code) || null;
    }

    function calculateVoucherDiscount(voucher, ongkir) {
        if (!voucher) return 0;
        if (voucher.discount_type === 'fixed') return Math.min(voucher.discount_value, subtotal + ongkir);
        if (voucher.discount_type === 'percent') return Math.floor(subtotal * (voucher.discount_value / 100));
        if (voucher.discount_type === 'free_shipping') return ongkir;
        return 0;
    }

    function updateTotals() {
        const checkedPengiriman = document.querySelector('input[name="metode_pengiriman"]:checked');
        const ongkir = checkedPengiriman ? (parseInt(checkedPengiriman.getAttribute('data-harga')) || 0) : 0;
        const voucher = selectedVoucher();
        const voucherDiscount = calculateVoucherDiscount(voucher, ongkir);
        const total = Math.max(0, subtotal + ongkir - tierDiscount - voucherDiscount);

        document.getElementById('ongkir_display').textContent = formatRupiah(ongkir);
        document.getElementById('total_display').textContent = formatRupiah(total);

        const voucherRow = document.getElementById('voucher_row');
        const voucherDiscountDisplay = document.getElementById('voucher_discount_display');
        if (voucherRow && voucherDiscountDisplay) {
            voucherRow.style.display = voucherDiscount > 0 ? 'flex' : 'none';
            voucherDiscountDisplay.textContent = '- ' + formatRupiah(voucherDiscount);
        }

        const hint = document.getElementById('voucher_hint');
        if (hint && voucher) {
            hint.textContent = voucher.title + ' akan mengurangi total sebesar ' + formatRupiah(voucherDiscount) + '.';
        } else if (hint) {
            hint.textContent = 'Voucher akan otomatis ditandai terpakai setelah pembayaran/order berhasil.';
        }
    }

    document.querySelectorAll('input[name="metode_pengiriman"]').forEach(radio => {
        radio.addEventListener('change', updateTotals);
    });

    document.getElementById('voucher_code')?.addEventListener('change', updateTotals);

    const checkedPengiriman = document.querySelector('input[name="metode_pengiriman"]:checked');
    if (checkedPengiriman) updateTotals();

    document.getElementById('btn_bayar').addEventListener('click', function() {
        const alamat = document.getElementById('alamat_input').value;
        const metodePengiriman = document.querySelector('input[name="metode_pengiriman"]:checked');
        const metodePembayaran = document.querySelector('input[name="metode_pembayaran"]:checked');

        if (!metodePengiriman || !metodePembayaran) {
            alert('Pilih metode pengiriman dan pembayaran terlebih dahulu.');
            return;
        }

        fetch('{{ route("customer.checkout.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                alamat: alamat,
                metode_pengiriman_id: metodePengiriman.value,
                metode_pembayaran_id: metodePembayaran.value,
                voucher_code: document.getElementById('voucher_code')?.value || '',
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) window.location.href = data.redirect_url;
            else alert(data.message || 'Terjadi kesalahan.');
        })
        .catch(() => alert('Terjadi kesalahan. Silakan coba lagi.'));
    });
});
</script>
@endpush
