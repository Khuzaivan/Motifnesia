@extends('customer.layouts.mainLayout')

@section('container')
{{-- Load CSS for AOS --}}
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />

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
        transform: translateY(-2px);
        border-color: rgba(201, 168, 76, 0.3) !important;
        box-shadow: 0 12px 30px rgba(201, 168, 76, 0.08);
    }
    
    .btn-magnetic {
        transition: transform 0.2s cubic-bezier(0.25, 1, 0.5, 1);
    }

    /* Input focus glow styling */
    input:not([type="checkbox"]):not([type="radio"]):focus,
    select:focus,
    textarea:focus {
        border-color: rgba(201, 168, 76, 0.6) !important;
        box-shadow: 0 0 12px rgba(201, 168, 76, 0.25) !important;
        background: rgba(255, 255, 255, 0.08) !important;
    }
</style>

<div style="position:relative;min-height:100vh;padding-top:88px;padding-bottom:60px;background:#131313;overflow:hidden;">
    {{-- Three.js Background Canvas --}}
    <canvas id="three-particles-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>

    <div style="position:relative;z-index:1;max-width:860px;margin:0 auto;padding:0 24px;">
        <div style="margin-bottom:28px;" class="edit-header-section">
            <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#fff;margin-bottom:4px;">Edit Profil</h1>
            <p style="color:rgba(255,255,255,.4);font-size:.875rem;">Perbarui informasi akun dan alamat pengiriman Anda</p>
        </div>

        <div class="main-form-panel-wrapper">
            <div class="glass-card main-form-panel" style="border-radius:24px;padding:32px;">
            @if (session('success'))
                <div style="background:rgba(52,211,153,.08);border:1px solid rgba(52,211,153,.2);color:#6ee7b7;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-size:.875rem;">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#fca5a5;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-size:.875rem;">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#fca5a5;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-size:.875rem;">
                    <ul style="margin:0;padding-left:16px;">
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;" class="form-grid">
                    {{-- Left Column --}}
                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div>
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Username</label>
                            <input type="text" name="username" value="{{ old('username', $userProfile['username']) }}" 
                                   style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;transition:border-color .2s;" 
                                   onfocus="this.style.borderColor='#c9a84c';" onblur="this.style.borderColor='rgba(255,255,255,.1)';">
                        </div>
                        <div>
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Nama Lengkap</label>
                            <input type="text" name="full_name" value="{{ $userProfile['full_name'] }}" 
                                   style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;transition:border-color .2s;"
                                   onfocus="this.style.borderColor='#c9a84c';" onblur="this.style.borderColor='rgba(255,255,255,.1)';">
                        </div>
                        <div>
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Email</label>
                            <input type="email" name="email" value="{{ $userProfile['email'] }}" 
                                   style="width:100%;padding:12px 16px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.05);border-radius:12px;color:rgba(255,255,255,.4);font-size:.875rem;cursor:not-allowed;" readonly> 
                        </div>
                        <div>
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Nomor HP</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number', $userProfile['phone_number']) }}" 
                                   style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;transition:border-color .2s;"
                                   onfocus="this.style.borderColor='#c9a84c';" onblur="this.style.borderColor='rgba(255,255,255,.1)';">
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div>
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Tanggal Lahir</label>
                            <input type="date" name="birth_date" value="{{ old('birth_date', (isset($userProfile['birth_date']) && $userProfile['birth_date']) ? date('Y-m-d', strtotime($userProfile['birth_date'])) : '') }}" 
                                   style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:rgba(255,255,255,.8);font-size:.875rem;outline:none;transition:border-color .2s;color-scheme:dark;"
                                   onfocus="this.style.borderColor='#c9a84c';" onblur="this.style.borderColor='rgba(255,255,255,.1)';">
                        </div>
                        <div>
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Jenis Kelamin</label>
                            <select name="gender" style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:rgba(255,255,255,.8);font-size:.875rem;outline:none;appearance:none;transition:border-color .2s;"
                                    onfocus="this.style.borderColor='#c9a84c';" onblur="this.style.borderColor='rgba(255,255,255,.1)';">
                                <option value="" style="background:#222;" {{ old('gender', $userProfile['gender']) === null ? 'selected' : '' }}>Pilih</option>
                                <option value="L" style="background:#222;" {{ old('gender', $userProfile['gender']) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" style="background:#222;" {{ old('gender', $userProfile['gender']) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Foto Profil</label>
                            <div style="display:flex;align-items:center;gap:16px;">
                                <img id="currentPhoto" src="{{ $userProfile['profile_pic_url'] }}" 
                                     alt="Foto Profil" style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid #c9a84c;box-shadow:0 0 15px rgba(201,168,76,.25);">
                                <div style="flex:1;">
                                    <input type="file" id="profilePhoto" name="profile_photo" accept="image/*" style="display:none;">
                                    <label for="profilePhoto" class="btn-magnetic" style="display:inline-block;padding:8px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.15);border-radius:999px;font-size:.82rem;font-weight:600;color:rgba(255,255,255,.8);cursor:pointer;transition:all .2s;"
                                           onmouseenter="this.style.background='rgba(255,255,255,.1)';" onmouseleave="this.style.background='rgba(255,255,255,.05)';">
                                        Pilih Foto Baru
                                    </label>
                                    <p style="color:rgba(255,255,255,.4);font-size:.75rem;margin-top:8px;"><span class="file-name-display">Belum ada file dipilih</span></p>
                                    <div id="previewWrapper" style="display:none;margin-top:8px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Alamat --}}
                <div style="margin-top:32px;padding-top:24px;border-top:1px solid rgba(255,255,255,.06);">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                        <h3 style="font-family:'Playfair Display',serif;color:#c9a84c;font-size:1.1rem;font-weight:700;">Daftar Alamat</h3>
                        <button type="button" id="addAddressBtn" class="btn-magnetic" style="padding:8px 16px;background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.25);color:#c9a84c;border-radius:999px;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .2s;"
                                onmouseenter="this.style.background='rgba(201,168,76,.2)';" onmouseleave="this.style.background='rgba(201,168,76,.1)';">
                            + Tambah Alamat
                        </button>
                    </div>

                    <div id="addressesList" style="display:flex;flex-direction:column;gap:12px;">
                        @if($addresses && $addresses->count() > 0)
                            @foreach($addresses as $address)
                                <div class="glass-card" style="border-radius:16px;padding:20px;display:flex;justify-content:space-between;align-items:flex-start;{{ $address->is_primary ? 'border-color: rgba(201,168,76,.35) !important;' : '' }}" data-address-id="{{ $address->id }}">
                                    <div>
                                        <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                                            @if($address->label)
                                                <span style="padding:2px 10px;background:rgba(201,168,76,.1);color:#c9a84c;border-radius:999px;font-size:.75rem;font-weight:700;border:1px solid rgba(201,168,76,.2);">{{ $address->label }}</span>
                                            @endif
                                            @if($address->is_primary)
                                                <span style="padding:2px 10px;background:rgba(52,211,153,.1);color:#6ee7b7;border-radius:999px;font-size:.75rem;font-weight:700;border:1px solid rgba(52,211,153,.2);">✓ Utama</span>
                                            @endif
                                        </div>
                                        <div style="font-weight:600;color:rgba(255,255,255,.9);font-size:.875rem;margin-bottom:2px;">{{ $address->recipient_name }}</div>
                                        <div style="color:rgba(255,255,255,.45);font-size:.8rem;margin-bottom:2px;">{{ $address->phone_number }}</div>
                                        <div style="color:rgba(255,255,255,.65);font-size:.82rem;">{{ $address->address_line }}</div>
                                        <div style="color:rgba(255,255,255,.45);font-size:.78rem;">{{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</div>
                                        @if($address->notes)
                                            <div style="color:rgba(255,255,255,.35);font-size:.75rem;font-style:italic;margin-top:4px;">{{ $address->notes }}</div>
                                        @endif
                                    </div>
                                    <div style="display:flex;flex-direction:column;gap:8px;">
                                        @if(!$address->is_primary)
                                            <button type="button" onclick="setPrimaryAddress({{ $address->id }})" class="btn-magnetic" style="padding:5px 12px;background:rgba(59,130,246,.1);color:#93c5fd;border:1px solid rgba(59,130,246,.2);border-radius:8px;font-size:.75rem;font-weight:600;cursor:pointer;">Jadikan Utama</button>
                                        @endif
                                        <button type="button" onclick="editAddress({{ $address->id }})" class="btn-magnetic" style="padding:5px 12px;background:rgba(245,158,11,.1);color:#fcd34d;border:1px solid rgba(245,158,11,.2);border-radius:8px;font-size:.75rem;font-weight:600;cursor:pointer;">Edit</button>
                                        <button type="button" onclick="deleteAddress({{ $address->id }})" class="btn-magnetic" style="padding:5px 12px;background:rgba(239,68,68,.1);color:#fca5a5;border:1px solid rgba(239,68,68,.2);border-radius:8px;font-size:.75rem;font-weight:600;cursor:pointer;">Hapus</button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="glass-card" style="border-radius:16px;padding:32px;text-align:center;">
                                <p style="color:rgba(255,255,255,.4);font-size:.875rem;">Belum ada alamat. Klik "Tambah Alamat" untuk menambahkan.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div style="display:flex;gap:16px;margin-top:32px;">
                    <button type="button" onclick="window.location='{{ route('customer.profile.index') }}'" class="btn-magnetic"
                            style="flex:1;padding:14px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.7);border-radius:999px;font-size:.9rem;font-weight:600;cursor:pointer;transition:all .2s;"
                            onmouseenter="this.style.background='rgba(255,255,255,.1)';" onmouseleave="this.style.background='rgba(255,255,255,.05)';">
                        Batal
                    </button>
                    <button type="submit" class="btn-magnetic"
                            style="flex:2;padding:14px;background:linear-gradient(135deg,#c9a84c,#a8832d);border:none;border-radius:999px;color:#111;font-size:.95rem;font-weight:700;cursor:pointer;letter-spacing:.04em;transition:opacity .2s;"
                            onmouseenter="this.style.opacity='.85';" onmouseleave="this.style.opacity='1';">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>

{{-- Modal Tambah/Edit Alamat --}}
<div id="addressModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.8);z-index:9999;align-items:center;justify-content:center;padding:24px;">
    <div class="glass-card" style="border-radius:24px;padding:32px;width:100%;max-width:600px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 40px rgba(0,0,0,.5);transform: scale(0.85); opacity: 0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
            <h3 id="modalTitle" style="font-family:'Playfair Display',serif;color:#fff;font-size:1.4rem;font-weight:700;">Tambah Alamat Baru</h3>
            <button type="button" onclick="closeAddressModal()" style="background:none;border:none;color:rgba(255,255,255,.4);font-size:1.5rem;cursor:pointer;">&times;</button>
        </div>

        <form id="addressForm">
            <input type="hidden" id="addressId" name="address_id">
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Label Alamat</label>
                    <input type="text" id="addressLabel" name="label" style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;" placeholder="Rumah / Kantor">
                </div>
                <div>
                    <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Nama Penerima <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="recipientName" name="recipient_name" required style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;" placeholder="Nama penerima">
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Nomor HP <span style="color:#ef4444;">*</span></label>
                <input type="text" id="addressPhone" name="phone_number" required style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;" placeholder="08xxxxxxxxxx">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Alamat Lengkap <span style="color:#ef4444;">*</span></label>
                <textarea id="addressLineInput" name="address_line" required rows="3" style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;resize:vertical;" placeholder="Jalan, No. Rumah, RT/RW"></textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Kota <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="addressCity" name="city" required style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;">
                </div>
                <div>
                    <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Provinsi <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="addressProvince" name="province" required style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;">
                </div>
                <div>
                    <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Kode Pos <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="addressPostal" name="postal_code" required style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;">
                </div>
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block;color:rgba(255,255,255,.7);font-size:.875rem;font-weight:600;margin-bottom:8px;">Catatan (Opsional)</label>
                <textarea id="addressNotes" name="notes" rows="2" style="width:100%;padding:12px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;resize:vertical;" placeholder="Patokan, instruksi khusus, dll"></textarea>
            </div>

            <div style="display:flex;gap:12px;">
                <button type="button" onclick="closeAddressModal()" class="btn-magnetic" style="flex:1;padding:14px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.7);border-radius:999px;font-size:.9rem;font-weight:600;cursor:pointer;">Batal</button>
                <button type="submit" class="btn-magnetic" style="flex:2;padding:14px;background:linear-gradient(135deg,#c9a84c,#a8832d);border:none;border-radius:999px;color:#111;font-size:.95rem;font-weight:700;cursor:pointer;">Simpan Alamat</button>
            </div>
        </form>
    </div>
</div>

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
        gsap.from('.edit-header-section', {
            opacity: 0,
            y: -30,
            duration: 1,
            ease: 'power3.out'
        });

        gsap.from('.main-form-panel-wrapper', {
            opacity: 0,
            y: 40,
            duration: 1,
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

    // ==================== 5. ORIGINAL FUNCTIONALITY (KEEP INTACT) ====================
    const fileInput = document.getElementById('profilePhoto');
    const fileNameDisplay = document.querySelector('.file-name-display');
    const previewWrapper = document.getElementById('previewWrapper');
    const currentPhoto = document.getElementById('currentPhoto');

    fileInput.addEventListener('change', function (e) {
        const file = this.files[0];
        if (!file) {
            fileNameDisplay.textContent = 'Belum ada file dipilih';
            previewWrapper.style.display = 'none';
            return;
        }
        fileNameDisplay.textContent = file.name;
        const reader = new FileReader();
        reader.onload = function (evt) {
            previewWrapper.style.display = 'block';
            previewWrapper.innerHTML = '<img src="' + evt.target.result + '" style="max-width:72px;height:72px;border-radius:50%;object-fit:cover;border:2px solid #c9a84c;display:block;">';
            currentPhoto.style.display = 'none';
        }
        reader.readAsDataURL(file);
    });

    const addressModal = document.getElementById('addressModal');
    const addressForm = document.getElementById('addressForm');
    const modalTitle = document.getElementById('modalTitle');
    let currentAddressId = null;
    const addressesById = @json($addressFormData);

    document.getElementById('addAddressBtn').addEventListener('click', function() {
        modalTitle.textContent = 'Tambah Alamat Baru';
        addressForm.reset();
        currentAddressId = null;
        document.getElementById('addressId').value = '';
        addressModal.style.display = 'flex';
        gsap.fromTo('#addressModal .glass-card', 
            { scale: 0.8, opacity: 0 }, 
            { scale: 1, opacity: 1, duration: 0.35, ease: 'back.out(1.15)' }
        );
    });

    function closeAddressModal() {
        gsap.to('#addressModal .glass-card', {
            scale: 0.8, opacity: 0, duration: 0.2, ease: 'power2.in',
            onComplete: () => {
                addressModal.style.display = 'none';
                addressForm.reset();
                currentAddressId = null;
            }
        });
    }

    addressForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        delete data.address_id; 

        const addressId = currentAddressId || document.getElementById('addressId').value;
        const url = addressId ? `/profile/addresses/${addressId}/update` : '/profile/addresses';
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) { alert(result.message); closeAddressModal(); location.reload(); } 
            else { alert('Error: ' + (result.message || 'Terjadi kesalahan')); }
        } catch (error) { console.error('Error:', error); alert('Terjadi kesalahan saat menyimpan alamat'); }
    });

    function editAddress(addressId) {
        const address = addressesById[addressId];
        if (!address) {
            alert('Alamat tidak ditemukan.');
            return;
        }

        modalTitle.textContent = 'Edit Alamat';
        currentAddressId = addressId;
        document.getElementById('addressId').value = addressId;
        document.getElementById('addressLabel').value = address.label || '';
        document.getElementById('recipientName').value = address.recipient_name || '';
        document.getElementById('addressPhone').value = address.phone_number || '';
        document.getElementById('addressLineInput').value = address.address_line || '';
        document.getElementById('addressCity').value = address.city || '';
        document.getElementById('addressProvince').value = address.province || '';
        document.getElementById('addressPostal').value = address.postal_code || '';
        document.getElementById('addressNotes').value = address.notes || '';
        addressModal.style.display = 'flex';
        gsap.fromTo('#addressModal .glass-card', 
            { scale: 0.8, opacity: 0 }, 
            { scale: 1, opacity: 1, duration: 0.35, ease: 'back.out(1.15)' }
        );
    }

    async function setPrimaryAddress(addressId) {
        if (!confirm('Jadikan alamat ini sebagai alamat utama?')) return;
        try {
            const response = await fetch(`/profile/addresses/${addressId}/set-primary`, {
                method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const result = await response.json();
            if (result.success) { alert(result.message); location.reload(); } 
            else { alert('Error: ' + result.message); }
        } catch (error) { console.error('Error:', error); alert('Terjadi kesalahan'); }
    }

    async function deleteAddress(addressId) {
        if (!confirm('Hapus alamat ini?')) return;
        try {
            const response = await fetch(`/profile/addresses/${addressId}`, {
                method: 'DELETE', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const result = await response.json();
            if (result.success) { alert(result.message); location.reload(); } 
            else { alert('Error: ' + result.message); }
        } catch (error) { console.error('Error:', error); alert('Terjadi kesalahan'); }
    }
</script>
@endpush

