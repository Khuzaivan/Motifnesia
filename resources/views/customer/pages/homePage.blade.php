@extends('customer.layouts.mainLayout')

@section('container')
{{-- Load CSS for AOS --}}
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />

{{-- ==============================
     SECTION 1: CINEMATIC SPLIT HERO WITH 3D WebGL PARTICLES
     ============================== --}}
<div class="w-full max-w-[1400px] mx-auto pt-24 px-4 sm:px-6 mb-24">
    <div class="relative h-[600px] lg:h-[540px] rounded-[2.5rem] overflow-hidden shadow-2xl group bg-[#111315]" id="hero-wrapper" data-aos="fade-down" data-aos-duration="1200">
        
        {{-- Three.js Canvas replacing the static CSS particles --}}
        <canvas id="hero-3d-particles" class="absolute inset-0 z-10 w-full lg:w-[55%] pointer-events-none overflow-hidden" style="mix-blend-mode: screen; opacity: 0.7;"></canvas>

        <div id="homepage-carousel" class="relative w-full h-full">
            @php
                $heroImages = [
                    asset('images/hero_slide_1.png'),
                    asset('images/hero_slide_2.png'),
                    asset('images/kategori_pria.png'),
                ];
            @endphp
            @if(isset($slides) && $slides->count())
                @foreach($slides as $i => $slide)
                    <div class="absolute inset-0 carousel-slide flex flex-col lg:flex-row {{ $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}" data-index="{{ $i }}">
                        
                        {{-- Text Section (Left) --}}
                        <div class="w-full lg:w-[55%] h-full p-8 lg:p-20 flex flex-col justify-center relative z-20 slide-caption {{ $i === 0 ? 'active' : '' }}" data-caption-index="{{ $i }}">
                            <div class="max-w-xl">
                                <span class="inline-block text-[#c9a84c] text-xs font-bold tracking-[0.2.5em] uppercase mb-4 opacity-0 caption-tag" style="transform: translateY(20px);">✦ Koleksi Eksklusif Motifnesia</span>
                                
                                <h2 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-white mb-6 leading-[1.15] opacity-0 caption-title tracking-tight" style="font-family:'Playfair Display',serif; transform: translateY(25px);">
                                    {{ $slide->judul ?? 'Batik Nusantara Premium' }}
                                </h2>
                                
                                <p class="text-white/70 text-sm lg:text-base mb-8 opacity-0 caption-desc leading-relaxed max-w-md" style="transform: translateY(30px);">
                                    {{ $slide->caption ?? 'Kami menghadirkan koleksi batik nusantara kualitas terbaik. Desain modern, elegan, dan abadi.' }}
                                </p>
                                
                                <div class="flex gap-4 opacity-0 caption-btn" style="transform: translateY(35px);">
                                    <a href="#koleksi" class="magnetic-btn inline-flex items-center gap-3 px-8 py-3.5 bg-white hover:bg-[#f5f0e8] text-[#1c1a16] font-bold rounded-full transition-all duration-300 text-sm tracking-wide shadow-md">
                                        Lihat Koleksi
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Image Section (Right) --}}
                        <div class="absolute lg:relative w-full h-full lg:w-[45%] top-0 right-0 z-10 overflow-hidden">
                            {{-- Mobile overlay gradient --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-[#111315] via-[#111315]/80 to-transparent lg:hidden z-10"></div>
                            
                            {{-- Image wrapper with GSAP clip-path animation --}}
                            <div class="w-full h-full parallax-img transform scale-105" data-slide="{{ $i }}" style="clip-path: inset(0% 0% 0% 0%);">
                                <img src="{{ $heroImages[$i % count($heroImages)] }}" alt="Slide {{ $i+1 }}"
                                    class="w-full h-full object-cover lg:rounded-l-[2.5rem]">
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Navigation Arrows (Magnetic Controls) --}}
                <div class="absolute bottom-10 right-10 z-30 flex gap-3 hidden lg:flex">
                    <button id="carousel-prev" class="magnetic-control bg-white/10 hover:bg-white backdrop-blur-md border border-white/12 text-white hover:text-[#1c1a16] rounded-full w-12 h-12 flex items-center justify-center transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button id="carousel-next" class="magnetic-control bg-white/10 hover:bg-white backdrop-blur-md border border-white/12 text-white hover:text-[#1c1a16] rounded-full w-12 h-12 flex items-center justify-center transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                {{-- Indicators --}}
                <div class="absolute bottom-8 left-8 lg:left-20 z-30 flex gap-2">
                    @foreach($slides as $i => $slide)
                        <button class="carousel-dot transition-all duration-300 rounded-full {{ $i === 0 ? 'w-8 h-1.5 bg-[#c9a84c]' : 'w-2.5 h-1.5 bg-white/30 hover:bg-white/70' }}" data-dot-index="{{ $i }}"></button>
                    @endforeach
                </div>
            @else
                {{-- Fallback --}}
                <div class="w-full h-full flex flex-col lg:flex-row items-center">
                    <div class="w-full lg:w-1/2 p-20 z-20">
                        <h1 class="text-5xl font-bold mb-4 text-white" style="font-family:'Playfair Display',serif;">Motifnesia<span class="text-[#c9a84c]">.</span></h1>
                        <p class="text-white/70 text-xl">Koleksi Batik Nusantara Premium</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ==============================
     SECTION 2: TRUST BADGES / KEUNGGULAN
     ============================== --}}
