@extends('customer.layouts.mainLayout')

@section('container')
{{-- Custom styles for glassmorphism and message transitions --}}
<style>
    .glass-chat-container {
        background: rgba(30, 30, 30, 0.45) !important;
        backdrop-filter: blur(16px) saturate(120%);
        -webkit-backdrop-filter: blur(16px) saturate(120%);
        border: 1px solid rgba(255, 255, 255, 0.06) !important;
        border-radius: 24px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        transition: border-color 0.3s ease;
    }
    html.customer-light .glass-chat-container {
        background: rgba(255, 250, 242, 0.6) !important;
        border: 1px solid rgba(78, 61, 37, 0.12) !important;
        box-shadow: 0 20px 50px rgba(78, 61, 37, 0.08);
    }
    
    .chat-bubble-admin {
        background: rgba(255, 255, 255, 0.04) !important;
        border: 1px solid rgba(255, 255, 255, 0.05) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    html.customer-light .chat-bubble-admin {
        background: rgba(78, 61, 37, 0.05) !important;
        border: 1px solid rgba(78, 61, 37, 0.08) !important;
        box-shadow: 0 4px 12px rgba(78,61,37,0.04);
    }

    /* Input focus visual border glow */
    .chat-input-focus:focus {
        border-color: var(--clr-gold) !important;
        box-shadow: 0 0 15px rgba(201, 168, 76, 0.25) !important;
        background: rgba(255, 255, 255, 0.08) !important;
    }
    html.customer-light .chat-input-focus:focus {
        background: #ffffff !important;
        border-color: var(--clr-gold-dark) !important;
        box-shadow: 0 0 15px rgba(201, 168, 76, 0.15) !important;
    }
</style>

<div style="min-height:100vh;padding-top:88px;padding-bottom:0;background:#131313;display:flex;flex-direction:column;position:relative;overflow:hidden;">
    
    {{-- Three.js WebGL Particle Background --}}
    <canvas id="chat-3d-bg" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:0;pointer-events:none;outline:none;mix-blend-mode:screen;opacity:0.65;"></canvas>

    <div style="max-width:860px;width:100%;margin:0 auto;padding:0 24px;flex:1;display:flex;flex-direction:column;position:relative;z-index:10;">

        {{-- Chat Container --}}
        <div id="chat-container" class="glass-chat-container" style="background:#1e1e1e;border:1px solid rgba(255,255,255,.06);border-radius:24px;overflow:hidden;flex:1;display:flex;flex-direction:column;margin-bottom:24px;min-height:0;height:calc(100vh - 140px); opacity:0; transform: translateY(30px);">

            {{-- Chat Header --}}
            <div style="background:linear-gradient(135deg,rgba(201,168,76,.12),rgba(168,131,45,.08));border-bottom:1px solid rgba(201,168,76,.15);padding:18px 24px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div class="magnetic-avatar" style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#c9a84c,#a8832d);display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(201,168,76,0.3);">
                        <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <div>
                        <h2 style="font-family:'Playfair Display',serif;color:#fff;font-weight:700;font-size:1rem;margin-bottom:2px;">Customer Support</h2>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="width:7px;height:7px;background:#6ee7b7;border-radius:50%;display:inline-block;box-shadow:0 0 6px rgba(110,231,183,.6);"></span>
                            <p style="color:rgba(255,255,255,.5);font-size:.75rem;">Admin akan segera membalas</p>
                        </div>
                    </div>
                </div>
                <a href="{{ url()->previous() }}" class="magnetic-close"
                   style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.5);text-decoration:none;transition:all .2s;"
                   onmouseenter="this.style.background='rgba(255,255,255,.1)';"
                   onmouseleave="this.style.background='rgba(255,255,255,.05)';">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            </div>

            {{-- Messages Area --}}
            <div id="chatMessages" style="flex:1;padding:20px 24px;overflow-y:auto;display:flex;flex-direction:column;gap:16px;background:rgba(24, 24, 24, 0.45);">
                @if(session('success'))
                    <div style="align-self:center;background:rgba(52,211,153,.1);border:1px solid rgba(52,211,153,.22);color:#86efac;border-radius:999px;padding:8px 14px;font-size:.78rem;font-weight:800;">
                        {{ session('success') }}
                    </div>
                @endif
                @if($messages->isEmpty())
                    <div id="empty-state" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;color:rgba(255,255,255,.3);">
                        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-bottom:12px;opacity:.4;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <p style="font-size:.9rem;font-weight:500;">Mulai percakapan</p>
                        <p style="font-size:.8rem;margin-top:4px;color:rgba(255,255,255,.2);">Kirim pesan pertama Anda</p>
                    </div>
                @else
                    @foreach($messages as $message)
                    @php $isAdmin = $message->sender_id !== Auth::id(); @endphp
                    <div class="chat-message-bubble" style="display:flex;justify-content:{{ $isAdmin ? 'flex-start' : 'flex-end' }};" data-message-id="{{ $message->id }}">
                        <div style="max-width:70%;">
                            @if($isAdmin)
                            <div style="display:flex;align-items:flex-end;gap:8px;">
                                <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#c9a84c,#a8832d);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 6px rgba(201,168,76,0.2);">
                                    <svg width="15" height="15" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div>
                                    <div class="chat-bubble-admin" style="background:#2a2a2a;border:1px solid rgba(255,255,255,.06);border-radius:16px;border-bottom-left-radius:4px;padding:10px 14px;">
                                        <p style="color:rgba(255,255,255,.9);font-size:.875rem;line-height:1.5;white-space:normal;">{!! nl2br(e($message->message)) !!}</p>
                                    </div>
                                    <p style="color:rgba(255,255,255,.25);font-size:.72rem;margin-top:4px;margin-left:4px;">{{ $message->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                            @else
                            <div style="display:flex;flex-direction:column;align-items:flex-end;">
                                <div style="background:linear-gradient(135deg,#c9a84c,#a8832d);border-radius:16px;border-bottom-right-radius:4px;padding:10px 14px;box-shadow:0 3px 8px rgba(201,168,76,0.25);">
                                    <p style="color:#111;font-size:.875rem;line-height:1.5;font-weight:500;white-space:normal;">{!! nl2br(e($message->message)) !!}</p>
                                </div>
                                <p style="color:rgba(255,255,255,.25);font-size:.72rem;margin-top:4px;margin-right:4px;">{{ $message->created_at->format('H:i') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            {{-- Input Area --}}
            <div style="padding:16px 20px;background:rgba(30, 30, 30, 0.6);border-top:1px solid rgba(255,255,255,.06);flex-shrink:0;">
                <form id="chatForm" style="display:flex;gap:10px;">
                    @csrf
                    <input type="hidden" id="chatId" value="{{ $chat->id ?? '' }}">
                    <input type="text" id="messageInput" placeholder="Ketik pesan Anda..." class="chat-input-focus"
                           style="flex:1;padding:12px 16px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;transition:all .3s;"
                           required>
                    <button type="submit" class="magnetic-send btn-glow-gold"
                            style="padding:12px 24px;background:linear-gradient(135deg,#c9a84c,#a8832d);border:none;border-radius:12px;color:#111;font-weight:800;font-size:.875rem;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all .3s;flex-shrink:0;">
                        Kirim
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Load Visual Libraries --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<script>
let chatId = document.getElementById('chatId').value;
let lastMessageId = {{ $messages->last()->id ?? 0 }};
let pollingInterval;
const originalPageTitle = document.title;

// ==================== 1. GELEMBUNG CHAT POP-UP REVEAL (GSAP) ====================
function animateNewBubble(el) {
    if (!el) return;
    gsap.fromTo(el,
        { opacity: 0, y: 30, scale: 0.82 },
        { opacity: 1, y: 0, scale: 1.0, duration: 0.45, ease: 'back.out(1.5)', clearProps: 'transform' }
    );
}

// ==================== 2. GSAP ENTRANCE FOR CHAT CONTAINER & HISTORY ====================
document.addEventListener('DOMContentLoaded', () => {
    // Entrance Chat Box
    gsap.to('#chat-container', {
        opacity: 1,
        y: 0,
        scale: 1,
        duration: 1.1,
        ease: 'power4.out',
        delay: 0.1
    });

    // Staggered reveal for existing chat history
    const existingBubbles = document.querySelectorAll('.chat-message-bubble');
    if (existingBubbles.length > 0) {
        gsap.fromTo(existingBubbles,
            { opacity: 0, y: 20, scale: 0.96 },
            { 
                opacity: 1, 
                y: 0, 
                scale: 1.0, 
                duration: 0.55, 
                stagger: 0.04, 
                ease: 'power3.out', 
                delay: 0.45,
                clearProps: 'transform' 
            }
        );
    }

    // ==================== 3. THREE.JS 3D FLOATING PARTICLES BACKDROP ====================
    const canvas = document.getElementById('chat-3d-bg');
    if (canvas) {
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 0.1, 100);
        camera.position.z = 8;

        const renderer = new THREE.WebGLRenderer({
            canvas: canvas,
            alpha: true,
            antialias: true
        });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        // Generate particle point texture (soft glow)
        function createSoftPoint() {
            const pCanvas = document.createElement('canvas');
            pCanvas.width = 16;
            pCanvas.height = 16;
            const ctx = pCanvas.getContext('2d');
            const grad = ctx.createRadialGradient(8, 8, 0, 8, 8, 8);
            grad.addColorStop(0, 'rgba(201, 168, 76, 1)');
            grad.addColorStop(0.3, 'rgba(201, 168, 76, 0.6)');
            grad.addColorStop(1, 'rgba(0,0,0,0)');
            ctx.fillStyle = grad;
            ctx.fillRect(0, 0, 16, 16);
            return new THREE.CanvasTexture(pCanvas);
        }

        // Generate crystal points
        const particleCount = 180;
        const particleGeo = new THREE.BufferGeometry();
        const posArray = new Float32Array(particleCount * 3);

        for (let i = 0; i < particleCount * 3; i += 3) {
            posArray[i] = (Math.random() - 0.5) * 14;      // x
            posArray[i + 1] = (Math.random() - 0.5) * 10;  // y
            posArray[i + 2] = (Math.random() - 0.5) * 6;   // z
        }

        particleGeo.setAttribute('position', new THREE.BufferAttribute(posArray, 3));

        const particleMat = new THREE.PointsMaterial({
            size: 0.09,
            map: createSoftPoint(),
            transparent: true,
            opacity: 0.6,
            blending: THREE.AdditiveBlending,
            depthWrite: false
        });

        const particles = new THREE.Points(particleGeo, particleMat);
        scene.add(particles);

        // Parallax mouse movements
        let mouseX = 0;
        let mouseY = 0;
        let targetCamX = 0;
        let targetCamY = 0;

        window.addEventListener('mousemove', (e) => {
            mouseX = (e.clientX / window.innerWidth) * 2 - 1;
            mouseY = -(e.clientY / window.innerHeight) * 2 + 1;

            targetCamX = mouseX * 0.9;
            targetCamY = mouseY * 0.6;
        });

        // Animation clock
        const clock = new THREE.Clock();

        function renderParticles() {
            requestAnimationFrame(renderParticles);

            const elapsedTime = clock.getElapsedTime();

            // Smooth parallax camera lerp
            camera.position.x += (targetCamX - camera.position.x) * 0.05;
            camera.position.y += (targetCamY - camera.position.y) * 0.05;
            camera.lookAt(0, 0, 0);

            // Animate points drifting upwards and swaying
            const pos = particleGeo.attributes.position.array;
            for (let i = 0; i < particleCount * 3; i += 3) {
                // drift up
                pos[i + 1] += 0.005;
                // sway x
                pos[i] += Math.sin(elapsedTime * 0.5 + i) * 0.002;
                
                // reset to bottom
                if (pos[i + 1] > 5) {
                    pos[i + 1] = -5;
                    pos[i] = (Math.random() - 0.5) * 14;
                }
            }
            particleGeo.attributes.position.needsUpdate = true;

            renderer.render(scene, camera);
        }

        renderParticles();

        // Resize handler
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    }

    // ==================== 4. MAGNETIC BUTTONS (GSAP) ====================
    const magneticItems = document.querySelectorAll('.magnetic-avatar, .magnetic-close, .magnetic-send');
    magneticItems.forEach(item => {
        item.addEventListener('mousemove', (e) => {
            const rect = item.getBoundingClientRect();
            const x = e.clientX - (rect.left + rect.width / 2);
            const y = e.clientY - (rect.top + rect.height / 2);

            gsap.to(item, {
                x: x * 0.32,
                y: y * 0.32,
                duration: 0.3,
                ease: 'power2.out'
            });
        });

        item.addEventListener('mouseleave', () => {
            gsap.to(item, {
                x: 0,
                y: 0,
                duration: 0.6,
                ease: 'elastic.out(1, 0.3)'
            });
        });
    });
});

function showChatInterrupt(text) {
    let alertBox = document.getElementById('chatInterruptAlert');
    if (!alertBox) {
        alertBox = document.createElement('div');
        alertBox.id = 'chatInterruptAlert';
        alertBox.style.cssText = 'position:fixed;right:22px;bottom:96px;z-index:1200;background:#1e1e1e;border:1px solid rgba(201,168,76,.38);color:#f5efe2;padding:12px 16px;border-radius:14px;box-shadow:0 18px 48px rgba(0,0,0,.42);font-size:.84rem;font-weight:700;backdrop-filter:blur(8px);';
        document.body.appendChild(alertBox);
    }

    alertBox.textContent = text;
    alertBox.style.display = 'block';
    
    // GSAP alert popup animation
    gsap.fromTo(alertBox,
        { opacity: 0, scale: 0.8, y: 15 },
        { opacity: 1, scale: 1.0, y: 0, duration: 0.3, ease: 'back.out(1.5)' }
    );
    
    document.title = 'Pesan baru - ' + originalPageTitle;
    setTimeout(() => {
        gsap.to(alertBox, {
            opacity: 0,
            scale: 0.8,
            y: 15,
            duration: 0.35,
            onComplete: () => { alertBox.style.display = 'none'; }
        });
    }, 3200);
}

document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        document.title = originalPageTitle;
    }
});

