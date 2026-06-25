@extends('customer.layouts.mainLayout')

@section('container')
{{-- Load CSS for AOS --}}
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />

<style>
    /* Premium Glassmorphism Variables & Utilities */
    .glass-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(16px) saturate(120%);
        -webkit-backdrop-filter: blur(16px) saturate(120%);
        border: 1px solid rgba(255, 255, 255, 0.07);
        border-radius: 24px;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    html.customer-light .glass-card {
        background: rgba(78, 61, 37, 0.03);
        border: 1px solid rgba(78, 61, 37, 0.1);
        box-shadow: 0 10px 30px rgba(78, 61, 37, 0.06);
    }

    .glass-card:hover {
        transform: translateY(-5px);
        border-color: rgba(201, 168, 76, 0.35);
        box-shadow: 0 20px 45px rgba(201, 168, 76, 0.12);
    }

    /* Pulse glow effect for buttons */
    .btn-glow-gold {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 0 15px rgba(201, 168, 76, 0.2);
    }
    .btn-glow-gold::after {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.15), transparent);
        transform: rotate(45deg);
        transition: all 0.6s ease;
        left: -100%;
    }
    .btn-glow-gold:hover::after {
        left: 100%;
    }
    .btn-glow-gold:hover {
        box-shadow: 0 0 25px rgba(201, 168, 76, 0.5);
        transform: scale(1.02);
    }

    /* Interactive hover for rewards cards */
    .reward-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .reward-card:hover {
        border-color: var(--clr-gold) !important;
        box-shadow: 0 15px 35px rgba(201, 168, 76, 0.15) !important;
    }

    /* Benefit items list */
    .benefit-item {
        position: relative;
        padding-left: 32px;
        transition: all 0.3s ease;
    }
    .benefit-item::before {
        content: '✓';
        position: absolute;
        left: 0; top: 0;
        width: 22px; height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(201, 168, 76, 0.12);
        color: #c9a84c;
        font-size: 0.78rem;
        font-weight: 800;
        border: 1px solid rgba(201, 168, 76, 0.2);
        transition: all 0.3s ease;
    }
    .benefit-item:hover::before {
        background: var(--clr-gold);
        color: #000;
        transform: scale(1.1) rotate(360deg);
    }

    /* Responsive grid override */
    @media (max-width: 900px) {
        .membership-register-grid,
        .membership-dashboard-grid,
        .membership-summary-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<div style="min-height:100vh;padding-top:88px;padding-bottom:60px;background:#131313; transition: background-color 0.3s;">
    <div style="max-width:1180px;margin:0 auto;padding:0 24px;">
        
        {{-- Header Section --}}
        <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:18px;margin-bottom:32px;flex-wrap:wrap;" data-aos="fade-down" data-aos-duration="1000">
            <div>
                <h1 style="font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:700;color:#fff;margin-bottom:6px;">Membership Motifnesia</h1>
                <p style="color:rgba(255,255,255,.48);font-size:.92rem;letter-spacing:0.02em;">Kumpulkan poin dari setiap transaksi Anda dan nikmati voucher eksklusif khusus member.</p>
            </div>
            @if($user->isMemberActive())
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <a href="{{ route('customer.membership.history') }}" style="padding:11px 22px;border-radius:999px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.12);color:rgba(255,255,255,.82);font-size:.85rem;font-weight:700;text-decoration:none;transition:all 0.3s;" onmouseenter="this.style.background='rgba(255,255,255,0.08)';" onmouseleave="this.style.background='rgba(255,255,255,0.04)';">Riwayat Poin</a>
                    <a href="{{ route('customer.membership.vouchers') }}" style="padding:11px 22px;border-radius:999px;background:rgba(201,168,76,.12);border:1px solid rgba(201,168,76,.28);color:#c9a84c;font-size:.85rem;font-weight:700;text-decoration:none;transition:all 0.3s;" onmouseenter="this.style.background='rgba(201,168,76,0.2)';" onmouseleave="this.style.background='rgba(201,168,76,0.12)';">Voucher Saya</a>
                </div>
            @endif
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.24);color:#86efac;border-radius:16px;padding:14px 18px;margin-bottom:20px;font-size:.9rem;font-weight:600;" data-aos="fade-in">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.24);color:#fca5a5;border-radius:16px;padding:14px 18px;margin-bottom:20px;font-size:.9rem;font-weight:600;" data-aos="fade-in">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.24);color:#fca5a5;border-radius:16px;padding:14px 18px;margin-bottom:20px;font-size:.9rem;" data-aos="fade-in">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @php
            $benefits = [
                'Mendapat poin setiap transaksi berhasil.',
                'Poin bisa ditukar menjadi voucher belanja diskon.',
                'Mendapat info katalog batik terbaru lebih awal.',
                'Akses eksklusif promo event khusus member.',
                'Notifikasi info penawaran spesial langsung ke email.',
                'Data pesanan tercatat rapi untuk program loyalitas.',
            ];
        @endphp

        {{-- USER BELUM DAFTAR MEMBER --}}
        @if(! $user->isMemberActive())
            <div class="membership-register-grid" style="display:grid;grid-template-columns:minmax(0,1.15fr) minmax(320px,.85fr);gap:24px;align-items:start;">
                
                {{-- Benefits Card --}}
                <div class="glass-card" style="padding:32px;" data-aos="fade-right" data-aos-duration="1000">
                    <h2 style="font-family:'Playfair Display',serif;color:#c9a84c;font-size:1.45rem;font-weight:700;margin-bottom:18px;">Keuntungan Eksklusif Member</h2>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;">
                        @foreach($benefits as $benefit)
                            <div class="benefit-item" style="color:rgba(255,255,255,.76);font-size:.88rem;line-height:1.6;">
                                {{ $benefit }}
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Register Form --}}
                <form action="{{ route('customer.membership.register') }}" method="POST" class="glass-card" style="padding:32px;border-color:rgba(201,168,76,.24);" data-aos="fade-left" data-aos-duration="1000">
                    @csrf
                    <div style="margin-bottom:24px;">
                        <span style="display:inline-flex;padding:5px 12px;border-radius:999px;background:rgba(201,168,76,.12);border:1px solid rgba(201,168,76,.24);color:#c9a84c;font-size:.72rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;">DAFTAR GRATIS</span>
                        <h2 style="font-family:'Playfair Display',serif;color:#fff;font-size:1.55rem;font-weight:700;margin-top:14px;margin-bottom:8px;">Bergabung Sekarang</h2>
                        <p style="color:rgba(255,255,255,.45);font-size:.85rem;line-height:1.6;">Lengkapi data diri Anda untuk mengaktifkan akun membership Motifnesia.</p>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div>
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.8rem;font-weight:700;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.02em;">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->full_name ?: $user->name) }}" required style="width:100%;padding:12px 16px;border-radius:14px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);color:#fff;outline:none;font-size:.88rem;transition:all 0.3s;" onfocus="this.style.borderColor='#c9a84c';this.style.backgroundColor='rgba(255,255,255,0.06)';" onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.backgroundColor='rgba(255,255,255,0.04)';">
                        </div>
                        <div>
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.8rem;font-weight:700;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.02em;">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required style="width:100%;padding:12px 16px;border-radius:14px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);color:#fff;outline:none;font-size:.88rem;transition:all 0.3s;" onfocus="this.style.borderColor='#c9a84c';this.style.backgroundColor='rgba(255,255,255,0.06)';" onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.backgroundColor='rgba(255,255,255,0.04)';">
                        </div>
                        <div>
                            <label style="display:block;color:rgba(255,255,255,.7);font-size:.8rem;font-weight:700;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.02em;">Nomor Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone_number) }}" required placeholder="Contoh: 081234567890" style="width:100%;padding:12px 16px;border-radius:14px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);color:#fff;outline:none;font-size:.88rem;transition:all 0.3s;" onfocus="this.style.borderColor='#c9a84c';this.style.backgroundColor='rgba(255,255,255,0.06)';" onblur="this.style.borderColor='rgba(255,255,255,0.1)';this.style.backgroundColor='rgba(255,255,255,0.04)';">
                        </div>
                    </div>

                    <button type="submit" class="btn-glow-gold" style="width:100%;margin-top:24px;padding:14px;border:0;border-radius:999px;background:linear-gradient(135deg,#c9a84c,#a8832d);color:#111;font-weight:800;cursor:pointer;font-size:.9rem;letter-spacing:0.04em;">Daftar Membership</button>
                </form>
            </div>

        {{-- USER SUDAH DAFTAR MEMBER (MEMBERSHIP DASHBOARD) --}}
        @else
            @php
                $tierInfo = $user->membership_tier_info;
            @endphp
            <div class="membership-dashboard-grid" style="display:grid;grid-template-columns:minmax(320px,.75fr) minmax(0,1.25fr);gap:24px;align-items:start;">
                
                {{-- Kiri: 3D Card & Benefit Info --}}
                <div style="display:flex;flex-direction:column;gap:20px;">
                    
                    {{-- 3D Interactive Membership Card --}}
                    <div id="three-card-container" style="width:100%;height:260px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);border-radius:24px;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;box-shadow:0 20px 40px rgba(0,0,0,0.3);backdrop-filter:blur(10px);" data-aos="zoom-in" data-aos-duration="1000">
                        <div style="position:absolute;top:20px;left:20px;z-index:10;pointer-events:none;">
                            <span id="tier-badge-visual" style="display:inline-flex;padding:5px 12px;border-radius:999px;background:rgba(255,255,255,0.05);border:1px solid {{ $tierInfo['color'] }};color:{{ $tierInfo['color'] }};font-size:.7rem;font-weight:800;letter-spacing:0.05em;text-transform:uppercase;backdrop-filter:blur(5px);">{{ $tierInfo['badge'] }}</span>
                        </div>
                        <div style="position:absolute;bottom:20px;left:20px;z-index:10;pointer-events:none;text-shadow:0 2px 4px rgba(0,0,0,0.5);">
                            <p style="color:rgba(255,255,255,0.4);font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 2px;">Member</p>
                            <h3 style="font-family:'Playfair Display',serif;color:#fff;font-size:1.15rem;font-weight:700;margin:0;">{{ $user->full_name ?: $user->name }}</h3>
                        </div>
                        <div style="position:absolute;bottom:20px;right:20px;z-index:10;pointer-events:none;text-align:right;text-shadow:0 2px 4px rgba(0,0,0,0.5);">
                            <p style="color:rgba(255,255,255,0.4);font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 2px;">Poin Saya</p>
                            <h3 style="color:#fff;font-size:1.2rem;font-weight:800;margin:0;color:#c9a84c;">{{ number_format((int) $user->reward_points, 0, ',', '.') }} pts</h3>
                        </div>
                        <!-- Canvas Three.js -->
                        <canvas id="three-card-canvas" style="position:absolute;top:0;left:0;width:100%;height:100%;outline:none;z-index:1;"></canvas>
                    </div>

                    {{-- Tier Progress --}}
                    <div class="glass-card" style="padding:24px;" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                        <div style="border-top:0px solid rgba(255,255,255,.1);">
                            <div style="display:flex;justify-content:space-between;font-size:.8rem;color:rgba(255,255,255,.7);margin-bottom:8px;">
                                <span>Total Belanja: <strong>Rp{{ number_format($tierInfo['current_spending'], 0, ',', '.') }}</strong></span>
                                @if($tierInfo['next_tier'] !== null)
                                    <span>Tingkat Berikutnya: <strong>{{ ucfirst($tierInfo['next_tier']) }}</strong></span>
                                @else
                                    <span>Tingkat Maksimum! 🎉</span>
                                @endif
                            </div>
                            
                            {{-- Progress Bar Container --}}
                            <div style="width:100%;height:8px;background:rgba(255,255,255,.1);border-radius:999px;overflow:hidden;margin-bottom:12px;position:relative;">
                                <div id="membership-progress-bar" style="width:0%;height:100%;background:linear-gradient(90deg, #c9a84c, {{ $tierInfo['color'] }});border-radius:999px;" data-target="{{ $tierInfo['progress_percentage'] }}"></div>
                            </div>
                            
                            @if($tierInfo['next_tier'] !== null)
                                <p style="color:rgba(255,255,255,.45);font-size:.78rem;margin:0;line-height:1.4;">Belanja kurang <strong>Rp{{ number_format($tierInfo['needed_spending'], 0, ',', '.') }}</strong> untuk naik ke tier {{ ucfirst($tierInfo['next_tier']) }}.</p>
                            @else
                                <p style="color:#86efac;font-size:.78rem;margin:0;line-height:1.4;">Anda berada di tier tertinggi dengan benefit diskon 5% & 2x poin multiplier!</p>
                            @endif
                        </div>
                    </div>

                    {{-- Benefit List --}}
                    <div class="glass-card" style="padding:24px;" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                        <h3 style="font-family:'Playfair Display',serif;color:#c9a84c;font-size:1.15rem;font-weight:700;margin-bottom:16px;">Manfaat Eksklusif Tier</h3>
                        <div style="display:flex;flex-direction:column;gap:12px;">
                            @foreach($benefits as $benefit)
                                <div class="benefit-item" style="color:rgba(255,255,255,.68);font-size:.84rem;line-height:1.5;">
                                    {{ $benefit }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Kanan: Penukaran Voucher & Histori --}}
                <div style="display:flex;flex-direction:column;gap:24px;">
                    
                    {{-- Voucher Grid --}}
                    <div class="glass-card" style="padding:28px;" data-aos="fade-left" data-aos-duration="1000">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px;">
                            <h2 style="font-family:'Playfair Display',serif;color:#fff;font-size:1.35rem;font-weight:700;">Tukar Poin Jadi Voucher</h2>
                            <span style="color:rgba(255,255,255,.4);font-size:.78rem;letter-spacing:0.02em;">1 poin tiap Rp10.000 belanja</span>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;">
                            @forelse($rewards as $reward)
                                @php $canRedeem = (int) $user->reward_points >= (int) $reward->points_required; @endphp
                                <div class="reward-card" style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:20px;display:flex;flex-direction:column;gap:14px;transition:all 0.3s;">
                                    <div>
                                        <h3 style="color:#fff;font-size:1.05rem;font-weight:800;margin-bottom:6px;">{{ $reward->title }}</h3>
                                        <p style="color:#c9a84c;font-size:.85rem;font-weight:800;margin-bottom:10px;">{{ $reward->discount_label }}</p>
                                        <p style="color:rgba(255,255,255,.48);font-size:.8rem;line-height:1.55;">{{ $reward->description ?: 'Voucher potongan belanja khusus member Motifnesia.' }}</p>
                                    </div>
                                    <div style="margin-top:auto;">
                                        <div style="color:rgba(255,255,255,.62);font-size:.8rem;margin-bottom:12px;">Butuh <strong style="color:#fff;">{{ number_format($reward->points_required, 0, ',', '.') }}</strong> poin</div>
                                        <form action="{{ route('customer.membership.redeem', $reward->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" {{ $canRedeem ? '' : 'disabled' }} class="{{ $canRedeem ? 'btn-glow-gold' : '' }}" style="width:100%;padding:10px 14px;border-radius:999px;border:0;font-size:.82rem;font-weight:800;cursor:{{ $canRedeem ? 'pointer' : 'not-allowed' }};background:{{ $canRedeem ? 'linear-gradient(135deg,#c9a84c,#a8832d)' : 'rgba(255,255,255,.08)' }};color:{{ $canRedeem ? '#111' : 'rgba(255,255,255,.32)' }};transition:all 0.3s;">
                                                {{ $canRedeem ? 'Tukar Voucher' : 'Poin Belum Cukup' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div style="grid-column:1/-1;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:18px;padding:32px;text-align:center;color:rgba(255,255,255,.45);">Belum ada voucher membership aktif.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Summary Grid --}}
                    <div class="membership-summary-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                        
                        {{-- Riwayat Poin --}}
                        <div class="glass-card" style="padding:24px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                                <h3 style="font-family:'Playfair Display',serif;color:#fff;font-size:1.15rem;font-weight:700;">Riwayat Poin</h3>
                                <a href="{{ route('customer.membership.history') }}" style="color:#c9a84c;font-size:.78rem;text-decoration:none;font-weight:700;transition:all 0.3s;" onmouseenter="this.style.color='#fff';" onmouseleave="this.style.color='#c9a84c';">Lihat Semua</a>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:2px;">
                                @forelse($pointTransactions as $transaction)
                                    @php
                                        $pointValue = (int) $transaction->points;
                                        $isPositive = $transaction->type === 'earn' || ($transaction->type === 'adjust' && $pointValue >= 0);
                                    @endphp
                                    <div class="transaction-item" style="display:flex;justify-content:space-between;gap:12px;padding:12px 0;border-bottom:1px solid rgba(255,255,255,.04);">
                                        <div>
                                            <p style="color:rgba(255,255,255,.82);font-size:.82rem;font-weight:700;margin-bottom:4px;line-height:1.4;">{{ $transaction->description ?: ucfirst($transaction->type) }}</p>
                                            <p style="color:rgba(255,255,255,.35);font-size:.72rem;">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                                        </div>
                                        <span style="color:{{ $isPositive ? '#86efac' : '#fca5a5' }};font-weight:800;font-size:.88rem;white-space:nowrap;">{{ $isPositive ? '+' : '-' }}{{ number_format(abs($pointValue), 0, ',', '.') }}</span>
                                    </div>
                                @empty
                                    <p style="color:rgba(255,255,255,.45);font-size:.85rem;margin:20px 0;text-align:center;">Belum ada riwayat poin.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Voucher Saya --}}
                        <div class="glass-card" style="padding:24px;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                                <h3 style="font-family:'Playfair Display',serif;color:#fff;font-size:1.15rem;font-weight:700;">Voucher Saya</h3>
                                <a href="{{ route('customer.membership.vouchers') }}" style="color:#c9a84c;font-size:.78rem;text-decoration:none;font-weight:700;transition:all 0.3s;" onmouseenter="this.style.color='#fff';" onmouseleave="this.style.color='#c9a84c';">Lihat Semua</a>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:8px;">
                                @forelse($vouchers as $voucher)
                                    <div style="padding:14px;background:rgba(255,255,255,0.015);border:1px solid rgba(255,255,255,0.04);border-radius:14px;display:flex;flex-direction:column;gap:6px;">
                                        <p style="color:#fff;font-size:.84rem;font-weight:800;margin:0;">{{ $voucher->reward->title ?? 'Voucher Member' }}</p>
                                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:2px;">
                                            <code style="background:rgba(201,168,76,.12);border:1px dashed rgba(201,168,76,.35);color:#c9a84c;border-radius:8px;padding:4px 8px;font-size:.76rem;letter-spacing:0.02em;">{{ $voucher->voucher_code }}</code>
                                            <span style="color:rgba(255,255,255,.3);font-size:.7rem;">{{ $voucher->status === 'active' ? 'Aktif' : 'Terpakai' }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p style="color:rgba(255,255,255,.45);font-size:.85rem;margin:20px 0;text-align:center;">Belum ada voucher yang ditukar.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
    document.addEventListener('DOMContentLoaded', () => {
        // ==================== 1. INITIALIZE AOS ====================
        AOS.init({
            once: true,
            duration: 800,
            easing: 'ease-out-cubic'
        });



        // ==================== 3. ANIMATE PROGRESS BAR ====================
        const progressBar = document.getElementById('membership-progress-bar');
        if (progressBar) {
            const targetWidth = progressBar.dataset.target || 0;
            gsap.to(progressBar, {
                width: `${targetWidth}%`,
                duration: 1.5,
                ease: 'power3.out',
                delay: 0.3
            });
        }

        // ==================== 4. THREE.JS 3D MEMBERSHIP CARD ====================
        const canvas = document.getElementById('three-card-canvas');
        const container = document.getElementById('three-card-container');
        
        if (canvas && container) {
            // Get user tier parameters
            @if($user->isMemberActive())
                const tier = "{{ $user->membership_tier ?? 'bronze' }}";
                const userName = "{{ $user->full_name ?: $user->name }}";
            @else
                const tier = 'bronze';
                const userName = 'Guest';
            @endif

            // Scene setup
            const scene = new THREE.Scene();
            
            // Camera setup
            const camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 100);
            camera.position.z = 5.8;

            // Renderer setup
            const renderer = new THREE.WebGLRenderer({
                canvas: canvas,
                alpha: true,
                antialias: true
            });
            renderer.setSize(container.clientWidth, container.clientHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

            // Helper to draw realistic credit card textures dynamically using HTML5 Canvas
            function createCardTexture(tierName, holderName) {
                const texCanvas = document.createElement('canvas');
                texCanvas.width = 512;
                texCanvas.height = 320;
                const ctx = texCanvas.getContext('2d');

                // Metallic background gradient
                const grad = ctx.createLinearGradient(0, 0, 512, 320);
                if (tierName === 'gold') {
                    grad.addColorStop(0, '#1c170d');
                    grad.addColorStop(0.5, '#352914');
                    grad.addColorStop(1, '#110e08');
                } else if (tierName === 'silver') {
                    grad.addColorStop(0, '#1a1b1d');
                    grad.addColorStop(0.5, '#2e3035');
                    grad.addColorStop(1, '#101112');
                } else {
                    grad.addColorStop(0, '#1a130f');
                    grad.addColorStop(0.5, '#332117');
                    grad.addColorStop(1, '#100c0a');
                }
                ctx.fillStyle = grad;
                ctx.fillRect(0, 0, 512, 320);

                // Subtle repeating Kawung/batik lines
                ctx.strokeStyle = tierName === 'gold' ? 'rgba(201,168,76,0.08)' : (tierName === 'silver' ? 'rgba(192,192,192,0.06)' : 'rgba(205,127,50,0.08)');
                ctx.lineWidth = 1;
                for (let x = -20; x < 540; x += 40) {
                    for (let y = -20; y < 340; y += 40) {
                        ctx.beginPath();
                        ctx.arc(x, y, 20, 0, Math.PI * 2);
                        ctx.stroke();
                    }
                }

                // Inner card border
                ctx.strokeStyle = tierName === 'gold' ? 'rgba(201,168,76,0.3)' : (tierName === 'silver' ? 'rgba(192,192,192,0.25)' : 'rgba(205,127,50,0.3)');
                ctx.lineWidth = 4;
                ctx.strokeRect(12, 12, 488, 296);

                // Brand name
                ctx.fillStyle = tierName === 'gold' ? '#c9a84c' : (tierName === 'silver' ? '#c0c0c0' : '#cd7f32');
                ctx.font = 'bold 22px "Playfair Display", Georgia, serif';
                ctx.fillText('MOTIFNESIA', 35, 52);

                // Holographic Golden/Silver chip
                ctx.fillStyle = tierName === 'gold' ? '#c9a84c' : (tierName === 'silver' ? '#a5a5a5' : '#cd7f32');
                ctx.fillRect(35, 85, 52, 36);
                ctx.strokeStyle = 'rgba(0,0,0,0.2)';
                ctx.lineWidth = 2;
                ctx.strokeRect(35, 85, 52, 36);
                // Chip inner lines
                ctx.beginPath();
                ctx.moveTo(35, 103); ctx.lineTo(87, 103);
                ctx.moveTo(61, 85); ctx.lineTo(61, 121);
                ctx.stroke();

                // Cardholder details
                ctx.fillStyle = '#ffffff';
                ctx.font = '600 15px "Poppins", sans-serif';
                ctx.fillText(holderName.toUpperCase(), 35, 275);

                // Tier text
                ctx.fillStyle = tierName === 'gold' ? '#ffd700' : (tierName === 'silver' ? '#e5e5e5' : '#d2691e');
                ctx.font = '900 22px "Poppins", sans-serif';
                ctx.fillText((tierName + ' member').toUpperCase(), 35, 235);

                // Premium visual watermark circle
                ctx.beginPath();
                ctx.arc(430, 240, 36, 0, Math.PI * 2);
                ctx.fillStyle = tierName === 'gold' ? 'rgba(201,168,76,0.18)' : (tierName === 'silver' ? 'rgba(192,192,192,0.12)' : 'rgba(205,127,50,0.18)');
                ctx.fill();

                return new THREE.CanvasTexture(texCanvas);
            }

            // Create card geometry and mesh standard/physical material
            const texture = createCardTexture(tier, userName);
            const cardGeometry = new THREE.BoxGeometry(4.2, 2.6, 0.08);
            
            // Premium physically-based material for metallic shine
            const cardMaterial = new THREE.MeshPhysicalMaterial({
                map: texture,
                metalness: tier === 'gold' || tier === 'silver' ? 0.92 : 0.76,
                roughness: tier === 'gold' ? 0.16 : (tier === 'silver' ? 0.12 : 0.22),
                clearcoat: 1.0,
                clearcoatRoughness: 0.1,
                bumpScale: 0.05
            });

            const cardMesh = new THREE.Mesh(cardGeometry, cardMaterial);
            scene.add(cardMesh);

            // Light sources
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
            scene.add(ambientLight);

            // Standard directional light
            const dirLight = new THREE.DirectionalLight(0xffffff, 0.8);
            dirLight.position.set(2, 4, 6);
            scene.add(dirLight);

            // Spotlight / Point light following mouse coordinates to create shifting specular reflections
            const pointLightColor = tier === 'gold' ? 0xffd700 : (tier === 'silver' ? 0xffffff : 0xffaa66);
            const cursorLight = new THREE.PointLight(pointLightColor, 2.5, 12);
            cursorLight.position.set(0, 0, 3);
            scene.add(cursorLight);

            // Particle Star field system
            const particleCount = 70;
            const particleGeo = new THREE.BufferGeometry();
            const positions = new Float32Array(particleCount * 3);

            for(let i=0; i<particleCount*3; i+=3) {
                positions[i] = (Math.random() - 0.5) * 10;
                positions[i+1] = (Math.random() - 0.5) * 6;
                positions[i+2] = (Math.random() - 0.5) * 4 - 1;
            }

            particleGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
            
            const particleColor = tier === 'gold' ? 0xc9a84c : (tier === 'silver' ? 0xcccccc : 0xcd7f32);
            const particleMat = new THREE.PointsMaterial({
                size: 0.05,
                color: particleColor,
                transparent: true,
                opacity: 0.7,
                blending: THREE.AdditiveBlending
            });

            const particlesMesh = new THREE.Points(particleGeo, particleMat);
            scene.add(particlesMesh);

            // Mouse interaction variables
            let mouseX = 0;
            let mouseY = 0;
            let targetRotX = 0;
            let targetRotY = 0;

            container.addEventListener('mousemove', (e) => {
                const rect = container.getBoundingClientRect();
                // Normalize to [-1, 1]
                mouseX = ((e.clientX - rect.left) / container.clientWidth) * 2 - 1;
                mouseY = -((e.clientY - rect.top) / container.clientHeight) * 2 + 1;

                targetRotX = mouseY * 0.45;
                targetRotY = mouseX * 0.55;

                // Move point light slightly towards the cursor to highlight standard material specular
                cursorLight.position.x = mouseX * 2.5;
                cursorLight.position.y = mouseY * 1.5;
            });

            container.addEventListener('mouseleave', () => {
                targetRotX = 0;
                targetRotY = 0;
                gsap.to(cursorLight.position, {x: 0, y: 0, z: 3, duration: 1});
            });

            // GSAP entrance spin-in animation
            cardMesh.rotation.y = Math.PI * 2.5;
            cardMesh.scale.set(0.1, 0.1, 0.1);
            
            gsap.to(cardMesh.scale, {
                x: 1, y: 1, z: 1,
                duration: 1.5,
                ease: 'back.out(1.2)'
            });
            gsap.to(cardMesh.rotation, {
                y: 0,
                duration: 1.8,
                ease: 'power3.out'
            });

            // Clock for idle animations
            const clock = new THREE.Clock();

            // Render loop
            function animate() {
                requestAnimationFrame(animate);

                const elapsedTime = clock.getElapsedTime();

                // Smooth rotation interpolation (lerp)
                cardMesh.rotation.x += (targetRotX - cardMesh.rotation.x) * 0.08;
                cardMesh.rotation.y += (targetRotY - cardMesh.rotation.y) * 0.08;

                // Idle floating wave effect
                cardMesh.position.y = Math.sin(elapsedTime * 1.6) * 0.08;
                cardMesh.rotation.z = Math.cos(elapsedTime * 0.8) * 0.02;

                // Animate floating sparkles upwards
                const particlePositions = particleGeo.attributes.position.array;
                for (let i = 1; i < particleCount * 3; i += 3) {
                    particlePositions[i] += 0.006; // move up
                    if (particlePositions[i] > 3) {
                        particlePositions[i] = -3; // wrap to bottom
                    }
                }
                particleGeo.attributes.position.needsUpdate = true;

                renderer.render(scene, camera);
            }

            animate();

            // Window resize handler
            window.addEventListener('resize', () => {
                camera.aspect = container.clientWidth / container.clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(container.clientWidth, container.clientHeight);
            });
        }
    });
</script>
@endpush
