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

    .item-card {
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .item-card:hover {
        transform: translateY(-4px);
        border-color: rgba(201, 168, 76, 0.3) !important;
        box-shadow: 0 12px 30px rgba(201, 168, 76, 0.08);
    }
    
    .btn-magnetic {
        transition: transform 0.2s cubic-bezier(0.25, 1, 0.5, 1);
    }
</style>

<div style="position:relative;min-height:100vh;padding-top:88px;padding-bottom:60px;background:#131313;overflow:hidden;">
    {{-- Three.js Background Canvas --}}
    <canvas id="three-particles-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;pointer-events:none;"></canvas>

    <div style="position:relative;z-index:1;max-width:900px;margin:0 auto;padding:0 24px;">

        {{-- Page Header --}}
        <div style="margin-bottom:28px;" class="cart-header-section">
            <h1 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;color:#fff;margin-bottom:4px;">Keranjang Belanja</h1>
            <p style="color:rgba(255,255,255,.4);font-size:.875rem;">Periksa dan konfirmasi produk pilihan Anda</p>
        </div>

        @if($items->isEmpty())
            <div class="glass-card" style="border-radius:20px;padding:64px;text-align:center;">
                <div style="font-size:3.5rem;margin-bottom:16px;">🛒</div>
                <h3 style="font-family:'Playfair Display',serif;color:rgba(255,255,255,.8);font-size:1.4rem;margin-bottom:8px;">Keranjang Masih Kosong</h3>
                <p style="color:rgba(255,255,255,.4);margin-bottom:24px;">Mulai tambahkan produk batik favorit Anda</p>
                <a href="{{ route('customer.home') }}" class="btn-magnetic" style="display:inline-block;padding:12px 36px;background:linear-gradient(135deg,#c9a84c,#a8832d);color:#111;font-weight:700;border-radius:999px;text-decoration:none;font-size:.9rem;letter-spacing:.04em;">Mulai Belanja</a>
            </div>
        @else
        <form action="{{ route('customer.cart.checkout') }}" method="POST">
            @csrf

            {{-- Cart Items --}}
            <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:24px;">
                @foreach ($items as $item)
                @php
                    $hargaDiskon = $item->produk->harga_diskon ?? $item->produk->harga;
                    $diskonPersen = $item->produk->diskon_persen ?? 0;
                @endphp
                <div class="item-card-wrapper">
                    <div class="item-card glass-card" data-price="{{ $hargaDiskon }}" data-qty="{{ $item->qty }}">

                    {{-- Checkbox --}}
                    <input type="checkbox" name="selected_items[]" value="{{ $item->id }}"
                           class="item-checkbox" style="width:18px;height:18px;accent-color:#c9a84c;flex-shrink:0;cursor:pointer;">

                    {{-- Image --}}
                    @if($item->produk)
                    <img src="{{ $item->produk->image_url }}" alt="{{ $item->produk->nama_produk }}"
                         style="width:76px;height:76px;object-fit:cover;border-radius:12px;flex-shrink:0;border:1px solid rgba(255,255,255,.08);">
                    @else
                    <div style="width:76px;height:76px;background:#2a2a2a;border-radius:12px;flex-shrink:0;"></div>
                    @endif

                    {{-- Product Info --}}
                    <div style="flex:1;">
                        <div style="font-weight:600;font-size:1rem;color:rgba(255,255,255,.9);margin-bottom:4px;">
                            {{ $item->produk->nama_produk }} <span style="color:rgba(255,255,255,.4);font-size:.85rem;">- {{ $item->ukuran }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="color:#c9a84c;font-weight:600;font-size:.9rem;">Rp {{ number_format($hargaDiskon, 0, ',', '.') }}</span>
                            @if($diskonPersen > 0)
                                <span style="background:rgba(201,168,76,.15);color:#c9a84c;font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px;border:1px solid rgba(201,168,76,.25);">-{{ $diskonPersen }}%</span>
                            @endif
                        </div>
                    </div>

                    {{-- Qty Controls --}}
                    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                        <button type="button" class="btn-qty-minus" data-cart-id="{{ $item->id }}" data-qty="{{ $item->qty }}"
                                style="width:32px;height:32px;border-radius:8px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.05);color:rgba(255,255,255,.8);font-size:1.2rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;"
                                onmouseenter="this.style.borderColor='#c9a84c';this.style.color='#c9a84c';"
                                onmouseleave="this.style.borderColor='rgba(255,255,255,.12)';this.style.color='rgba(255,255,255,.8)';">−</button>
                        <input type="text" value="{{ $item->qty }}" readonly class="qty-display"
                               style="width:44px;text-align:center;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.12);border-radius:8px;padding:6px 4px;color:#fff;font-weight:600;font-size:.9rem;">
                        <button type="button" class="btn-qty-plus" data-cart-id="{{ $item->id }}" data-qty="{{ $item->qty }}"
                                style="width:32px;height:32px;border-radius:8px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.05);color:rgba(255,255,255,.8);font-size:1.2rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;"
                                onmouseenter="this.style.borderColor='#c9a84c';this.style.color='#c9a84c';"
                                onmouseleave="this.style.borderColor='rgba(255,255,255,.12)';this.style.color='rgba(255,255,255,.8)';">+</button>
                    </div>

                    {{-- Subtotal --}}
                    <div style="min-width:120px;text-align:right;flex-shrink:0;">
                        <div style="font-weight:700;color:#c9a84c;font-size:1rem;">Rp {{ number_format($hargaDiskon * $item->qty, 0, ',', '.') }}</div>
                    </div>

                    {{-- Delete --}}
                    <button type="button" class="btn-delete-item btn-magnetic" data-cart-id="{{ $item->id }}"
                            style="width:36px;height:36px;border:none;background:rgba(239,68,68,.1);border-radius:8px;color:#ef4444;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .2s;"
                            onmouseenter="this.style.background='rgba(239,68,68,.25)';"
                            onmouseleave="this.style.background='rgba(239,68,68,.1)';">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
                </div>
                @endforeach
            </div>

            {{-- Total & Checkout --}}
            <div class="glass-card" style="border-radius:20px;padding:28px;" data-aos="fade-up">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <span style="color:rgba(255,255,255,.6);font-size:1rem;">Total Terpilih:</span>
                    <span id="total-display" style="font-size:1.5rem;font-weight:700;color:#c9a84c;">Rp 0</span>
                </div>
                <button type="submit" class="btn-magnetic" style="width:100%;padding:14px;background:linear-gradient(135deg,#c9a84c,#a8832d);border:none;border-radius:999px;color:#111;font-size:1rem;font-weight:700;cursor:pointer;letter-spacing:.04em;transition:all .25s;"
                        onmouseenter="this.style.opacity='.85';this.style.transform='translateY(-1px)';"
                        onmouseleave="this.style.opacity='1';this.style.transform='';">
                    Checkout →
                </button>
            </div>
        </form>
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
document.addEventListener('DOMContentLoaded', function() {
    // ==================== 1. INITIALIZE AOS ====================
    AOS.init({
        once: true,
        duration: 800,
        easing: 'ease-out-cubic'
    });

    // ==================== 2. GSAP ENTRANCE ANIMATION ====================
    gsap.from('.cart-header-section', {
        opacity: 0,
        y: -30,
        duration: 1,
        ease: 'power3.out'
    });

    gsap.from('.item-card-wrapper', {
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

    // ==================== 5. CART FUNCTIONALITY (KEEP ORIGINAL LOGIC) ====================
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
            const card = checkbox.closest('.item-card');
            const price = parseInt(card.getAttribute('data-price'));
            const qty = parseInt(card.getAttribute('data-qty'));
            total += price * qty;
        });
        document.getElementById('total-display').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    document.querySelectorAll('.item-checkbox').forEach(cb => cb.addEventListener('change', calculateTotal));
    calculateTotal();

    document.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            updateQty(this.getAttribute('data-cart-id'), parseInt(this.getAttribute('data-qty')) + 1, this);
        });
    });

    document.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const qty = parseInt(this.getAttribute('data-qty'));
            if (qty > 1) updateQty(this.getAttribute('data-cart-id'), qty - 1, this);
        });
    });

    document.querySelectorAll('.btn-delete-item').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Hapus item dari keranjang?')) deleteItem(this.getAttribute('data-cart-id'));
        });
    });

    function updateQty(cartId, qty, btnElement) {
        fetch('{{ route("customer.cart.update") }}', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
            },
            body: JSON.stringify({ cart_id: cartId, qty: qty })
        })
        .then(r => r.json()).then(data => {
            if (data.success ?? (data.status === 200)) {
                const card = btnElement.closest('.item-card');
                const qtyControls = btnElement.closest('[style*="align-items:center;gap:8px"]') || btnElement.parentElement;
                const qtyDisplay = card.querySelector('.qty-display');
                if (qtyDisplay) qtyDisplay.value = qty;
                qtyControls.querySelectorAll('button').forEach(b => b.setAttribute('data-qty', qty));
                card.setAttribute('data-qty', qty);
                const price = parseInt(card.getAttribute('data-price'));
                const subtotalEl = card.querySelector('[style*="font-weight:700;color:#c9a84c"]');
                if (subtotalEl) subtotalEl.textContent = 'Rp ' + (price * qty).toLocaleString('id-ID');
                calculateTotal();
            } else { alert(data.message || 'Gagal update qty'); }
        }).catch(err => alert('Terjadi kesalahan update qty: ' + err.message));
    }

    function deleteItem(cartId) {
        let url = '{{ url("/cart") }}/' + cartId;
        fetch(url, {
            method: 'DELETE', 
            headers: { 
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
            }
        }).then(r => r.json()).then(data => {
            if (data.success) {
                location.reload();
            } else { 
                alert(data.message || 'Gagal menghapus item'); 
            }
        }).catch((err) => alert('Terjadi kesalahan hapus item: ' + err.message));
    }
});
</script>
@endpush