function scrollToBottom() {
    const c = document.getElementById('chatMessages');
    
    // Smooth scroll down using GSAP
    gsap.to(c, {
        scrollTo: c.scrollHeight,
        scrollTop: c.scrollHeight,
        duration: 0.45,
        ease: 'power2.out'
    });
}

function formatMessageText(text) {
    return String(text || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/\n/g, '<br>');
}

function addMessage(message, isAdmin) {
    const chatMessages = document.getElementById('chatMessages');
    const div = document.createElement('div');
    div.style.cssText = `display:flex;justify-content:${isAdmin ? 'flex-start' : 'flex-end'}; opacity:0;`;
    div.className = 'chat-message-bubble';
    div.setAttribute('data-message-id', message.id);
    
    if (isAdmin) {
        div.innerHTML = `<div style="max-width:70%;"><div style="display:flex;align-items:flex-end;gap:8px;">
            <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#c9a84c,#a8832d);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 6px rgba(201,168,76,0.2);">
                <svg width="15" height="15" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <div class="chat-bubble-admin" style="background:#2a2a2a;border:1px solid rgba(255,255,255,.06);border-radius:16px;border-bottom-left-radius:4px;padding:10px 14px;">
                    <p style="color:rgba(255,255,255,.9);font-size:.875rem;line-height:1.5;white-space:normal;">${formatMessageText(message.message)}</p>
                </div>
                <p style="color:rgba(255,255,255,.25);font-size:.72rem;margin-top:4px;margin-left:4px;">${message.created_at}</p>
            </div></div></div>`;
    } else {
        div.innerHTML = `<div style="max-width:70%;"><div style="display:flex;flex-direction:column;align-items:flex-end;">
            <div style="background:linear-gradient(135deg,#c9a84c,#a8832d);border-radius:16px;border-bottom-right-radius:4px;padding:10px 14px;box-shadow:0 3px 8px rgba(201,168,76,0.25);">
                <p style="color:#111;font-size:.875rem;line-height:1.5;font-weight:500;white-space:normal;">${formatMessageText(message.message)}</p>
            </div>
            <p style="color:rgba(255,255,255,.25);font-size:.72rem;margin-top:4px;margin-right:4px;">${message.created_at}</p>
        </div></div>`;
    }
    
    // Hide empty state if first message
    const emptyState = document.getElementById('empty-state');
    if (emptyState) emptyState.remove();

    chatMessages.appendChild(div);
    
    // Trigger pop-up bouncing animation
    animateNewBubble(div);
    scrollToBottom();
    
    lastMessageId = message.id;
}

