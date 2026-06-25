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

    .voucher-card:hover {
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

    <div style="position:relative;z-index:1;max-width:1000px;margin:0 auto;padding:0 24px;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:26px;flex-wrap:wrap;" class="voucher-header-section">
            <div>
                <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#fff;margin-bottom:4px;">Voucher Saya</h1>
                <p style="color:rgba(255,255,255,.45);font-size:.875rem;">Voucher hasil penukaran poin membership.</p>
            </div>
            <a href="{{ route('customer.membership.index') }}" class="btn-magnetic" style="padding:10px 22px;border-radius:999px;background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.25);color:#c9a84c;font-size:.82rem;font-weight:700;text-decoration:none;transition:all .2s;"
               onmouseenter="this.style.background='rgba(201,168,76,.2)';"
               onmouseleave="this.style.background='rgba(201,168,76,.1)';">Kembali</a>
        </div>

        @if(session('success'))
            <div style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.24);color:#86efac;border-radius:14px;padding:14px 16px;margin-bottom:18px;font-size:.9rem;font-weight:600;" class="voucher-header-section">
                {{ session('success') }}
            </div>
        @endif

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;">
            @forelse($vouchers as $voucher)
                <div class="voucher-card-wrapper">
                    <div class="glass-card voucher-card" style="padding:24px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px;">
                            <span style="padding:4px 10px;border-radius:999px;font-size:.72rem;font-weight:800;background:{{ $voucher->status === 'active' ? 'rgba(34,197,94,.12)' : 'rgba(255,255,255,.08)' }};color:{{ $voucher->status === 'active' ? '#86efac' : 'rgba(255,255,255,.45)' }};">{{ strtoupper($voucher->status) }}</span>
                            <span style="color:rgba(255,255,255,.35);font-size:.75rem;">{{ optional($voucher->redeemed_at)->format('d M Y') }}</span>
                        </div>
                        <h2 style="color:#fff;font-size:1.05rem;font-weight:800;margin-bottom:5px;font-family:'Playfair Display',serif;">{{ $voucher->reward->title ?? 'Voucher Member' }}</h2>
                        <p style="color:#c9a84c;font-size:.82rem;font-weight:800;margin-bottom:12px;">{{ $voucher->reward->discount_label ?? 'Promo Member' }}</p>
                        <code style="display:block;background:rgba(201,168,76,.12);border:1px dashed rgba(201,168,76,.35);color:#c9a84c;border-radius:10px;padding:10px 12px;font-size:.85rem;text-align:center;margin-bottom:12px;letter-spacing:.05em;font-weight:700;">{{ $voucher->voucher_code }}</code>
                        <p style="color:rgba(255,255,255,.42);font-size:.78rem;">Ditukar dengan {{ number_format($voucher->points_used, 0, ',', '.') }} poin.</p>
                    </div>
                </div>
            @empty
                <div style="grid-column:1/-1;" class="voucher-card-wrapper">
                    <div class="glass-card" style="padding:64px 24px;text-align:center;color:rgba(255,255,255,.45);font-size:.9rem;">
                        Belum ada voucher yang ditukar.
                    </div>
                </div>
            @endforelse
        </div>

        @if($vouchers->hasPages())
            <div style="margin-top:22px;">{{ $vouchers->links() }}</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- Load Animation Libraries --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ==================== 1. INITIALIZE AOS ====================
    AOS.init({
        once: true,
        duration: 800,
        easing: 'ease-out-cubic'
    });

    // ==================== 2. GSAP ENTRANCE ANIMATION ====================
    gsap.from('.voucher-header-section', {
        opacity: 0,
        y: -30,
        stagger: 0.1,
        duration: 1,
        ease: 'power3.out'
    });

    gsap.from('.voucher-card-wrapper', {
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
});
</script>
@endpush
