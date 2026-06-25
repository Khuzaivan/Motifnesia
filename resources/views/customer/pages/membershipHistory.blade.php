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

    .history-row {
        transition: all 0.3s ease;
    }
    .history-row:hover {
        background: rgba(255, 255, 255, 0.015) !important;
    }
    
    .btn-magnetic {
        transition: transform 0.2s cubic-bezier(0.25, 1, 0.5, 1);
    }
</style>

<div style="position:relative;min-height:100vh;padding-top:88px;padding-bottom:60px;background:#131313;overflow:hidden;">
    {{-- Three.js Background Canvas --}}
    <canvas id="three-particles-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>

    <div style="position:relative;z-index:1;max-width:900px;margin:0 auto;padding:0 24px;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:26px;flex-wrap:wrap;" class="history-header-section">
            <div>
                <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#fff;margin-bottom:4px;">Riwayat Poin</h1>
                <p style="color:rgba(255,255,255,.45);font-size:.875rem;">Semua aktivitas poin membership Anda.</p>
            </div>
            <a href="{{ route('customer.membership.index') }}" class="btn-magnetic" style="padding:10px 22px;border-radius:999px;background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.25);color:#c9a84c;font-size:.82rem;font-weight:700;text-decoration:none;transition:all .2s;"
               onmouseenter="this.style.background='rgba(201,168,76,.2)';"
               onmouseleave="this.style.background='rgba(201,168,76,.1)';">Kembali</a>
        </div>

        <div class="history-list-wrapper">
            <div class="glass-card" style="overflow:hidden;">
                @forelse($pointTransactions as $transaction)
                    @php
                        $pointValue = (int) $transaction->points;
                        $isPositive = $transaction->type === 'earn' || ($transaction->type === 'adjust' && $pointValue >= 0);
                    @endphp
                    <div class="history-row" style="display:flex;justify-content:space-between;gap:16px;padding:18px 20px;border-bottom:1px solid rgba(255,255,255,.05);{{ $loop->last ? 'border-bottom:none;' : '' }}">
                        <div>
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap;">
                                <span style="padding:3px 10px;border-radius:999px;font-size:.72rem;font-weight:800;background:{{ $isPositive ? 'rgba(34,197,94,.12)' : 'rgba(239,68,68,.12)' }};color:{{ $isPositive ? '#86efac' : '#fca5a5' }};">{{ strtoupper($transaction->type) }}</span>
                                @if($transaction->order_id)
                                    <span style="color:rgba(255,255,255,.35);font-size:.76rem;">Order #{{ $transaction->order_id }}</span>
                                @endif
                            </div>
                            <p style="color:rgba(255,255,255,.84);font-size:.9rem;font-weight:700;margin-bottom:4px;">{{ $transaction->description ?: 'Transaksi poin membership' }}</p>
                            <p style="color:rgba(255,255,255,.36);font-size:.78rem;">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div style="color:{{ $isPositive ? '#86efac' : '#fca5a5' }};font-size:1rem;font-weight:900;white-space:nowrap;">
                            {{ $isPositive ? '+' : '-' }}{{ number_format(abs($pointValue), 0, ',', '.') }} poin
                        </div>
                    </div>
                @empty
                    <div style="padding:64px 24px;text-align:center;color:rgba(255,255,255,.45);font-size:.9rem;">Belum ada riwayat poin.</div>
                @endforelse
            </div>
        </div>

        @if($pointTransactions->hasPages())
            <div style="margin-top:22px;">{{ $pointTransactions->links() }}</div>
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
    gsap.from('.history-header-section', {
        opacity: 0,
        y: -30,
        duration: 1,
        ease: 'power3.out'
    });

    gsap.from('.history-list-wrapper', {
        opacity: 0,
        y: 40,
        duration: 1,
        ease: 'power3.out'
    });

    // Stagger animation for individual rows inside the card
    gsap.from('.history-row', {
        opacity: 0,
        x: -20,
        stagger: 0.05,
        duration: 0.6,
        delay: 0.2,
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
