<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Motifnesia - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/motifnesia_logo.png') }}">
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    {{-- Alpine & ApexCharts --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #0f172a; color: #f1f5f9; margin:0; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Custom Scrollbar for dark theme */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        /* Animations */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-slide-up {
            animation: fadeSlideUp 0.6s ease-out forwards;
        }
        
        /* Glass card utility */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* LIGHT MODE GLOBAL OVERRIDES */
        .light-mode {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
        }
        /* Body & background layers */
        .light-mode body { background-color: #f1f5f9 !important; color: #1e293b !important; }
        .light-mode .bg-slate-900,
        .light-mode .bg-slate-900\/30,
        .light-mode .bg-slate-900\/50 { background-color: #f1f5f9 !important; }
        .light-mode .bg-slate-800,
        .light-mode .bg-slate-800\/50,
        .light-mode .bg-slate-800\/30,
        .light-mode .bg-slate-800\/80 { background-color: #ffffff !important; }
        .light-mode .bg-slate-700,
        .light-mode .bg-slate-700\/50 { background-color: #f8fafc !important; }
        
        /* Text overrides */
        .light-mode .text-white { color: #0f172a !important; }
        .light-mode .text-slate-100 { color: #1e293b !important; }
        .light-mode .text-slate-200 { color: #334155 !important; }
        .light-mode .text-slate-300 { color: #475569 !important; }
        .light-mode .text-slate-400 { color: #64748b !important; }
        .light-mode .text-slate-500 { color: #94a3b8 !important; }
        .light-mode .text-slate-600 { color: #cbd5e1 !important; }
        
        /* Borders */
        .light-mode .border-white\/10 { border-color: rgba(0, 0, 0, 0.1) !important; }
        .light-mode .border-white\/5 { border-color: rgba(0, 0, 0, 0.05) !important; }
        .light-mode .border-white\/20 { border-color: rgba(0, 0, 0, 0.15) !important; }
        .light-mode .divide-white\/5 > * + * { border-color: rgba(0, 0, 0, 0.07) !important; }
        
        /* Backgrounds */
        .light-mode .bg-white\/5 { background-color: rgba(0, 0, 0, 0.04) !important; }
        .light-mode .bg-white\/10 { background-color: rgba(0, 0, 0, 0.08) !important; }
        .light-mode .bg-white\/20 { background-color: rgba(0, 0, 0, 0.1) !important; }
        .light-mode .hover\:bg-white\/5:hover { background-color: rgba(0, 0, 0, 0.06) !important; }
        .light-mode .hover\:bg-white\/10:hover { background-color: rgba(0, 0, 0, 0.10) !important; }
        .light-mode .hover\:bg-slate-700\/50:hover { background-color: rgba(0, 0, 0, 0.04) !important; }
        
        /* Glass card: translucent white panel with subtle shadow */
        .light-mode .glass-card {
            background: rgba(255, 255, 255, 0.95) !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.08) !important;
        }
        
        /* Sidebar override */
        .light-mode aside { background-color: #ffffff !important; border-right: 1px solid #e2e8f0 !important; }
        .light-mode aside .bg-\[\#0a0f1e\] { background-color: #ffffff !important; }
        
        /* Inputs */
        .light-mode input, .light-mode select, .light-mode textarea {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
        }
        .light-mode input::placeholder,
        .light-mode textarea::placeholder { color: #94a3b8 !important; opacity: 1; }
        .light-mode select { color-scheme: light; }
        .light-mode select option {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }
        .light-mode select option[value=""] { color: #64748b !important; }
        .light-mode input[type="file"]::file-selector-button {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
        }
        .light-mode input[type="checkbox"],
        .light-mode input[type="radio"] {
            accent-color: #d97706;
        }
        
        /* Table rows */
        .light-mode thead, .light-mode .bg-slate-800\/50 { background-color: #f8fafc !important; }
        .light-mode .hover\:bg-slate-700\/50:hover { background-color: #f1f5f9 !important; }
        
        /* Chat bubbles in light mode */
        .light-mode #chatMessages { background-color: #f1f5f9 !important; }
        .light-mode .bg-slate-700.border.rounded-2xl { 
            background-color: #e2e8f0 !important; 
            border-color: #e2e8f0 !important; 
        }
        
        /* Scrollbars */
        .light-mode ::-webkit-scrollbar-track { background: #f1f5f9; }
        .light-mode ::-webkit-scrollbar-thumb { background: #cbd5e1; }

        /* Premium Overrides */
        input:focus, select:focus, textarea:focus {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.18) !important;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }
        .glass-card {
            transition: border-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease !important;
            transform-style: preserve-3d;
        }
        .glass-card:hover {
            border-color: rgba(245, 158, 11, 0.28) !important;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.35), 0 0 24px rgba(245, 158, 11, 0.06) !important;
        }
        .light-mode .glass-card:hover {
            border-color: rgba(217, 119, 6, 0.28) !important;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.06), 0 0 24px rgba(217, 119, 6, 0.06) !important;
        }
        .btn-magnetic, button, select, input, textarea, a {
            transition: transform 0.2s cubic-bezier(0.25, 1, 0.5, 1);
        }
    </style>
    <script>
        // Check local storage for theme
        if (localStorage.getItem('adminTheme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex overflow-hidden relative" x-data="{ sidebarOpen: true }">
    {{-- Three.js Background Canvas --}}
    <canvas id="three-admin-canvas" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:0;pointer-events:none;opacity:0.25;"></canvas>

    {{-- Sidebar --}}
    @include('admin.components.sidebar', ['activePage' => $activePage ?? 'default'])

    <div class="flex-1 flex flex-col h-screen overflow-hidden transition-all duration-300 relative z-10">
        {{-- Header --}}
        @include('admin.components.navbar') 

        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto p-4 md:p-8 relative">
            <div class="max-w-7xl mx-auto space-y-6 animate-fade-slide-up">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Animation Libraries CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ==================== 1. THREE.JS BG PARTICLES ====================
            const canvas = document.getElementById('three-admin-canvas');
            if (canvas) {
                const scene = new THREE.Scene();
                const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 100);
                camera.position.z = 5;

                const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
                renderer.setSize(window.innerWidth, window.innerHeight);
                renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

                const particleCount = 100;
                const geometry = new THREE.BufferGeometry();
                const positions = new Float32Array(particleCount * 3);
                const velocities = new Float32Array(particleCount * 3);

                for (let i = 0; i < particleCount * 3; i += 3) {
                    positions[i] = (Math.random() - 0.5) * 8;
                    positions[i+1] = (Math.random() - 0.5) * 8;
                    positions[i+2] = (Math.random() - 0.5) * 5;

                    velocities[i] = (Math.random() - 0.5) * 0.002;
                    velocities[i+1] = (Math.random() - 0.5) * 0.002 + 0.001; // drift upwards
                    velocities[i+2] = (Math.random() - 0.5) * 0.002;
                }

                geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));

                const material = new THREE.PointsMaterial({
                    size: 0.04,
                    color: 0xf59e0b, // Amber gold color
                    transparent: true,
                    opacity: 0.3,
                    blending: THREE.AdditiveBlending
                });

                const points = new THREE.Points(geometry, material);
                scene.add(points);

                function resizeCanvas() {
                    camera.aspect = window.innerWidth / window.innerHeight;
                    camera.updateProjectionMatrix();
                    renderer.setSize(window.innerWidth, window.innerHeight);
                }
                window.addEventListener('resize', resizeCanvas);

                // Mouse interact
                let mouseX = 0;
                let mouseY = 0;
                window.addEventListener('mousemove', (e) => {
                    mouseX = (e.clientX / window.innerWidth - 0.5) * 0.4;
                    mouseY = (e.clientY / window.innerHeight - 0.5) * 0.4;
                });

                function animate() {
                    requestAnimationFrame(animate);
                    
                    const pos = geometry.attributes.position.array;
                    for (let i = 1; i < particleCount * 3; i += 3) {
                        pos[i-1] += velocities[i-1];
                        pos[i] += velocities[i];
                        pos[i+1] += velocities[i+1];

                        // Recycle particles
                        if (pos[i] > 4) pos[i] = -4;
                        if (pos[i-1] > 4) pos[i-1] = -4;
                        if (pos[i-1] < -4) pos[i-1] = 4;
                    }
                    geometry.attributes.position.needsUpdate = true;

                    points.rotation.y += 0.0003;
                    points.rotation.x += 0.0001;

                    // Parallax camera slide
                    camera.position.x += (mouseX - camera.position.x) * 0.05;
                    camera.position.y += (-mouseY - camera.position.y) * 0.05;

                    renderer.render(scene, camera);
                }
                animate();
            }

            // ==================== 2. GSAP INTERACTIONS ====================
            // Entrance animation for content and cards
            gsap.from('.animate-fade-slide-up', {
                opacity: 0,
                y: 35,
                duration: 0.8,
                ease: 'power3.out'
            });

            gsap.from('.glass-card', {
                opacity: 0,
                scale: 0.98,
                y: 25,
                duration: 0.7,
                stagger: 0.08,
                ease: 'power2.out',
                clearProps: 'all'
            });

            // Entrance animation for table rows
            gsap.from('table tbody tr', {
                opacity: 0,
                x: -20,
                duration: 0.5,
                stagger: 0.03,
                ease: 'power1.out',
                delay: 0.3,
                clearProps: 'all'
            });

            // 3D Card Hover Tilt
            const handleCardTilt = (e) => {
                document.querySelectorAll('.glass-card').forEach(card => {
                    const rect = card.getBoundingClientRect();
                    const isHovered = (e.clientX >= rect.left && e.clientX <= rect.right && e.clientY >= rect.top && e.clientY <= rect.bottom);
                    
                    if (isHovered) {
                        const x = e.clientX - rect.left - rect.width / 2;
                        const y = e.clientY - rect.top - rect.height / 2;
                        gsap.to(card, {
                            rotationY: x * 0.015,
                            rotationX: -y * 0.015,
                            transformPerspective: 1000,
                            ease: 'power1.out',
                            duration: 0.3
                        });
                    } else {
                        gsap.to(card, {
                            rotationY: 0,
                            rotationX: 0,
                            ease: 'power1.out',
                            duration: 0.3
                        });
                    }
                });
            };
            window.addEventListener('mousemove', handleCardTilt);

            // Magnetic Pull Buttons
            const setupMagneticButtons = () => {
                document.querySelectorAll('.btn-magnetic, .admin-icon-btn, button[type="submit"], button[type="button"], input[type="submit"]').forEach(btn => {
                    // Avoid magnetic on sidebar toggle to avoid jumpiness
                    if (btn.getAttribute('@click') === 'sidebarOpen = !sidebarOpen') return;
                    
                    btn.addEventListener('mousemove', (e) => {
                        const rect = btn.getBoundingClientRect();
                        const x = e.clientX - rect.left - rect.width / 2;
                        const y = e.clientY - rect.top - rect.height / 2;
                        gsap.to(btn, {
                            x: x * 0.22,
                            y: y * 0.22,
                            duration: 0.3,
                            ease: 'power2.out'
                        });
                    });
                    btn.addEventListener('mouseleave', () => {
                        gsap.to(btn, {
                            x: 0,
                            y: 0,
                            duration: 0.4,
                            ease: 'elastic.out(1.1, 0.4)'
                        });
                    });
                });
            };
            setupMagneticButtons();

            // Run setup again if dynamic content might add new buttons
            const observer = new MutationObserver(setupMagneticButtons);
            observer.observe(document.body, { childList: true, subtree: true });
        });
    </script>
    @stack('scripts')
</body>
</html>
