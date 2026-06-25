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

    .payment-card:hover {
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

    <div style="position:relative;z-index:1;max-width:700px;margin:0 auto;padding:0 24px;">

        <div style="margin-bottom:28px;text-align:center;" class="payment-header-section">
            <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#fff;margin-bottom:4px;">Konfirmasi Pembayaran</h1>
            <p style="color:rgba(255,255,255,.4);font-size:.875rem;">Selesaikan pembayaran sebelum waktu habis</p>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            {{-- Countdown --}}
            <div class="payment-step-wrapper">
                <div class="glass-card payment-card" style="background:linear-gradient(135deg,rgba(201,168,76,.06),rgba(168,131,45,.03)) !important;border:1px solid rgba(201,168,76,.2) !important;padding:28px;text-align:center;">
                    <p style="color:rgba(255,255,255,.55);font-size:.82rem;margin-bottom:4px;">Bayar sebelum</p>
                    <p style="color:rgba(255,255,255,.8);font-size:.95rem;font-weight:600;margin-bottom:16px;">{{ $paymentDeadline->format('d F Y H:i') }} WIB</p>
                    <div id="countdown_timer" style="font-size:2.5rem;font-weight:900;color:#c9a84c;font-family:'Playfair Display',serif;letter-spacing:.08em;">23:59:59</div>
                </div>
            </div>

            {{-- Nomor Rekening --}}
            <div class="payment-step-wrapper">
                <div class="glass-card payment-card" style="padding:28px;text-align:center;">
                    <p style="color:rgba(255,255,255,.4);font-size:.78rem;margin-bottom:8px;">{{ $checkoutData['created_at']->format('d F Y H:i') }} WIB</p>
                    <p style="color:rgba(255,255,255,.5);font-size:.82rem;margin-bottom:6px;letter-spacing:.05em;text-transform:uppercase;">Nomor Rekening</p>
                    <div style="font-size:1.6rem;font-weight:900;color:#fff;letter-spacing:.12em;margin-bottom:20px;">8887867867555700</div>

                    <div style="border-top:1px solid rgba(255,255,255,.06);padding-top:20px;">
                        <p style="color:rgba(255,255,255,.5);font-size:.82rem;margin-bottom:6px;">Total Tagihan</p>
                        <p style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#c9a84c;margin-bottom:12px;">Rp {{ number_format($checkoutData['total_bayar'], 0, ',', '.') }}</p>
                        <button id="link_detail" class="btn-magnetic" style="background:none;border:none;color:rgba(201,168,76,.75);font-size:.82rem;cursor:pointer;text-decoration:underline;outline:none;">Lihat Rincian</button>
                    </div>
                </div>
            </div>

            {{-- Detail Rincian (toggle) --}}
            <div class="payment-step-wrapper" id="detail_box_wrapper" style="display:none;">
                <div class="glass-card" style="padding:24px;border-color:rgba(201,168,76,.2) !important;">
                    <h4 style="color:#c9a84c;font-weight:700;font-size:.95rem;margin-bottom:16px;">Rincian Belanja</h4>
                    @foreach($checkoutData['products'] as $product)
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:.875rem;">
                        <span style="color:rgba(255,255,255,.65);">{{ $product['nama'] }} ({{ $product['ukuran'] }}) x{{ $product['qty'] }}</span>
                        <span style="color:rgba(255,255,255,.8);">Rp {{ number_format($product['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:.875rem;">
                        <span style="color:rgba(255,255,255,.5);">Subtotal Produk:</span>
                        <span style="color:rgba(255,255,255,.75);">Rp {{ number_format($checkoutData['subtotal_produk'], 0, ',', '.') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:.875rem;">
                        <span style="color:rgba(255,255,255,.5);">Ongkos Kirim ({{ $checkoutData['metode_pengiriman']['nama'] }}):</span>
                        <span style="color:rgba(255,255,255,.75);">Rp {{ number_format($checkoutData['total_ongkir'], 0, ',', '.') }}</span>
                    </div>
                    @if(($checkoutData['voucher_discount'] ?? 0) > 0)
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:.875rem;">
                        <span style="color:rgba(255,255,255,.5);">Voucher Membership ({{ $checkoutData['voucher']['code'] ?? '-' }}):</span>
                        <span style="color:#86efac;font-weight:700;">- Rp {{ number_format($checkoutData['voucher_discount'], 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div style="display:flex;justify-content:space-between;padding:12px 0 0;font-weight:700;">
                        <span style="color:#fff;">Total:</span>
                        <span style="color:#c9a84c;font-size:1.1rem;">Rp {{ number_format($checkoutData['total_bayar'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Form Pembayaran --}}
            <div class="payment-step-wrapper">
                <form id="form_payment" action="{{ route('customer.payment.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="glass-card payment-card" style="padding:24px;margin-bottom:16px;">
                        <div style="margin-bottom:20px;">
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:10px;">Masukkan Nomor Pembayaran</label>
                            <input type="text" name="payment_number" id="payment_number" placeholder="Contoh: 8887867867555700" required
                                   style="width:100%;padding:13px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.9rem;outline:none;transition:border-color .2s;"
                                   onfocus="this.style.borderColor='#c9a84c';"
                                   onblur="this.style.borderColor='rgba(255,255,255,.1)';">
                        </div>
                        <div>
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:10px;">Unggah Bukti Transfer / Pembayaran</label>
                            <input type="file" name="payment_proof" id="payment_proof" accept="image/*" required
                                   style="width:100%;padding:10px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.9rem;outline:none;transition:border-color .2s;"
                                   onfocus="this.style.borderColor='#c9a84c';"
                                   onblur="this.style.borderColor='rgba(255,255,255,.1)';">
                            <small style="display:block;color:rgba(255,255,255,.4);font-size:.75rem;margin-top:6px;">Format gambar (JPG, PNG, GIF), maks. 5MB</small>
                        </div>
                    </div>
                    <button type="submit" class="btn-magnetic" style="width:100%;padding:15px;background:linear-gradient(135deg,#c9a84c,#a8832d);border:none;border-radius:999px;color:#111;font-size:1rem;font-weight:700;cursor:pointer;letter-spacing:.04em;transition:all .25s;"
                            onmouseenter="this.style.opacity='.85';this.style.transform='translateY(-1px)';"
                            onmouseleave="this.style.opacity='1';this.style.transform='';">
                        Konfirmasi Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

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
    gsap.from('.payment-header-section', {
        opacity: 0,
        y: -30,
        duration: 1,
        ease: 'power3.out'
    });

    gsap.from('.payment-step-wrapper', {
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

    // ==================== 5. PAYMENT DETAIL TOGGLE & TIMER ====================
    document.getElementById('link_detail').addEventListener('click', function(e) {
        e.preventDefault();
        const wrapper = document.getElementById('detail_box_wrapper');
        const isHidden = wrapper.style.display === 'none';
        
        if (isHidden) {
            wrapper.style.display = 'block';
            gsap.fromTo(wrapper, { opacity: 0, height: 0 }, { opacity: 1, height: 'auto', duration: 0.4, ease: 'power2.out' });
            this.textContent = 'Sembunyikan Rincian';
        } else {
            gsap.to(wrapper, { opacity: 0, height: 0, duration: 0.3, ease: 'power2.in', onComplete: () => {
                wrapper.style.display = 'none';
            }});
            this.textContent = 'Lihat Rincian';
        }
    });

    const deadline = new Date('{{ $paymentDeadline->format("Y-m-d H:i:s") }}');
    function updateCountdown() {
        const diff = deadline - new Date();
        if (diff <= 0) { document.getElementById('countdown_timer').textContent = '00:00:00'; return; }
        const h = Math.floor(diff / 3600000), m = Math.floor((diff % 3600000) / 60000), s = Math.floor((diff % 60000) / 1000);
        document.getElementById('countdown_timer').textContent =
            String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);
});
</script>
@endpush
