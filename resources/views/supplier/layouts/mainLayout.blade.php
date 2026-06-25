<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Motifnesia - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/motifnesia_logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background: #080b12;
            color: #f8fafc;
            font-family: 'Nunito', sans-serif;
            overflow-x: hidden;
        }
        h1, h2, h3 { font-family: 'Plus Jakarta Sans', sans-serif; }
        .supplier-shell {
            min-height: 100vh;
            position: relative;
            isolation: isolate;
            background:
                radial-gradient(circle at 80% 12%, rgba(245, 158, 11, .14), transparent 32%),
                linear-gradient(135deg, #080b12 0%, #111827 52%, #17110a 100%);
        }
        .supplier-shell::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -2;
            opacity: .2;
            background-image:
                linear-gradient(45deg, rgba(245,158,11,.08) 1px, transparent 1px),
                linear-gradient(135deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 38px 38px, 30px 30px;
        }
        #supplier-orbit {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            opacity: .28;
            pointer-events: none;
        }
        .supplier-card {
            background: rgba(255,255,255,.055);
            border: 1px solid rgba(255,255,255,.1);
            backdrop-filter: blur(14px);
            box-shadow: 0 24px 70px rgba(0,0,0,.32);
            transform-style: preserve-3d;
        }
    </style>
</head>
<body>
    <div class="supplier-shell">
        <canvas id="supplier-orbit"></canvas>
        <header class="sticky top-0 z-30 border-b border-white/10 bg-slate-950/80 backdrop-blur-xl">
            <div class="max-w-7xl mx-auto px-4 md:px-8 h-20 flex items-center justify-between">
                <a href="{{ route('supplier.procurements.index') }}" class="flex items-center gap-3">
                    <span class="w-11 h-11 rounded-2xl bg-amber-500/10 border border-amber-400/30 flex items-center justify-center text-amber-300">
                        <i class="ri-archive-stack-line text-xl"></i>
                    </span>
                    <div>
                        <p class="text-lg font-extrabold text-white leading-tight">Motifnesia Supplier</p>
                        <p class="text-xs text-slate-500">Portal Pengadaan Stok</p>
                    </div>
                </a>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:block text-right">
                        <p class="text-sm font-bold text-white">{{ auth()->user()->full_name ?: auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500">Supplier</p>
                    </div>
                    <a href="{{ route('auth.logout') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-500/10 hover:bg-red-500 text-red-300 hover:text-white border border-red-500/20 font-bold transition-all">
                        <i class="ri-logout-box-line"></i> Logout
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 md:px-8 py-8 space-y-6">
            @yield('content')
        </main>
    </div>

    {{-- Animation Libraries CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('supplier-orbit');
        if (!canvas || !window.THREE) return;

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(55, window.innerWidth / window.innerHeight, 0.1, 100);
        camera.position.z = 6;
        const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        const group = new THREE.Group();
        scene.add(group);

        for (let i = 0; i < 3; i++) {
            const geometry = new THREE.TorusGeometry(1.35 + i * .42, .006, 8, 96);
            const material = new THREE.MeshBasicMaterial({ color: 0xf59e0b, transparent: true, opacity: .28 - i * .04 });
            const ring = new THREE.Mesh(geometry, material);
            ring.rotation.x = Math.PI / (2.4 + i * .2);
            ring.rotation.y = i * .55;
            group.add(ring);
        }

        function resize() {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        }
        window.addEventListener('resize', resize);

        function animateOrbit() {
            requestAnimationFrame(animateOrbit);
            group.rotation.y += .003;
            group.rotation.x += .001;
            renderer.render(scene, camera);
        }
        animateOrbit();
    });
    </script>
    @stack('scripts')
</body>
</html>