<div class="w-full max-w-[1400px] mx-auto px-6 mb-24" id="trust-section">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach([
            ['icon' => '🚚', 'title' => 'Gratis Ongkir', 'desc' => 'Ke seluruh Indonesia'],
            ['icon' => '✅', 'title' => '100% Original', 'desc' => 'Dijamin keasliannya'],
            ['icon' => '🔄', 'title' => 'Retur 7 Hari', 'desc' => 'Tanpa pertanyaan'],
            ['icon' => '🔒', 'title' => 'Pembayaran Aman', 'desc' => 'Berbagai metode'],
        ] as $i => $badge)
        <div class="trust-badge bg-[#181818] border border-white/5 rounded-2.5xl p-5 flex items-center gap-4 shadow-[0_10px_30px_rgba(0,0,0,0.2)] hover:border-[#c9a84c]/28 hover:shadow-[0_10px_30px_rgba(201,168,76,0.08)] transition-all duration-300 transform hover:-translate-y-1 opacity-0"
             style="animation-delay: {{ $i * 100 }}ms" data-aos="zoom-in" data-aos-delay="{{ $i * 100 }}">
            <span class="text-3.5xl filter drop-shadow-md">{{ $badge['icon'] }}</span>
            <div>
                <p class="font-bold text-white/90 text-sm mb-0.5">{{ $badge['title'] }}</p>
                <p class="text-xs text-white/45">{{ $badge['desc'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ==============================
     SECTION 2.5: EXPLORE BY CATEGORY
     ============================== --}}
<div class="w-full max-w-[1400px] mx-auto px-6 mb-24">
    <div class="mb-12" data-aos="fade-up" data-aos-duration="1000">
        <h2 class="text-2xl md:text-3xl font-bold text-center text-white tracking-[0.05em]" style="font-family:'Playfair Display',serif;">
            Eksplorasi <span class="text-[#c9a84c]">Kategori</span>
        </h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
        {{-- Kategori 1 --}}
        <a href="{{ route('customer.home', ['gender' => 'Pria']) }}" class="group relative h-[320px] rounded-3xl overflow-hidden block shadow-lg" data-aos="fade-right" data-aos-delay="0" data-aos-duration="1000">
            <img src="{{ asset('images/kategori_pria.png') }}" alt="Batik Pria" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
            <div class="absolute inset-0 bg-gradient-to-t from-[#111111]/90 via-[#111111]/30 to-transparent"></div>
            <div class="absolute bottom-6 left-6 right-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white group-hover:bg-[#c9a84c] group-hover:border-[#c9a84c] transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <span class="text-white font-semibold text-lg tracking-wide group-hover:text-[#c9a84c] transition-colors">Pria</span>
            </div>
        </a>

        {{-- Kategori 2 --}}
        <a href="{{ route('customer.home', ['gender' => 'Wanita']) }}" class="group relative h-[320px] rounded-3xl overflow-hidden block shadow-lg" data-aos="fade-up" data-aos-delay="150" data-aos-duration="1000">
            <img src="{{ asset('images/kategori_wanita.png') }}" alt="Batik Wanita" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
            <div class="absolute inset-0 bg-gradient-to-t from-[#111111]/90 via-[#111111]/30 to-transparent"></div>
            <div class="absolute bottom-6 left-6 right-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white group-hover:bg-[#c9a84c] group-hover:border-[#c9a84c] transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <span class="text-white font-semibold text-lg tracking-wide group-hover:text-[#c9a84c] transition-colors">Wanita</span>
            </div>
        </a>

        {{-- Kategori 3 --}}
        <a href="{{ route('customer.home', ['gender' => 'Anak-anak']) }}" class="group relative h-[320px] rounded-3xl overflow-hidden block shadow-lg" data-aos="fade-left" data-aos-delay="300" data-aos-duration="1000">
            <img src="{{ asset('images/kategori_anak.png') }}" alt="Batik Anak-anak" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
            <div class="absolute inset-0 bg-gradient-to-t from-[#111111]/90 via-[#111111]/30 to-transparent"></div>
            <div class="absolute bottom-6 left-6 right-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white group-hover:bg-[#c9a84c] group-hover:border-[#c9a84c] transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-white font-semibold text-lg tracking-wide group-hover:text-[#c9a84c] transition-colors">Anak-anak</span>
            </div>
        </a>
    </div>
</div>

{{-- ==============================
     SECTION 3: PRODUCTS + SIDEBAR
     ============================== --}}
<div class="w-full max-w-[1400px] mx-auto px-6 mb-24" id="koleksi">
    <div class="flex flex-col md:flex-row gap-8 relative">

        {{-- Sidebar Filter --}}
        <div class="sticky top-28 h-fit max-h-[calc(100vh-8rem)] w-full md:w-72 shrink-0 overflow-y-auto bg-[#181818] border border-white/5 rounded-2xl shadow-[0_12px_40px_rgb(0,0,0,0.6)] custom-scrollbar" data-aos="fade-right" data-aos-duration="1000">
            @include('customer.components.sideBar')
        </div>

        {{-- Product Grid --}}
        <div class="flex-1">
            {{-- Center Aligned Luxe Section Header --}}
            <div class="mb-12" data-aos="fade-up" data-aos-duration="1000">
                <div class="flex items-center justify-center gap-6 mb-3">
                    <div class="h-[1px] bg-gradient-to-r from-transparent to-[#c9a84c] w-12 md:w-24"></div>
                    <h2 class="text-2xl md:text-3xl font-bold text-[#c9a84c] tracking-[0.2em] uppercase drop-shadow-[0_0_15px_rgba(201,168,76,0.35)]">TOP DEALS</h2>
                    <div class="h-[1px] bg-gradient-to-l from-transparent to-[#c9a84c] w-12 md:w-24"></div>
                </div>
                <p class="text-white/50 text-center text-sm tracking-wide">Eksplorasi ragam motif nusantara eksklusif terbaik</p>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($products as $idx => $product)
                    <div class="product-card-wrapper" style="--delay: {{ ($idx % 8) * 80 }}ms">
                        @include('customer.components.product-card', ['product' => $product])
                    </div>
                @endforeach
            </div>

            @if($products->isEmpty())
                <div class="text-center py-24" data-aos="fade-up">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold text-white mb-2">Produk Tidak Ditemukan</h3>
                    <p class="text-white/50">Coba ubah filter atau reset pencarian</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Custom Rounded borders */
    .rounded-2.5xl { border-radius: 1.5rem; }

    /* Product card entrance with slide-up + scale-up */
    .product-card-wrapper {
        opacity: 1;
        transform: translateY(0) scale(1);
        transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1) var(--delay, 0ms),
                    transform 0.8s cubic-bezier(0.16, 1, 0.3, 1) var(--delay, 0ms);
        display: flex;
        flex-direction: column;
    }
    .product-card-wrapper.card-hidden {
        opacity: 0;
        transform: translateY(35px) scale(0.96);
    }

    /* Glow details for badges */
    .trust-badge {
        transition: all 0.3s ease;
    }
    .trust-badge.visible {
        opacity: 1 !important;
    }

    /* Shimmer line progress under hero */
    #hero-wrapper::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        height: 4px;
        background: linear-gradient(90deg, #c9a84c, #ffd700, #c9a84c);
        background-size: 200% auto;
        animation: shimmerLine 3s linear infinite;
        z-index: 40;
        width: var(--progress, 0%);
        transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes shimmerLine {
        to { background-position: 200% center; }
    }
</style>

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
            duration: 900,
            easing: 'ease-out-cubic'
        });



        // ==================== 3. THREE.JS 3D WebGL PARTICLE WAVE ====================
        const canvas = document.getElementById('hero-3d-particles');
        const heroWrapper = document.getElementById('hero-wrapper');
        
        if (canvas && heroWrapper) {
            const scene = new THREE.Scene();
            
            // Camera setup
            const camera = new THREE.PerspectiveCamera(45, canvas.clientWidth / canvas.clientHeight, 0.1, 100);
            camera.position.set(0, 4.2, 5.5);
            camera.lookAt(0, 0, 0);

            // Renderer setup
            const renderer = new THREE.WebGLRenderer({
                canvas: canvas,
                alpha: true,
                antialias: true
            });
            renderer.setSize(canvas.clientWidth, canvas.clientHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

            // Generate soft particle texture
            function createCircleTexture() {
                const texCanvas = document.createElement('canvas');
                texCanvas.width = 16;
                texCanvas.height = 16;
                const ctx = texCanvas.getContext('2d');
                const grad = ctx.createRadialGradient(8, 8, 0, 8, 8, 8);
                grad.addColorStop(0, '#c9a84c');
                grad.addColorStop(0.3, 'rgba(201, 168, 76, 0.8)');
                grad.addColorStop(1, 'rgba(0,0,0,0)');
                ctx.fillStyle = grad;
                ctx.fillRect(0, 0, 16, 16);
                return new THREE.CanvasTexture(texCanvas);
            }

            // Create grid of points
            const particleCount = 1200;
            const particleGeo = new THREE.BufferGeometry();
            const positions = new Float32Array(particleCount * 3);
            
            const numX = 40;
            const numY = 30;
            const spacingX = 0.22;
            const spacingY = 0.22;
            const startX = -(numX * spacingX) / 2;
            const startY = -(numY * spacingY) / 2;

            let idx = 0;
            for (let ix = 0; ix < numX; ix++) {
                for (let iy = 0; iy < numY; iy++) {
                    positions[idx] = startX + ix * spacingX; // x
                    positions[idx + 1] = 0; // y
                    positions[idx + 2] = startY + iy * spacingY; // z
                    idx += 3;
                }
            }

            particleGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));

            const particleTexture = createCircleTexture();
            const particleMat = new THREE.PointsMaterial({
                size: 0.14,
                map: particleTexture,
                transparent: true,
                opacity: 0.72,
                blending: THREE.AdditiveBlending,
                depthWrite: false
            });

            const particleSystem = new THREE.Points(particleGeo, particleMat);
            scene.add(particleSystem);

            // Mouse interaction variables
            let mouseX = 999;
            let mouseY = 999;
            let mouseWorldX = 999;
            let mouseWorldZ = 999;

            heroWrapper.addEventListener('mousemove', (e) => {
                const rect = heroWrapper.getBoundingClientRect();
                mouseX = ((e.clientX - rect.left) / heroWrapper.clientWidth) * 2 - 1;
                mouseY = -((e.clientY - rect.top) / heroWrapper.clientHeight) * 2 + 1;

                // Simple coordinate projection to scene boundaries
                mouseWorldX = mouseX * 4.4;
                mouseWorldZ = -mouseY * 3.3;
            });

            heroWrapper.addEventListener('mouseleave', () => {
                mouseWorldX = 999;
                mouseWorldZ = 999;
            });

            // Wave render loop
            const clock = new THREE.Clock();
            
            function animate() {
                requestAnimationFrame(animate);
                
                const time = clock.getElapsedTime();
                const posArray = particleGeo.attributes.position.array;
                
                let i = 0;
                for (let ix = 0; ix < numX; ix++) {
                    for (let iy = 0; iy < numY; iy++) {
                        const px = posArray[i];
                        const pz = posArray[i + 2];
                        
                        // Dual sin-wave calculations
                        let targetY = (Math.sin((ix * 0.25) + (time * 1.5)) * 0.26) + 
                                      (Math.sin((iy * 0.2) + (time * 1.8)) * 0.22);
                        
                        // Local mouse ripple repulsion
                        if (mouseWorldX !== 999) {
                            const dx = px - mouseWorldX;
                            const dz = pz - mouseWorldZ;
                            const dist = Math.sqrt(dx * dx + dz * dz);
                            if (dist < 1.4) {
                                // Add upward wave puff based on mouse proximity
                                targetY += (1.4 - dist) * 0.58;
                            }
                        }

                        // Apply y coordinate
                        posArray[i + 1] = targetY;
                        i += 3;
                    }
                }
                
                particleGeo.attributes.position.needsUpdate = true;
                renderer.render(scene, camera);
            }
            
            animate();

            // Resize handler
            window.addEventListener('resize', () => {
                camera.aspect = canvas.clientWidth / canvas.clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(canvas.clientWidth, canvas.clientHeight);
            });
        }

        // ==================== 4. GSAP HERO CAROUSEL CHOREOGRAPHY ====================
        const slides = Array.from(document.querySelectorAll('#homepage-carousel .carousel-slide'));
        const captions = Array.from(document.querySelectorAll('.slide-caption'));
        const dots = Array.from(document.querySelectorAll('.carousel-dot'));
        const wrapper = document.getElementById('hero-wrapper');
        
        if (slides.length) {
            let activeIdx = 0;
            const totalSlides = slides.length;
            let autoInterval;

            const runTransition = (targetIdx) => {
                const incomingSlide = slides[targetIdx];
                const activeSlide = slides[activeIdx];
                
                if (targetIdx === activeIdx) return;

                // Stop previous timeline animations on elements
                gsap.killTweensOf([
                    activeSlide, incomingSlide,
                    '.parallax-img',
                    '.caption-tag', '.caption-title', '.caption-desc', '.caption-btn'
                ]);

                // 1. Image transition (clip-path reveal like a sliding curtain)
                const isForward = targetIdx > activeIdx || (activeIdx === totalSlides - 1 && targetIdx === 0);
                const startClip = isForward ? 'inset(0% 0% 0% 100%)' : 'inset(0% 100% 0% 0%)';
                
                // Set initial scale and clip on incoming image
                gsap.set(incomingSlide.querySelector('.parallax-img'), {
                    clipPath: startClip,
                    scale: 1.16
                });

                // Position incoming slide above/z-index
                slides.forEach((s, idx) => {
                    s.style.zIndex = idx === targetIdx ? '20' : (idx === activeIdx ? '10' : '0');
                });
                
                incomingSlide.style.opacity = '1';

                // Clip-path reveal curtain transition
                gsap.to(incomingSlide.querySelector('.parallax-img'), {
                    clipPath: 'inset(0% 0% 0% 0%)',
                    scale: 1.05,
                    duration: 1.25,
                    ease: 'power3.inOut'
                });

                // 2. Incoming text staggered animation
                const incomingCaption = captions[targetIdx];
                const activeCaption = captions[activeIdx];

                // Fade out current slide text
                if (activeCaption) {
                    gsap.to(activeCaption.querySelectorAll('.caption-tag, .caption-title, .caption-desc, .caption-btn'), {
                        opacity: 0,
                        y: -25,
                        duration: 0.4,
                        ease: 'power2.in',
                        stagger: 0.05,
                        onComplete: () => {
                            activeSlide.style.opacity = '0';
                        }
                    });
                }

                // Fade in incoming text
                if (incomingCaption) {
                    gsap.fromTo(incomingCaption.querySelectorAll('.caption-tag, .caption-title, .caption-desc, .caption-btn'), 
                        { opacity: 0, y: 25 },
                        { 
                            opacity: 1, 
                            y: 0, 
                            duration: 0.85, 
                            ease: 'power3.out', 
                            stagger: 0.12, 
                            delay: 0.35 
                        }
                    );
                }

                // Indicators update
                dots.forEach((d, di) => {
                    if (di === targetIdx) {
                        d.classList.add('w-8', 'bg-[#c9a84c]');
                        d.classList.remove('w-2.5', 'bg-white/30');
                    } else {
                        d.classList.remove('w-8', 'bg-[#c9a84c]');
                        d.classList.add('w-2.5', 'bg-white/30');
                    }
                });

                // Progress Bar under hero
                if (wrapper) {
                    wrapper.style.setProperty('--progress', `${((targetIdx + 1) / totalSlides) * 100}%`);
                }

                activeIdx = targetIdx;
            };

            const carouselNext = () => runTransition((activeIdx + 1) % totalSlides);
            const carouselPrev = () => runTransition((activeIdx - 1 + totalSlides) % totalSlides);

            // Controls listeners
            document.getElementById('carousel-next')?.addEventListener('click', () => { carouselNext(); resetAuto(); });
            document.getElementById('carousel-prev')?.addEventListener('click', () => { carouselPrev(); resetAuto(); });
            
            dots.forEach((d, di) => {
                d.addEventListener('click', () => { runTransition(di); resetAuto(); });
            });

            // Start auto play
            const startAuto = () => { autoInterval = setInterval(carouselNext, 6000); };
            const resetAuto = () => { clearInterval(autoInterval); startAuto(); };
            
            // Set initial state
            if (captions[0]) {
                gsap.to(captions[0].querySelectorAll('.caption-tag, .caption-title, .caption-desc, .caption-btn'), {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    stagger: 0.1,
                    ease: 'power3.out'
                });
            }
            if (wrapper) wrapper.style.setProperty('--progress', `${(1 / totalSlides) * 100}%`);
            startAuto();
        }

        // ==================== 5. MAGNETIC BUTTONS EFFECT ====================
        const magneticBtns = document.querySelectorAll('.magnetic-btn, .magnetic-control');
        magneticBtns.forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const btnX = rect.left + rect.width / 2;
                const btnY = rect.top + rect.height / 2;
                
                // Offset calculation from center
                const deltaX = e.clientX - btnX;
                const deltaY = e.clientY - btnY;

                // Move button towards cursor (magnetic draw)
                gsap.to(btn, {
                    x: deltaX * 0.35,
                    y: deltaY * 0.35,
                    duration: 0.3,
                    ease: 'power2.out'
                });
            });

            btn.addEventListener('mouseleave', () => {
                // Elastic snapback
                gsap.to(btn, {
                    x: 0,
                    y: 0,
                    duration: 0.6,
                    ease: 'elastic.out(1, 0.3)'
                });
            });
        });

        // ==================== 6. PRODUCT CARDS INTERSECTION OBSERVER ====================
        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.remove('card-hidden');
                    cardObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.05 });

        document.querySelectorAll('.product-card-wrapper').forEach(el => {
            const rect = el.getBoundingClientRect();
            if (rect.top > window.innerHeight) {
                el.classList.add('card-hidden');
                cardObserver.observe(el);
            }
        });
    });
</script>
@endpush