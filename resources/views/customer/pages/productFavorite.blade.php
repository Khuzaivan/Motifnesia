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
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    
    html.customer-light .glass-card {
        background: rgba(78, 61, 37, 0.02) !important;
        border: 1px solid rgba(78, 61, 37, 0.08) !important;
    }

    .fav-card:hover {
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

        <div style="margin-bottom:28px;" class="fav-header-section">
            <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#fff;margin-bottom:4px;">Favorite</h1>
            <p style="color:rgba(255,255,255,.4);font-size:.875rem;">Koleksi produk batik pilihan Anda</p>
        </div>

        @if(session('success'))
            <div style="background:rgba(52,211,153,.08);border:1px solid rgba(52,211,153,.2);color:#6ee7b7;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-size:.875rem;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#fca5a5;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-size:.875rem;">{{ session('error') }}</div>
        @endif

        <div class="fav-list" style="display:flex;flex-direction:column;gap:12px;">
            @forelse($favorites as $favorite)
                @php
                    $hargaDiskon = $favorite->produk->harga_diskon ?? $favorite->produk->harga;
                    $diskonPersen = $favorite->produk->diskon_persen ?? 0;
                @endphp
                <div class="fav-card-wrapper">
                    <div class="fav-card glass-card" style="border-radius:16px;padding:16px;display:flex;align-items:center;gap:16px;">
                    
                    {{-- Image --}}
                    <img src="{{ $favorite->produk->image_url }}" alt="{{ $favorite->produk->nama_produk }}"
                         style="width:72px;height:72px;object-fit:cover;border-radius:12px;border:1px solid rgba(255,255,255,.08);flex-shrink:0;">

                    {{-- Info --}}
                    <div style="flex:1;">
                        <h3 style="font-weight:600;font-size:1rem;color:rgba(255,255,255,.9);margin-bottom:4px;">{{ $favorite->produk->nama_produk }}</h3>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="color:#c9a84c;font-weight:600;font-size:.9rem;">Rp {{ number_format($hargaDiskon, 0, ',', '.') }}</span>
                            @if($diskonPersen > 0)
                                <span style="background:rgba(201,168,76,.15);color:#c9a84c;font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px;border:1px solid rgba(201,168,76,.25);">-{{ $diskonPersen }}%</span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                        <a href="{{ route('customer.favorites.addToCart', $favorite->id) }}" class="btn-magnetic"
                           style="padding:9px 18px;background:linear-gradient(135deg,#c9a84c,#a8832d);border-radius:10px;color:#111;font-size:.82rem;font-weight:700;text-decoration:none;transition:opacity .2s;"
                           onmouseenter="this.style.opacity='.85';" onmouseleave="this.style.opacity='1';">Tambah Keranjang</a>

                        <button type="button" onclick="deleteFavorite({{ $favorite->id }})" class="btn-magnetic"
                                style="width:36px;height:36px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);border-radius:10px;color:#ef4444;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;"
                                onmouseenter="this.style.background='rgba(239,68,68,.25)';" onmouseleave="this.style.background='rgba(239,68,68,.1)';">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
                </div>
            @empty
                <div class="glass-card" style="border-radius:20px;padding:64px;text-align:center;">
                    <div style="font-size:3.5rem;margin-bottom:16px;">❤️</div>
                    <h3 style="font-family:'Playfair Display',serif;color:rgba(255,255,255,.8);font-size:1.4rem;margin-bottom:8px;">Belum Ada Produk Favorite</h3>
                    <p style="color:rgba(255,255,255,.4);margin-bottom:24px;">Tambahkan batik pilihan Anda ke favorite</p>
                    <a href="{{ route('customer.home') }}" class="btn-magnetic" style="display:inline-block;padding:12px 36px;background:linear-gradient(135deg,#c9a84c,#a8832d);color:#111;font-weight:700;border-radius:999px;text-decoration:none;font-size:.9rem;">Mulai Belanja</a>
                </div>
            @endforelse

            @if($favorites->count() > 0)
                <div style="text-align:center;margin-top:24px;">
                    <a href="{{ route('customer.home') }}" class="btn-magnetic"
                       style="display:inline-block;padding:12px 36px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.8);font-weight:600;border-radius:999px;text-decoration:none;font-size:.9rem;transition:all .2s;"
                       onmouseenter="this.style.background='rgba(255,255,255,.1)';" onmouseleave="this.style.background='rgba(255,255,255,.05)';">
                        Mulai Belanja Lagi
                    </a>
                </div>
            @endif
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
    function deleteFavorite(id) {
        if (!confirm('Hapus produk dari favorite?')) return;
        let url = '{{ url("/favorites") }}/' + id;
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(res => {
            if (res.ok || res.redirected) {
                location.reload();
            } else {
                alert('Gagal menghapus favorite.');
            }
        }).catch(err => alert('Terjadi kesalahan: ' + err.message));
    }

    document.addEventListener('DOMContentLoaded', () => {
        // ==================== 1. INITIALIZE AOS ====================
        AOS.init({
            once: true,
            duration: 800,
            easing: 'ease-out-cubic'
        });

        // ==================== 2. GSAP ENTRANCE ANIMATION ====================
        gsap.from('.fav-header-section', {
            opacity: 0,
            y: -30,
            duration: 1,
            ease: 'power3.out'
        });

        gsap.from('.fav-card-wrapper', {
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

