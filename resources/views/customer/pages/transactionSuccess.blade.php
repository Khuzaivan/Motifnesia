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

    .success-card:hover {
        transform: translateY(-4px);
        border-color: rgba(201, 168, 76, 0.35) !important;
        box-shadow: 0 12px 30px rgba(201, 168, 76, 0.1);
    }
    
    .btn-magnetic {
        transition: transform 0.2s cubic-bezier(0.25, 1, 0.5, 1);
    }
</style>

<div style="position:relative;min-height:100vh;padding-top:88px;padding-bottom:60px;background:#131313;overflow:hidden;display:flex;align-items:center;justify-content:center;">
    {{-- Three.js Background Canvas --}}
    <canvas id="three-particles-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>

    <div style="position:relative;z-index:1;max-width:560px;width:100%;padding:0 24px;text-align:center;">

        <div style="display:flex;flex-direction:column;gap:16px;">
            {{-- Success Animation & Header --}}
            <div class="success-step-wrapper">
                <div style="width:100px;height:100px;background:linear-gradient(135deg,#c9a84c,#a8832d);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 28px;box-shadow:0 0 60px rgba(201,168,76,.35);">
                    <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>

                <h1 style="font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:700;color:#fff;margin-bottom:8px;">Pembayaran Berhasil!</h1>
                <p style="color:rgba(255,255,255,.5);font-size:.9rem;margin-bottom:12px;line-height:1.6;">Pesanan Anda sedang diproses. Kami akan mengirimkan konfirmasi melalui email.</p>
            </div>

            {{-- Order Info --}}
            <div class="success-step-wrapper">
                <div class="glass-card success-card" style="padding:24px;text-align:left;">
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.05);">
                        <span style="color:rgba(255,255,255,.5);font-size:.875rem;">Nomor Pesanan:</span>
                        <span style="color:#fff;font-weight:700;font-size:.875rem;">{{ $order->order_number }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.05);">
                        <span style="color:rgba(255,255,255,.5);font-size:.875rem;">Total Pembayaran:</span>
                        <span style="color:#c9a84c;font-weight:700;font-size:1.1rem;">Rp {{ number_format($order->total_bayar, 0, ',', '.') }}</span>
                    </div>
                    @if(($order->voucher_discount ?? 0) > 0)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.05);">
                        <span style="color:rgba(255,255,255,.5);font-size:.875rem;">Voucher Membership:</span>
                        <span style="color:#86efac;font-weight:700;font-size:.875rem;">{{ $order->voucher_code }} (-Rp {{ number_format($order->voucher_discount, 0, ',', '.') }})</span>
                    </div>
                    @endif
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;">
                        <span style="color:rgba(255,255,255,.5);font-size:.875rem;">Status:</span>
                        <span style="background:rgba(201,168,76,.15);color:#c9a84c;padding:4px 14px;border-radius:999px;font-size:.78rem;font-weight:700;border:1px solid rgba(201,168,76,.3);">{{ $order->deliveryStatus->nama_status }}</span>
                    </div>
                </div>
            </div>

            <div class="success-step-wrapper" style="margin-top:12px;">
                <a href="{{ route('customer.home') }}" class="btn-magnetic"
                   style="display:inline-block;padding:13px 48px;background:linear-gradient(135deg,#c9a84c,#a8832d);color:#111;font-size:.95rem;font-weight:700;border-radius:999px;text-decoration:none;letter-spacing:.04em;transition:all .25s;"
                   onmouseenter="this.style.opacity='.85';this.style.transform='translateY(-2px)';"
                   onmouseleave="this.style.opacity='1';this.style.transform='';">
                    Kembali ke Beranda
                </a>
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
    gsap.from('.success-step-wrapper', {
        opacity: 0,
        y: 40,
        stagger: 0.12,
        duration: 1,
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
});
</script>
@endpush
