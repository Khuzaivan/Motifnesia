@extends('customer.layouts.mainLayout')

@section('container')
{{-- Load CSS for AOS --}}
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />

@php
    $user = Auth::user();
    $totalOrders = $user ? $user->orders()->whereNotIn('payment_status', ['expired', 'rejected'])->count() : 0;
    $totalSpending = $user ? $user->total_spending : 0;
@endphp

<style>
    /* Glassmorphism panel styling */
    .glass-card {
        background: rgba(255, 255, 255, 0.02) !important;
        backdrop-filter: blur(16px) saturate(120%) !important;
        -webkit-backdrop-filter: blur(16px) saturate(120%) !important;
        border: 1px solid rgba(255, 255, 255, 0.06) !important;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    
    html.customer-light .glass-card {
        background: rgba(78, 61, 37, 0.02) !important;
        border: 1px solid rgba(78, 61, 37, 0.08) !important;
    }

    .glass-card:hover {
        transform: translateY(-4px);
        border-color: rgba(201, 168, 76, 0.3) !important;
        box-shadow: 0 12px 30px rgba(201, 168, 76, 0.08);
    }
    
    .btn-magnetic {
        transition: transform 0.2s cubic-bezier(0.25, 1, 0.5, 1);
    }

    /* Profile picture glow ring */
    @keyframes border-glow {
        0% { box-shadow: 0 0 15px rgba(201, 168, 76, 0.3); border-color: #c9a84c; }
        50% { box-shadow: 0 0 25px rgba(201, 168, 76, 0.6); border-color: #a8832d; }
        100% { box-shadow: 0 0 15px rgba(201, 168, 76, 0.3); border-color: #c9a84c; }
    }
    .profile-avatar-ring {
        animation: border-glow 3s infinite ease-in-out;
    }
</style>

<div style="position:relative;min-height:100vh;padding-top:88px;padding-bottom:60px;background:#131313;overflow:hidden;">
    {{-- Three.js Background Canvas --}}
    <canvas id="three-particles-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>

    <div style="position:relative;z-index:1;max-width:1100px;margin:0 auto;padding:0 24px;">

        <div style="margin-bottom:28px;" class="profile-header-section">
            <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#fff;margin-bottom:4px;">Profil Saya</h1>
            <p style="color:rgba(255,255,255,.4);font-size:.875rem;">Kelola informasi akun dan alamat pengiriman</p>
        </div>

        <div style="display:grid;grid-template-columns:280px 1fr;gap:24px;" class="profile-grid">

            {{-- Sidebar --}}
            <div class="profile-sidebar" style="display:flex;flex-direction:column;gap:16px;background:transparent !important;border:none !important;padding:0 !important;width:100% !important;max-width:280px !important;text-align:left !important;">
                {{-- Profile Card --}}
                <div class="glass-card" style="border-radius:20px;padding:28px;text-align:center;">
                    <div class="profile-avatar-ring" style="width:100px;height:100px;border-radius:50%;overflow:hidden;border:3px solid #c9a84c;margin:0 auto 16px;">
                        <img src="{{ $userProfile['profile_pic_url'] }}"
                             alt="Foto Profil" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;color:#fff;margin-bottom:20px;">{{ $userProfile['full_name'] ?? $userProfile['username'] }}</h3>

                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <a href="{{ route('customer.profile.edit') }}" class="btn-magnetic"
                           style="display:block;padding:11px;background:linear-gradient(135deg,#c9a84c,#a8832d);color:#111;font-weight:700;border-radius:12px;text-decoration:none;font-size:.85rem;transition:opacity .2s;"
                           onmouseenter="this.style.opacity='.85';" onmouseleave="this.style.opacity='1';">✏️ Edit Profil</a>
                        <button id="openHistoryModalBtn" class="btn-magnetic"
                                style="display:block;width:100%;padding:11px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.8);font-weight:600;border-radius:12px;cursor:pointer;font-size:.85rem;transition:all .2s;"
                                onmouseenter="this.style.background='rgba(255,255,255,.1)';" onmouseleave="this.style.background='rgba(255,255,255,.05)';">🛍️ Riwayat Pembelian</button>
                        <a href="{{ route('auth.logout') }}" class="btn-magnetic"
                           style="display:block;padding:11px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.15);color:#fca5a5;font-weight:600;border-radius:12px;text-decoration:none;font-size:.85rem;transition:all .2s;"
                           onmouseenter="this.style.background='rgba(239,68,68,.2)';" onmouseleave="this.style.background='rgba(239,68,68,.1)';">🚪 Logout</a>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="glass-card" style="background:linear-gradient(135deg,rgba(201,168,76,.08),rgba(168,131,45,.04));border-radius:16px;padding:20px;">
                    <h4 style="color:#c9a84c;font-weight:700;font-size:.875rem;margin-bottom:14px;">📊 Statistik Belanja</h4>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:.82rem;">
                        <span style="color:rgba(255,255,255,.5);">Total Pesanan:</span>
                        <span style="color:#c9a84c;font-weight:700;">{{ $totalOrders }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;font-size:.82rem;">
                        <span style="color:rgba(255,255,255,.5);">Total Belanja:</span>
                        <span style="color:#c9a84c;font-weight:700;">Rp {{ number_format($totalSpending, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="profile-content" style="display:flex;flex-direction:column;gap:16px;">

                {{-- Biodata --}}
                <div class="glass-card" style="border-radius:20px;padding:24px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid rgba(201,168,76,.15);">
                        <span style="color:#c9a84c;font-size:1.1rem;">👤</span>
                        <h3 style="font-family:'Playfair Display',serif;color:#c9a84c;font-size:1.1rem;font-weight:700;">Biodata Diri</h3>
                    </div>
                    @foreach([
                        ['label'=>'Nama Lengkap','value'=>$userProfile['username'] ?? '-'],
                        ['label'=>'Email','value'=>$userProfile['email'] ?? '-'],
                        ['label'=>'Tanggal Lahir','value'=>$userProfile['birth_date'] ? \Carbon\Carbon::parse($userProfile['birth_date'])->format('d M Y') : '-'],
                        ['label'=>'Jenis Kelamin','value'=>$userProfile['gender'] === 'L' ? 'Laki-laki' : ($userProfile['gender'] === 'P' ? 'Perempuan' : '-')],
                    ] as $i => $row)
                    <div style="display:flex;align-items:center;padding:11px 0;{{ $i < 3 ? 'border-bottom:1px solid rgba(255,255,255,.04);' : '' }}">
                        <span style="width:160px;color:rgba(255,255,255,.4);font-size:.82rem;flex-shrink:0;">{{ $row['label'] }}</span>
                        <span style="color:rgba(255,255,255,.85);font-weight:500;font-size:.875rem;">{{ $row['value'] }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Kontak & Alamat --}}
                <div class="glass-card" style="border-radius:20px;padding:24px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid rgba(201,168,76,.15);">
                        <span style="color:#c9a84c;font-size:1.1rem;">📋</span>
                        <h3 style="font-family:'Playfair Display',serif;color:#c9a84c;font-size:1.1rem;font-weight:700;">Informasi Kontak</h3>
                    </div>
                    <div style="display:flex;align-items:center;padding:11px 0;border-bottom:1px solid rgba(255,255,255,.04);">
                        <span style="width:160px;color:rgba(255,255,255,.4);font-size:.82rem;flex-shrink:0;">📞 Nomor HP</span>
                        <span style="color:rgba(255,255,255,.85);font-weight:500;font-size:.875rem;">{{ $userProfile['phone_number'] ?? '-' }}</span>
                    </div>
                    <div style="display:flex;align-items:flex-start;padding:14px 0;">
                        <span style="width:160px;color:rgba(255,255,255,.4);font-size:.82rem;flex-shrink:0;padding-top:2px;">📍 Alamat</span>
                        <div style="flex:1;">
                            @if($addresses && $addresses->count() > 0)
                                <div style="display:flex;flex-direction:column;gap:10px;">
                                    @foreach($addresses as $address)
                                    <div style="background:rgba(255,255,255,.03);border:1px solid {{ $address->is_primary ? 'rgba(201,168,76,.25)' : 'rgba(255,255,255,.06)' }};border-radius:12px;padding:14px;">
                                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                                            @if($address->label)
                                                <span style="padding:2px 10px;background:rgba(201,168,76,.1);color:#c9a84c;border-radius:999px;font-size:.75rem;font-weight:700;border:1px solid rgba(201,168,76,.2);">{{ $address->label }}</span>
                                            @endif
                                            @if($address->is_primary)
                                                <span style="padding:2px 10px;background:rgba(52,211,153,.1);color:#6ee7b7;border-radius:999px;font-size:.75rem;font-weight:700;border:1px solid rgba(52,211,153,.2);">✓ Utama</span>
                                            @endif
                                        </div>
                                        <p style="font-weight:600;color:rgba(255,255,255,.9);font-size:.875rem;margin-bottom:2px;">{{ $address->recipient_name }}</p>
                                        <p style="color:rgba(255,255,255,.45);font-size:.8rem;margin-bottom:2px;">{{ $address->phone_number }}</p>
                                        <p style="color:rgba(255,255,255,.65);font-size:.82rem;">{{ $address->address_line }}</p>
                                        <p style="color:rgba(255,255,255,.45);font-size:.78rem;">{{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                                        @if($address->notes)
                                            <p style="color:rgba(255,255,255,.35);font-size:.75rem;font-style:italic;margin-top:4px;">{{ $address->notes }}</p>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="color:rgba(255,255,255,.35);font-size:.875rem;">Belum ada alamat. Tambahkan di halaman <a href="{{ route('customer.profile.edit') }}" style="color:#c9a84c;">Edit Profil</a>.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('customer.modals.purchaseHistoryModal')
@include('customer.modals.reviewModal')
@include('customer.modals.viewReviewModal')
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
        gsap.from('.profile-header-section', {
            opacity: 0,
            y: -30,
            duration: 1,
            ease: 'power3.out'
        });

        gsap.from('.profile-sidebar', {
            opacity: 0,
            x: -50,
            duration: 1.2,
            ease: 'power3.out'
        });

        gsap.from('.profile-content', {
            opacity: 0,
            x: 50,
            duration: 1.2,
            ease: 'power3.out'
        });

        // ==================== 3. MAGNETIC BUTTONS ====================
        document.querySelectorAll('.btn-magnetic').forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                btn.style.transform = `translate(${x * 0.22}px, ${y * 0.22}px)`;
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

