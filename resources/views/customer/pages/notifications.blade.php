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
        border-radius: 16px;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    
    html.customer-light .glass-card {
        background: rgba(78, 61, 37, 0.02) !important;
        border: 1px solid rgba(78, 61, 37, 0.08) !important;
    }

    .notif-card:hover {
        transform: translateY(-4px);
        border-color: rgba(201, 168, 76, 0.35) !important;
        box-shadow: 0 12px 30px rgba(201, 168, 76, 0.1);
    }

    /* Unread dot pulse animation */
    @keyframes pulse-gold {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(201, 168, 76, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(201, 168, 76, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(201, 168, 76, 0); }
    }
    .unread-pulse {
        animation: pulse-gold 2s infinite;
        background: #c9a84c;
        border-radius: 50%;
        width: 8px;
        height: 8px;
        position: absolute;
        top: 20px;
        right: 20px;
    }
</style>

<div style="position:relative;min-height:100vh;padding-top:88px;padding-bottom:60px;background:#131313;overflow:hidden;">
    {{-- Three.js Background Canvas --}}
    <canvas id="three-particles-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>

    <div style="position:relative;z-index:1;max-width:800px;margin:0 auto;padding:0 24px;">

        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;" class="notif-header">
            <div>
                <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#fff;margin-bottom:4px;">Notifikasi</h1>
                <p style="color:rgba(255,255,255,.4);font-size:.875rem;">Informasi pesanan dan aktivitas akun Anda</p>
            </div>
            @if($notifications->where('is_read', false)->count() > 0)
                <form action="{{ route('customer.notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-magnetic"
                            style="padding:9px 20px;background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.25);color:#c9a84c;border-radius:999px;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .2s;"
                            onmouseenter="this.style.background='rgba(201,168,76,.2)';"
                            onmouseleave="this.style.background='rgba(201,168,76,.1)';">
                        ✓ Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- List --}}
        <div class="notif-list" style="display:flex;flex-direction:column;gap:12px;">
            @forelse($notifications as $notif)
            @php
                $isMembershipNotification = str_starts_with($notif->type, 'membership_') || in_array($notif->type, ['member_new_product', 'member_special_promo'], true);
            @endphp
            <div class="notif-card-wrapper">
                <div class="notif-card glass-card" 
                     style="padding:20px;position:relative;{{ !$notif->is_read ? 'border-left:3px solid #c9a84c; border-color: rgba(201,168,76,.3) !important;' : '' }}">
                <div style="display:flex;gap:16px;">
                    {{-- Icon --}}
                    <div style="width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;
                                background:{{ $notif->type === 'order' ? 'rgba(59,130,246,.12)' : ($notif->type === 'return' ? 'rgba(245,158,11,.12)' : ($isMembershipNotification ? 'rgba(201,168,76,.12)' : 'rgba(168,85,247,.12)')) }};">
                        @if($notif->type === 'order') 📦
                        @elseif($notif->type === 'return') ↩️
                        @elseif($notif->type === 'review') ⭐
                        @elseif($isMembershipNotification) M
                        @else 🔔
                        @endif
                    </div>

                    {{-- Content --}}
                    <div style="flex:1;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:6px;">
                            <h3 style="font-weight:700;font-size:.95rem;color:{{ !$notif->is_read ? '#c9a84c' : 'rgba(255,255,255,.9)' }};">{{ $notif->title }}</h3>
                            <span style="color:rgba(255,255,255,.35);font-size:.75rem;flex-shrink:0;margin-left:12px;">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p style="color:rgba(255,255,255,.55);font-size:.85rem;line-height:1.6;margin-bottom:8px;">{{ $notif->message }}</p>

                        {{-- Return Product Details --}}
                        @if($notif->type === 'return' && $notif->data)
                        @php
                            $data = is_array($notif->data) ? $notif->data : json_decode($notif->data, true);
                            $data = is_array($data) ? $data : [];
                        @endphp
                        <div style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:10px;padding:12px;margin-top:8px;display:flex;gap:12px;">
                            <img src="{{ \App\Support\AssetUrl::product($data['produk_gambar'] ?? null) }}" alt="{{ $data['produk_nama'] ?? 'Produk' }}"
                                 style="width:52px;height:52px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                            <div>
                                <p style="font-weight:600;font-size:.82rem;color:rgba(255,255,255,.8);margin-bottom:4px;">{{ $data['produk_nama'] ?? 'Produk' }}</p>
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                    <span style="padding:2px 10px;border-radius:999px;font-size:.75rem;font-weight:700;
                                                 background:{{ ($data['status'] ?? '') === 'Disetujui' ? 'rgba(52,211,153,.12)' : (($data['status'] ?? '') === 'Ditolak' ? 'rgba(239,68,68,.12)' : 'rgba(245,158,11,.12)') }};
                                                 color:{{ ($data['status'] ?? '') === 'Disetujui' ? '#6ee7b7' : (($data['status'] ?? '') === 'Ditolak' ? '#fca5a5' : '#fcd34d') }};">{{ $data['status'] ?? 'Pending' }}</span>
                                    <span style="color:rgba(255,255,255,.45);font-size:.75rem;">{{ $data['reason'] ?? '-' }}</span>
                                </div>
                                @if(isset($data['refund_amount']) && $data['refund_amount'])
                                    <p style="color:#6ee7b7;font-size:.8rem;font-weight:600;">Refund: Rp {{ number_format($data['refund_amount'], 0, ',', '.') }}</p>
                                @endif
                                @if(isset($data['admin_note']) && $data['admin_note'])
                                    <p style="color:rgba(255,255,255,.4);font-size:.75rem;margin-top:4px;">💬 {{ $data['admin_note'] }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($notif->link)
                            <a href="{{ $notif->link }}" class="btn-magnetic"
                               style="display:inline-block;margin-top:10px;padding:7px 18px;background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);color:#c9a84c;border-radius:999px;font-size:.78rem;font-weight:600;text-decoration:none;transition:all .2s;"
                                onmouseenter="this.style.background='rgba(201,168,76,.2)';"
                                onmouseleave="this.style.background='rgba(201,168,76,.1)';">
                                {{ $notif->type === 'return' ? 'Lihat Detail Retur' : ($isMembershipNotification ? 'Lihat Membership' : 'Lihat Detail') }}
                            </a>
                        @endif
                    </div>

                    @if(!$notif->is_read)
                    <div class="unread-pulse"></div>
                    @endif
                </div>
            </div>
            </div>
            @empty
            <div class="glass-card" style="padding:64px;text-align:center;">
                <div style="font-size:3.5rem;margin-bottom:16px;">🔕</div>
                <h3 style="font-family:'Playfair Display',serif;color:rgba(255,255,255,.8);font-size:1.4rem;margin-bottom:8px;">Belum Ada Notifikasi</h3>
                <p style="color:rgba(255,255,255,.4);">Notifikasi pesanan dan retur Anda akan muncul di sini</p>
            </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div style="margin-top:24px;">{{ $notifications->links() }}</div>
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
        gsap.from('.notif-header', {
            opacity: 0,
            y: -30,
            duration: 1,
            ease: 'power3.out'
        });

        gsap.from('.notif-card-wrapper', {
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

            // Particle Geometry
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

