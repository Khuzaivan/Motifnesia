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

    .return-card:hover {
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

    <div style="position:relative;z-index:1;max-width:900px;margin:0 auto;padding:0 24px;">

        <div style="margin-bottom:28px;" class="returns-header-section">
            <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#fff;margin-bottom:4px;">Retur Saya</h1>
            <p style="color:rgba(255,255,255,.4);font-size:.875rem;">Lacak status pengajuan retur produk Anda</p>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            @forelse($returns as $return)
            <div class="return-card-wrapper">
                <div class="glass-card return-card" style="padding:24px;">
                    {{-- Header --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                        <div>
                            <span style="font-weight:700;color:rgba(255,255,255,.9);font-size:.95rem;">Retur #{{ $return->id }}</span>
                            <span style="color:rgba(255,255,255,.35);font-size:.78rem;margin-left:10px;">{{ $return->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        @php
                            $statusColor = match($return->status) {
                                'Pending' => ['bg'=>'rgba(245,158,11,.12)','color'=>'#fcd34d','border'=>'rgba(245,158,11,.25)'],
                                'Disetujui' => ['bg'=>'rgba(52,211,153,.1)','color'=>'#6ee7b7','border'=>'rgba(52,211,153,.2)'],
                                'Ditolak' => ['bg'=>'rgba(239,68,68,.1)','color'=>'#fca5a5','border'=>'rgba(239,68,68,.2)'],
                                'Selesai' => ['bg'=>'rgba(201,168,76,.1)','color'=>'#c9a84c','border'=>'rgba(201,168,76,.2)'],
                                default => ['bg'=>'rgba(255,255,255,.05)','color'=>'rgba(255,255,255,.6)','border'=>'rgba(255,255,255,.1)'],
                            };
                        @endphp
                        <span style="padding:4px 14px;border-radius:999px;font-size:.78rem;font-weight:700;background:{{ $statusColor['bg'] }};color:{{ $statusColor['color'] }};border:1px solid {{ $statusColor['border'] }};">{{ $return->status }}</span>
                    </div>

                    {{-- Product Info --}}
                    <div style="display:flex;align-items:center;gap:16px;padding:16px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:12px;margin-bottom:16px;">
                        <img src="{{ $return->produk->image_url }}" alt="{{ $return->produk->nama_produk }}"
                             style="width:64px;height:64px;object-fit:cover;border-radius:10px;border:1px solid rgba(255,255,255,.08);flex-shrink:0;">
                        <div>
                            <p style="font-weight:600;color:rgba(255,255,255,.9);margin-bottom:4px;">{{ $return->produk->nama_produk }}</p>
                            <p style="color:rgba(255,255,255,.45);font-size:.82rem;">Ukuran: {{ $return->orderItem->ukuran }} &nbsp;|&nbsp; Qty: {{ $return->orderItem->qty }}</p>
                            <p style="color:rgba(255,255,255,.35);font-size:.78rem;">Order ID: #{{ $return->order_id }}</p>
                        </div>
                    </div>

                    {{-- Details --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;">
                        <div style="background:rgba(255,255,255,.03);border-radius:10px;padding:12px;">
                            <p style="color:rgba(255,255,255,.35);font-size:.75rem;margin-bottom:4px;">Alasan</p>
                            <p style="color:rgba(255,255,255,.8);font-size:.875rem;font-weight:500;">{{ $return->reason }}</p>
                        </div>
                        <div style="background:rgba(255,255,255,.03);border-radius:10px;padding:12px;">
                            <p style="color:rgba(255,255,255,.35);font-size:.75rem;margin-bottom:4px;">Tipe Retur</p>
                            <p style="color:rgba(255,255,255,.8);font-size:.875rem;font-weight:500;">{{ $return->action_type }}</p>
                        </div>
                        @if($return->description)
                        <div style="background:rgba(255,255,255,.03);border-radius:10px;padding:12px;grid-column:1/-1;">
                            <p style="color:rgba(255,255,255,.35);font-size:.75rem;margin-bottom:4px;">Keterangan</p>
                            <p style="color:rgba(255,255,255,.8);font-size:.875rem;">{{ $return->description }}</p>
                        </div>
                        @endif
                        <div style="background:rgba(255,255,255,.03);border-radius:10px;padding:12px;">
                            <p style="color:rgba(255,255,255,.35);font-size:.75rem;margin-bottom:4px;">Jumlah Refund</p>
                            <p style="color:#c9a84c;font-size:.95rem;font-weight:700;">Rp {{ number_format($return->refund_amount, 0, ',', '.') }}</p>
                        </div>
                        @if($return->refund_status !== 'Belum')
                        <div style="background:rgba(255,255,255,.03);border-radius:10px;padding:12px;">
                            <p style="color:rgba(255,255,255,.35);font-size:.75rem;margin-bottom:4px;">Status Refund</p>
                            <p style="color:{{ $return->refund_status === 'Selesai' ? '#6ee7b7' : '#fcd34d' }};font-size:.875rem;font-weight:600;">{{ $return->refund_status }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Photo Proof --}}
                    @if($return->photo_proof)
                    <div style="margin-bottom:16px;">
                        <p style="color:rgba(255,255,255,.4);font-size:.78rem;margin-bottom:8px;">Foto Bukti:</p>
                        <img src="{{ asset('storage/' . $return->photo_proof) }}" alt="Bukti"
                             onclick="openImgModal(this.src)"
                             style="width:100px;height:100px;object-fit:cover;border-radius:10px;cursor:pointer;border:1px solid rgba(255,255,255,.1);transition:transform .2s;"
                             onmouseenter="this.style.transform='scale(1.05)';"
                             onmouseleave="this.style.transform='';">
                    </div>
                    @endif

                    @if($return->courier_photo)
                    <div style="margin-bottom:16px;">
                        <p style="color:rgba(255,255,255,.4);font-size:.78rem;margin-bottom:8px;">Bukti Serah ke Kurir:</p>
                        <img src="{{ asset('storage/' . $return->courier_photo) }}" alt="Bukti kurir"
                             onclick="openImgModal(this.src)"
                             style="width:100px;height:100px;object-fit:cover;border-radius:10px;cursor:pointer;border:1px solid rgba(52,211,153,.22);transition:transform .2s;"
                             onmouseenter="this.style.transform='scale(1.05)';"
                             onmouseleave="this.style.transform='';">
                        @if($return->courier_note)
                            <p style="color:rgba(255,255,255,.45);font-size:.82rem;margin-top:8px;">{{ $return->courier_note }}</p>
                        @endif
                    </div>
                    @endif

                    {{-- Admin Note --}}
                    @if($return->admin_note)
                    <div style="background:rgba(201,168,76,.06);border:1px solid rgba(201,168,76,.15);border-radius:10px;padding:12px;margin-bottom:16px;">
                        <p style="color:#c9a84c;font-size:.8rem;font-weight:700;margin-bottom:4px;">💬 Catatan Admin</p>
                        <p style="color:rgba(255,255,255,.65);font-size:.85rem;">{{ $return->admin_note }}</p>
                    </div>
                    @endif

                    {{-- Actions --}}
                    @if($return->status === 'Pending')
                        <form action="{{ route('customer.returns.cancel', $return->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" onclick="return confirm('Yakin ingin membatalkan retur ini?')" class="btn-magnetic"
                                    style="padding:9px 24px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#fca5a5;border-radius:999px;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .2s;"
                                    onmouseenter="this.style.background='rgba(239,68,68,.2)';"
                                    onmouseleave="this.style.background='rgba(239,68,68,.1)';">
                                Batalkan Retur
                            </button>
                        </form>
                    @elseif($return->status === 'Disetujui')
                        @if(!$return->courier_photo)
                            <div style="background:rgba(52,211,153,.06);border:1px solid rgba(52,211,153,.15);border-radius:10px;padding:14px 16px;color:#6ee7b7;font-size:.85rem;margin-bottom:12px;">
                                Retur disetujui. Setelah barang diserahkan ke kurir, upload foto buktinya di bawah ini.
                            </div>
                            <form action="{{ route('customer.returns.courierProof', $return->id) }}" method="POST" enctype="multipart/form-data" style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:12px;padding:16px;display:flex;flex-direction:column;gap:12px;">
                                @csrf
                                <input type="file" name="courier_photo" accept="image/jpeg,image/png,image/jpg" required
                                       style="width:100%;color:rgba(255,255,255,.65);font-size:.82rem;">
                                <textarea name="courier_note" rows="2" placeholder="Catatan opsional, contoh: nomor resi atau nama kurir"
                                          style="width:100%;padding:10px 12px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.1);border-radius:10px;color:#fff;font-size:.82rem;outline:none;resize:vertical;"></textarea>
                                <button type="submit" class="btn-magnetic"
                                        style="width:max-content;padding:9px 24px;background:linear-gradient(135deg,#c9a84c,#a8832d);border:none;color:#111;border-radius:999px;font-size:.82rem;font-weight:800;cursor:pointer;">
                                    Kirim Bukti Kurir
                                </button>
                            </form>
                        @else
                            <div style="background:rgba(52,211,153,.06);border:1px solid rgba(52,211,153,.15);border-radius:10px;padding:10px 16px;color:#6ee7b7;font-size:.85rem;">
                                Bukti kurir sudah dikirim. Menunggu admin memproses refund atau penukaran.
                            </div>
                        @endif
                    @elseif($return->status === 'Selesai')
                        <div style="background:rgba(201,168,76,.06);border:1px solid rgba(201,168,76,.15);border-radius:10px;padding:10px 16px;color:#c9a84c;font-size:.85rem;">
                            ✅ Retur selesai diproses. Terima kasih!
                        </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="glass-card" style="padding:64px;text-align:center;">
                <div style="font-size:3.5rem;margin-bottom:16px;">📦</div>
                <h3 style="font-family:'Playfair Display',serif;color:rgba(255,255,255,.8);font-size:1.4rem;margin-bottom:8px;">Belum Ada Retur</h3>
                <p style="color:rgba(255,255,255,.4);margin-bottom:24px;">Anda belum pernah mengajukan retur produk</p>
                <a href="{{ route('customer.profile.index') }}" class="btn-magnetic" style="display:inline-block;padding:11px 32px;background:linear-gradient(135deg,#c9a84c,#a8832d);color:#111;font-weight:700;border-radius:999px;text-decoration:none;font-size:.875rem;">Kembali ke Profil</a>
            </div>
            @endforelse
        </div>

        @if($returns->hasPages())
            <div style="margin-top:24px;">{{ $returns->links() }}</div>
        @endif
    </div>
</div>

{{-- Image Modal --}}
<div id="imgModal" onclick="closeImgModal()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:9999;align-items:center;justify-content:center;cursor:pointer;padding:24px;">
    <img id="modalImg" style="max-width:95%;max-height:90vh;border-radius:16px;object-fit:contain;box-shadow:0 10px 40px rgba(0,0,0,.8);border:1px solid rgba(255,255,255,.05);transition:all .3s;">
</div>
@endsection

@push('scripts')
{{-- Load Animation Libraries --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
function openImgModal(src) {
    const m = document.getElementById('imgModal');
    const img = document.getElementById('modalImg');
    img.src = src;
    m.style.display = 'flex';
    gsap.fromTo(img, { scale: 0.9, opacity: 0 }, { scale: 1, opacity: 1, duration: 0.3, ease: 'back.out(1.5)' });
}
function closeImgModal() {
    const m = document.getElementById('imgModal');
    const img = document.getElementById('modalImg');
    gsap.to(img, { scale: 0.9, opacity: 0, duration: 0.2, ease: 'power2.in', onComplete: () => {
        m.style.display = 'none';
    }});
}

document.addEventListener('DOMContentLoaded', () => {
    // ==================== 1. INITIALIZE AOS ====================
    AOS.init({
        once: true,
        duration: 800,
        easing: 'ease-out-cubic'
    });

    // ==================== 2. GSAP ENTRANCE ANIMATION ====================
    gsap.from('.returns-header-section', {
        opacity: 0,
        y: -30,
        duration: 1,
        ease: 'power3.out'
    });

    gsap.from('.return-card-wrapper', {
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