document.getElementById('chatForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    if (!message) return;
    if (!chatId) {
        try {
            const r = await fetch('{{ route("customer.chat.getOrCreate") }}', {
                method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                body: JSON.stringify({subject:'Customer Support'})
            });
            const data = await r.json();
            chatId = data.chat_id;
            document.getElementById('chatId').value = chatId;
            startPolling();
        } catch(err) { console.error(err); return; }
    }
    try {
        const r = await fetch('{{ route("customer.chat.send") }}', {
            method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({chat_id:chatId, message:message})
        });
        const data = await r.json();
        if (data.success) { addMessage(data.message, false); messageInput.value = ''; }
    } catch(err) { console.error(err); }
});

async function pollMessages() {
    if (!chatId) return;
    try {
        const r = await fetch(`{{ url('/chat') }}/${chatId}/messages?last_message_id=${lastMessageId}`);
        const data = await r.json();
        if (data.success && data.messages.length > 0) {
            if (data.messages.some(msg => msg.is_admin)) {
                showChatInterrupt('Pesan baru dari admin');
            }
            data.messages.forEach(msg => addMessage(msg, msg.is_admin));
        }
    } catch(err) { console.error(err); }
}

function startPolling() {
    if (!pollingInterval) pollingInterval = setInterval(pollMessages, 3000);
}

if (chatId) { scrollToBottom(); startPolling(); }
</script>
@endpush
