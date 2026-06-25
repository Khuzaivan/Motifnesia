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

    .retur-card:hover {
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

    <div style="position:relative;z-index:1;max-width:760px;margin:0 auto;padding:0 24px;">

        <div style="margin-bottom:28px;" class="retur-header-section">
            <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#fff;margin-bottom:4px;">Ajukan Retur Produk</h1>
            <p style="color:rgba(255,255,255,.4);font-size:.875rem;">Lengkapi formulir untuk mengajukan pengembalian barang</p>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            {{-- Product Summary --}}
            <div class="retur-step-wrapper">
                <div class="glass-card retur-card" style="padding:24px;">
                    <h3 style="color:#c9a84c;font-weight:700;font-size:.95rem;margin-bottom:16px;font-family:'Playfair Display',serif;">📦 Informasi Produk</h3>
                    <div style="display:flex;align-items:center;gap:16px;">
                        <img src="{{ $orderItem->produk->image_url }}" alt="{{ $orderItem->produk->nama_produk }}"
                             style="width:72px;height:72px;object-fit:cover;border-radius:12px;border:1px solid rgba(255,255,255,.08);flex-shrink:0;">
                        <div>
                            <h4 style="font-weight:700;color:rgba(255,255,255,.9);margin-bottom:6px;">{{ $orderItem->produk->nama_produk }}</h4>
                            <p style="color:rgba(255,255,255,.45);font-size:.82rem;margin-bottom:2px;">Ukuran: {{ $orderItem->ukuran }} &nbsp;|&nbsp; Qty: {{ $orderItem->qty }}</p>
                            <p style="color:#c9a84c;font-size:.82rem;font-weight:600;">Rp {{ number_format($orderItem->harga, 0, ',', '.') }}</p>
                            <p style="color:rgba(255,255,255,.3);font-size:.78rem;">Order ID: #{{ $order->id }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Return Form --}}
            <div class="retur-step-wrapper">
                <form action="{{ route('customer.returns.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="order_item_id" value="{{ $orderItem->id }}">

                    <div class="glass-card retur-card" style="padding:24px;margin-bottom:16px;">
                        {{-- Reason --}}
                        <div style="margin-bottom:18px;">
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Alasan Retur <span style="color:#ef4444;">*</span></label>
                            <select name="reason" required
                                    style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:rgba(255,255,255,.8);font-size:.875rem;outline:none;appearance:none;transition:border-color .2s;"
                                    onfocus="this.style.borderColor='#c9a84c';"
                                    onblur="this.style.borderColor='rgba(255,255,255,.1)';">
                                <option value="" style="background:#222;color:#fff;">-- Pilih Alasan --</option>
                                <option value="Ukuran tidak sesuai" style="background:#222;color:#fff;">Ukuran tidak sesuai</option>
                                <option value="Barang rusak/cacat" style="background:#222;color:#fff;">Barang rusak/cacat</option>
                                <option value="Salah kirim produk" style="background:#222;color:#fff;">Salah kirim produk</option>
                                <option value="Tidak sesuai deskripsi" style="background:#222;color:#fff;">Tidak sesuai deskripsi</option>
                                <option value="Berubah pikiran" style="background:#222;color:#fff;">Berubah pikiran</option>
                                <option value="Lainnya" style="background:#222;color:#fff;">Lainnya</option>
                            </select>
                            @error('reason') <span style="color:#fca5a5;font-size:.78rem;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                        </div>

                        {{-- Description --}}
                        <div style="margin-bottom:18px;">
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Keterangan Tambahan</label>
                            <textarea name="description" rows="4" placeholder="Jelaskan detail masalah yang Anda alami..."
                                      style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;resize:vertical;font-family:inherit;transition:border-color .2s;"
                                      onfocus="this.style.borderColor='#c9a84c';"
                                      onblur="this.style.borderColor='rgba(255,255,255,.1)';">{{ old('description') }}</textarea>
                            <p style="color:rgba(255,255,255,.3);font-size:.75rem;margin-top:4px;">Maksimal 1000 karakter</p>
                            @error('description') <span style="color:#fca5a5;font-size:.78rem;">{{ $message }}</span> @enderror
                        </div>

                        {{-- Photo Upload --}}
                        <div style="margin-bottom:18px;">
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Foto Bukti (Opsional)</label>
                            <label for="photo_proof"
                                   style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:rgba(255,255,255,.03);border:2px dashed rgba(255,255,255,.12);border-radius:12px;cursor:pointer;transition:border-color .2s;"
                                   onmouseenter="this.style.borderColor='rgba(201,168,76,.4)';"
                                   onmouseleave="this.style.borderColor='rgba(255,255,255,.12)';">
                                <span style="font-size:1.5rem;">📷</span>
                                <span id="fileText" style="color:rgba(255,255,255,.45);font-size:.875rem;">Klik untuk pilih foto</span>
                            </label>
                            <input type="file" name="photo_proof" id="photo_proof" accept="image/jpeg,image/png,image/jpg" style="display:none;">
                            <div id="preview-container" style="display:none;margin-top:12px;">
                                <img id="image-preview" src="" alt="Preview" style="max-width:160px;border-radius:10px;border:1px solid rgba(255,255,255,.1);">
                            </div>
                            <p style="color:rgba(255,255,255,.3);font-size:.75rem;margin-top:6px;">Format: JPG, PNG. Maks. 2MB</p>
                            @error('photo_proof') <span style="color:#fca5a5;font-size:.78rem;">{{ $message }}</span> @enderror
                        </div>

                        {{-- Action Type --}}
                        <div>
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:12px;">Tipe Pengembalian <span style="color:#ef4444;">*</span></label>
                            <div style="display:flex;flex-direction:column;gap:10px;">
                                <label style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:12px;cursor:pointer;transition:border-color .2s;"
                                       onmouseenter="this.style.borderColor='rgba(201,168,76,.3)';"
                                       onmouseleave="this.style.borderColor='rgba(255,255,255,.08)';">
                                    <input type="radio" name="action_type" value="Refund" checked required style="accent-color:#c9a84c;width:16px;height:16px;cursor:pointer;">
                                    <div>
                                        <p style="font-weight:700;color:rgba(255,255,255,.9);font-size:.875rem;">💰 Refund (Pengembalian Uang)</p>
                                        <p style="color:rgba(255,255,255,.4);font-size:.78rem;margin-top:2px;">Dana akan dikembalikan ke rekening Anda</p>
                                    </div>
                                </label>
                                <label style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:12px;cursor:pointer;transition:border-color .2s;"
                                       onmouseenter="this.style.borderColor='rgba(201,168,76,.3)';"
                                       onmouseleave="this.style.borderColor='rgba(255,255,255,.08)';">
                                    <input type="radio" name="action_type" value="Tukar Barang" required style="accent-color:#c9a84c;width:16px;height:16px;cursor:pointer;">
                                    <div>
                                        <p style="font-weight:700;color:rgba(255,255,255,.9);font-size:.875rem;">🔄 Tukar Barang</p>
                                        <p style="color:rgba(255,255,255,.4);font-size:.78rem;margin-top:2px;">Tukar dengan produk yang sama (ukuran/warna berbeda)</p>
                                    </div>
                                </label>
                            </div>
                            @error('action_type') <span style="color:#fca5a5;font-size:.78rem;margin-top:4px;display:block;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Info Box --}}
                    <div class="glass-card" style="background:rgba(201,168,76,.06) !important;border:1px solid rgba(201,168,76,.18) !important;border-radius:16px;padding:20px;margin-bottom:20px;">
                        <h4 style="color:#c9a84c;font-weight:700;font-size:.875rem;margin-bottom:12px;font-family:'Playfair Display',serif;">ℹ️ Ketentuan Retur:</h4>
                        <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:7px;">
                            @foreach([
                                'Retur hanya dapat dilakukan maksimal <strong>7 hari</strong> sejak barang diterima',
                                'Barang harus dalam kondisi <strong>belum dipakai</strong> dan masih ada tag/label',
                                'Proses verifikasi memakan waktu <strong>1-3 hari kerja</strong>',
                                'Jika disetujui, refund diproses dalam <strong>5-7 hari kerja</strong>',
                                'Ongkir retur ditanggung customer (kecuali kesalahan toko)',
                            ] as $point)
                            <li style="display:flex;gap:8px;color:rgba(255,255,255,.6);font-size:.82rem;line-height:1.5;">
                                <span style="color:#c9a84c;flex-shrink:0;">•</span>
                                <span>{!! $point !!}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Buttons --}}
                    <div style="display:flex;gap:12px;">
                        <button type="button" onclick="window.history.back()" class="btn-magnetic"
                                style="flex:1;padding:13px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.6);border-radius:999px;font-size:.9rem;font-weight:600;cursor:pointer;transition:all .2s;"
                                onmouseenter="this.style.background='rgba(255,255,255,.1)';"
                                onmouseleave="this.style.background='rgba(255,255,255,.05)';">Batal</button>
                        <button type="submit" class="btn-magnetic"
                                style="flex:2;padding:13px;background:linear-gradient(135deg,#c9a84c,#a8832d);border:none;border-radius:999px;color:#111;font-size:.95rem;font-weight:700;cursor:pointer;letter-spacing:.03em;transition:opacity .2s;"
                                onmouseenter="this.style.opacity='.85';this.style.transform='translateY(-1px)';"
                                onmouseleave="this.style.opacity='1';this.style.transform='';">Ajukan Retur</button>
                    </div>
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
document.getElementById('photo_proof').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image-preview').src = e.target.result;
            document.getElementById('preview-container').style.display = 'block';
        };
        reader.readAsDataURL(file);
        document.getElementById('fileText').textContent = file.name;
    }
});

document.addEventListener('DOMContentLoaded', () => {
    // ==================== 1. INITIALIZE AOS ====================
    AOS.init({
        once: true,
        duration: 800,
        easing: 'ease-out-cubic'
    });

    // ==================== 2. GSAP ENTRANCE ANIMATION ====================
    gsap.from('.retur-header-section', {
        opacity: 0,
        y: -30,
        duration: 1,
        ease: 'power3.out'
    });

    gsap.from('.retur-step-wrapper', {
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
