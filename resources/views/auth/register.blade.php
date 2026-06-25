<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar - Motifnesia</title>
  <meta name="description" content="Buat akun Motifnesia dan mulai belanja koleksi batik nusantara premium.">
  <link rel="icon" type="image/png" href="{{ asset('images/motifnesia_logo.png') }}">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script>
    (function(){try{if(localStorage.getItem('customerTheme')==='light')document.documentElement.classList.add('customer-light')}catch(e){}})();
  </script>
  <style>
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;background:#0f0f0d;color:#fff;min-height:100vh;overflow-x:hidden;}
    body::before{content:'';position:fixed;inset:0;z-index:-2;background:#0f0f0d;}
    body::after{content:'';position:fixed;inset:0;z-index:-1;opacity:.22;background-image:linear-gradient(rgba(15,15,13,.72),rgba(15,15,13,.9)),radial-gradient(circle at 18% 18%,rgba(201,168,76,.16),transparent 28%),radial-gradient(circle at 78% 72%,rgba(201,168,76,.1),transparent 26%),repeating-linear-gradient(135deg,rgba(201,168,76,.05) 0 1px,transparent 1px 22px),repeating-linear-gradient(45deg,rgba(255,255,255,.035) 0 1px,transparent 1px 26px);}
    @keyframes fadeIn{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
    .auth-page{min-height:100vh;padding:24px;}
    .auth-mini-bar{position:fixed;top:22px;left:50%;transform:translateX(-50%);width:min(1180px,calc(100% - 48px));height:64px;padding:0 18px;border:1px solid rgba(255,255,255,.1);border-radius:22px;background:rgba(18,18,17,.72);backdrop-filter:blur(18px);box-shadow:0 18px 50px rgba(0,0,0,.24);display:flex;align-items:center;justify-content:space-between;gap:18px;z-index:20;}
    .auth-mini-brand{display:flex;align-items:center;gap:10px;color:#fff;text-decoration:none;font-weight:800;letter-spacing:-.02em;}
    .auth-mini-brand img{width:34px;height:34px;border-radius:10px;object-fit:cover;border:1px solid rgba(201,168,76,.28);}
    .auth-mini-brand span{font-family:'Playfair Display',serif;font-size:1.15rem;}
    .auth-mini-nav{display:flex;align-items:center;gap:8px;}
    .auth-mini-nav a{color:rgba(255,255,255,.64);text-decoration:none;font-size:.82rem;font-weight:700;padding:9px 13px;border-radius:999px;transition:all .2s;}
    .auth-mini-nav a:hover{color:#c9a84c;background:rgba(201,168,76,.08);}
    .auth-mini-nav a.active{color:#111;background:#c9a84c;}
    .auth-theme-toggle{width:36px;height:36px;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.05);color:rgba(255,255,255,.72);display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;}
    .auth-theme-toggle:hover{color:#c9a84c;border-color:rgba(201,168,76,.34);background:rgba(201,168,76,.1);}
    .auth-theme-toggle .theme-icon-moon{display:none;}
    .auth-stage{min-height:calc(100vh - 48px);display:flex;align-items:center;justify-content:center;padding:104px 0 28px;animation:fadeIn .5s ease forwards;}
    .auth-card{display:grid;grid-template-columns:minmax(560px,.6fr) minmax(340px,.4fr);width:100%;max-width:1180px;min-height:640px;border-radius:30px;overflow:hidden;border:1px solid rgba(255,255,255,.08);box-shadow:0 42px 90px rgba(0,0,0,.58);background:#171717;}
    .auth-form-panel{background:rgba(24,24,24,.96);display:flex;align-items:center;justify-content:center;padding:48px 54px;overflow-y:auto;}
    .auth-brand-panel{position:relative;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:58px 42px;overflow:hidden;background:linear-gradient(160deg,rgba(26,22,18,.86),rgba(12,11,10,.94)),url('{{ asset('assets/konten/1765697674_slide_slideshow__2_.webp') }}') center/cover;}
    .auth-brand-panel::before{content:'';position:absolute;inset:0;background:radial-gradient(circle at 68% 26%,rgba(201,168,76,.2),transparent 30%),linear-gradient(135deg,rgba(201,168,76,.07) 0 1px,transparent 1px 18px);opacity:.75;}
    .auth-brand-panel::after{content:'';position:absolute;inset:26px;border:1px solid rgba(201,168,76,.16);border-radius:24px;pointer-events:none;}
    .auth-logo{position:relative;z-index:1;font-family:'Playfair Display',serif;font-size:3rem;font-weight:900;color:#c9a84c;letter-spacing:-.03em;margin-bottom:14px;}
    .auth-divider{position:relative;z-index:1;width:48px;height:2px;background:linear-gradient(90deg,transparent,#c9a84c,transparent);margin:16px auto 24px;}
    .auth-tagline{position:relative;z-index:1;color:rgba(255,255,255,.66);font-size:.95rem;line-height:1.7;max-width:260px;}
    .benefit-pills{position:relative;z-index:1;display:flex;flex-wrap:wrap;justify-content:center;gap:8px;margin-top:26px;}
    .benefit-pills span{padding:7px 11px;border-radius:999px;background:rgba(0,0,0,.34);border:1px solid rgba(201,168,76,.24);color:rgba(255,255,255,.72);font-size:.72rem;font-weight:700;}
    .auth-switch-text{position:relative;z-index:1;color:rgba(255,255,255,.58);font-size:.85rem;margin-top:34px;}
    .auth-btn-outline{position:relative;z-index:1;display:inline-block;margin-top:14px;padding:10px 30px;border:1.5px solid rgba(201,168,76,.52);border-radius:999px;color:#c9a84c;font-size:.84rem;font-weight:800;text-decoration:none;letter-spacing:.04em;transition:all .25s;}
    .auth-btn-outline:hover{background:#c9a84c;color:#111;border-color:#c9a84c;}
    .form-wrap{width:100%;max-width:610px;}
    .mobile-brand{display:none;margin-bottom:26px;}
    .mobile-brand strong{font-family:'Playfair Display',serif;color:#c9a84c;font-size:1.7rem;}
    .mobile-brand p{color:rgba(255,255,255,.5);font-size:.84rem;margin-top:4px;}
    .form-title{font-family:'Playfair Display',serif;font-size:2.1rem;font-weight:800;color:#fff;margin-bottom:6px;}
    .form-subtitle{color:rgba(255,255,255,.48);font-size:.9rem;margin-bottom:22px;}
    .alert-box{padding:13px 15px;border-radius:14px;font-size:.83rem;margin-bottom:18px;line-height:1.45;}
    .alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#fca5a5;}
    .alert-success{background:rgba(52,211,153,.08);border:1px solid rgba(52,211,153,.22);color:#86efac;}
    .form-section{padding:18px;border:1px solid rgba(255,255,255,.08);border-radius:18px;background:rgba(255,255,255,.025);margin-bottom:14px;}
    .section-title{display:flex;align-items:center;gap:10px;color:#f4e6b5;font-size:.82rem;font-weight:900;letter-spacing:.04em;text-transform:uppercase;margin-bottom:14px;}
    .section-title span{width:8px;height:8px;border-radius:50%;background:#c9a84c;box-shadow:0 0 12px rgba(201,168,76,.72);}
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
    .form-field{position:relative;margin-bottom:12px;}
    .form-section .form-field:last-child{margin-bottom:0;}
    .form-label{display:block;color:rgba(255,255,255,.7);font-size:.76rem;font-weight:800;margin-bottom:8px;}
    .form-input,.form-select{width:100%;padding:13px 15px;background:rgba(255,255,255,.055);border:1px solid rgba(255,255,255,.1);border-radius:14px;color:#fff;font-size:.9rem;font-family:'Inter',sans-serif;outline:none;transition:border-color .2s,background .2s,box-shadow .2s;}
    .form-input::placeholder{color:rgba(255,255,255,.28);}
    .form-input:focus,.form-select:focus{border-color:#c9a84c;background:rgba(201,168,76,.06);box-shadow:0 0 0 4px rgba(201,168,76,.08);}
    .form-select{appearance:none;color:rgba(255,255,255,.76);background-image:linear-gradient(45deg,transparent 50%,rgba(255,255,255,.55) 50%),linear-gradient(135deg,rgba(255,255,255,.55) 50%,transparent 50%);background-position:calc(100% - 18px) 50%,calc(100% - 12px) 50%;background-size:6px 6px,6px 6px;background-repeat:no-repeat;}
    .form-select option{background:#181818;color:#fff;}
    .form-input-icon{padding-right:44px;}
    .toggle-pw{position:absolute;right:12px;bottom:11px;background:none;border:none;color:rgba(255,255,255,.42);cursor:pointer;padding:4px;transition:color .2s;}
    .toggle-pw:hover{color:#c9a84c;}
    .form-note{display:block;color:rgba(255,255,255,.42);font-size:.73rem;margin-top:7px;line-height:1.45;}
    .btn-primary{width:100%;padding:14px;background:linear-gradient(135deg,#d4b865,#b4923f);border:none;border-radius:999px;color:#111;font-size:.95rem;font-weight:900;font-family:'Inter',sans-serif;cursor:pointer;transition:all .25s;letter-spacing:.04em;margin-top:4px;}
    .btn-primary:hover{transform:translateY(-1px);box-shadow:0 10px 28px rgba(201,168,76,.32);}
    .form-footer{text-align:center;color:rgba(255,255,255,.38);font-size:.8rem;margin-top:18px;}
    .form-link{color:#c9a84c;text-decoration:none;font-weight:800;transition:opacity .2s;}
    .form-link:hover{opacity:.75;}
    html.customer-light{color-scheme:light;}
    html.customer-light body{background:#f6efe4;color:#241f18;}
    html.customer-light body::before{background:#f6efe4;}
    html.customer-light body::after{opacity:.2;background-image:linear-gradient(rgba(246,239,228,.78),rgba(246,239,228,.94)),radial-gradient(circle at 18% 18%,rgba(201,168,76,.18),transparent 28%),radial-gradient(circle at 78% 72%,rgba(201,168,76,.12),transparent 26%),repeating-linear-gradient(135deg,rgba(168,131,45,.07) 0 1px,transparent 1px 22px);}
    html.customer-light .auth-mini-bar{background:rgba(255,250,242,.92);border-color:rgba(78,61,37,.16);box-shadow:0 18px 50px rgba(78,61,37,.14);}
    html.customer-light .auth-mini-brand,html.customer-light .auth-mini-nav a:not(.active){color:#241f18;}
    html.customer-light .auth-theme-toggle{background:rgba(36,31,24,.05);border-color:rgba(78,61,37,.16);color:#5f5548;}
    html.customer-light .auth-theme-toggle .theme-icon-sun{display:none;}
    html.customer-light .auth-theme-toggle .theme-icon-moon{display:block;}
    html.customer-light .auth-card{background:#fffaf2;border-color:rgba(78,61,37,.14);box-shadow:0 36px 80px rgba(78,61,37,.16);}
    html.customer-light .auth-form-panel{background:rgba(255,250,242,.96);}
    html.customer-light .form-title{color:#241f18;}
    html.customer-light .form-subtitle,html.customer-light .form-footer,html.customer-light .form-note{color:#746858;}
    html.customer-light .form-section{background:rgba(255,255,255,.58);border-color:rgba(78,61,37,.14);}
    html.customer-light .form-label{color:#3a3126;}
    html.customer-light .form-input,html.customer-light .form-select{background:#fffdf8;border-color:rgba(78,61,37,.16);color:#241f18;}
    html.customer-light .form-input::placeholder{color:rgba(36,31,24,.42);}
    html.customer-light .form-select option{background:#fffaf2;color:#241f18;}
    html.customer-light .mobile-brand p{color:#746858;}
    @media(max-width:980px){
      .auth-card{grid-template-columns:1fr;max-width:680px;}
      .auth-brand-panel{display:none;}
      .mobile-brand{display:block;}
      .auth-form-panel{padding:42px 30px;}
    }
    @media(max-width:860px){
      .auth-page{padding:14px;}
      .auth-mini-bar{position:relative;top:auto;left:auto;transform:none;width:100%;margin:0 auto 18px;border-radius:18px;height:auto;min-height:62px;flex-wrap:wrap;}
      .auth-mini-nav{margin-left:auto;}
      .auth-stage{min-height:auto;padding:0 0 18px;}
    }
    @media(max-width:620px){
      .auth-mini-bar{padding:12px;}
      .auth-mini-brand span{font-size:1rem;}
      .auth-mini-nav{width:100%;justify-content:space-between;}
      .auth-mini-nav a{font-size:.76rem;padding:8px 10px;}
      .auth-card{border-radius:24px;}
      .auth-form-panel{padding:32px 20px;}
      .form-grid{grid-template-columns:1fr;}
      .form-title{font-size:1.9rem;}
      .form-section{padding:15px;}
    }
  </style>
</head>
<body>
  <div class="auth-page">
    <header class="auth-mini-bar" aria-label="Navigasi autentikasi">
      <a href="{{ route('customer.home') }}" class="auth-mini-brand">
        <img src="{{ asset('images/motifnesia_logo.png') }}" alt="Motifnesia">
        <span>Motifnesia.</span>
      </a>
      <nav class="auth-mini-nav">
        <a href="{{ route('customer.home') }}">Kembali ke Beranda</a>
        <a href="{{ route('auth.login') }}">Masuk</a>
        <a href="{{ route('auth.register') }}" class="active">Daftar</a>
        <button type="button" class="auth-theme-toggle" data-customer-theme-toggle aria-label="Ganti mode tampilan" title="Ganti mode tampilan">
          <svg class="theme-icon-sun w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 3v2.25m0 13.5V21m8.25-9h-2.25M6 12H3.75m14.78-6.53-1.59 1.59M7.06 16.94l-1.59 1.59m13.06 0-1.59-1.59M7.06 7.06 5.47 5.47M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
          <svg class="theme-icon-moon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M21 12.79A8.25 8.25 0 1 1 11.21 3 6.75 6.75 0 0 0 21 12.79Z"/></svg>
        </button>
      </nav>
    </header>

    <main class="auth-stage">
      <div class="auth-card">
        <section class="auth-form-panel">
          <div class="form-wrap">
            <div class="mobile-brand">
              <strong>Motifnesia.</strong>
              <p>Buat akun untuk mulai belanja batik premium.</p>
            </div>

            <h1 class="form-title">Buat Akun</h1>
            <p class="form-subtitle">Isi data berikut untuk menyimpan pesanan, voucher, dan benefit membership Anda.</p>

            @if($errors->any())
              <div class="alert-box alert-error">Cek kembali data berikut: {{ $errors->first() }}</div>
            @endif
            @if(session('error'))
              <div class="alert-box alert-error">{{ session('error') }}</div>
            @endif
            @if(session('success'))
              <div class="alert-box alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('auth.doRegister') }}" method="POST" id="registerForm">
              @csrf
              <div class="form-section">
                <div class="section-title"><span></span>Akun</div>
                <div class="form-grid">
                  <div class="form-field">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Contoh: motiflover" required autocomplete="username" class="form-input" />
                  </div>
                  <div class="form-field">
                    <label for="full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" placeholder="Opsional" autocomplete="name" class="form-input" />
                  </div>
                </div>
                <div class="form-field">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autocomplete="email" class="form-input" />
                </div>
              </div>

              <div class="form-section">
                <div class="section-title"><span></span>Keamanan</div>
                <div class="form-grid">
                  <div class="form-field">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" placeholder="Buat password" required minlength="6" autocomplete="new-password" class="form-input form-input-icon" />
                    <button type="button" onclick="togglePw('password',this)" class="toggle-pw" aria-label="Tampilkan password">
                      <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                    <span class="form-note">Minimal 6 karakter.</span>
                  </div>
                  <div class="form-field">
                    <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="confirm_password" placeholder="Ulangi password" required minlength="6" autocomplete="new-password" class="form-input form-input-icon" />
                    <button type="button" onclick="togglePw('confirm_password',this)" class="toggle-pw" aria-label="Tampilkan password">
                      <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </div>
                </div>
              </div>

              <div class="form-section">
                <div class="section-title"><span></span>Info Tambahan</div>
                <div class="form-field">
                  <label for="secret_question" class="form-label">Pertanyaan Keamanan</label>
                  <select id="secret_question" name="secret_question" required class="form-select">
                    <option value="">Pilih pertanyaan keamanan</option>
                    <option value="makanan" {{ old('secret_question') === 'makanan' ? 'selected' : '' }}>Apa makanan favoritmu?</option>
                    <option value="hewan" {{ old('secret_question') === 'hewan' ? 'selected' : '' }}>Apa hewan peliharaan pertamamu?</option>
                    <option value="hobi" {{ old('secret_question') === 'hobi' ? 'selected' : '' }}>Apa hobimu?</option>
                  </select>
                  <span class="form-note">Dipakai untuk membantu reset password jika lupa.</span>
                </div>
                <div class="form-field">
                  <label for="secret_answer" class="form-label">Jawaban Keamanan</label>
                  <input type="text" id="secret_answer" name="secret_answer" value="{{ old('secret_answer') }}" placeholder="Tulis jawaban yang mudah Anda ingat" required class="form-input" />
                </div>
              </div>

              <button type="submit" class="btn-primary">DAFTAR SEKARANG</button>
            </form>

            <p class="form-footer">
              Sudah punya akun? <a href="{{ route('auth.login') }}" class="form-link">Masuk</a>
            </p>
          </div>
        </section>

        <section class="auth-brand-panel" aria-label="Benefit Motifnesia">
          <div class="auth-logo">Motifnesia.</div>
          <div class="auth-divider"></div>
          <p class="auth-tagline">Satu akun untuk belanja batik, menyimpan voucher, dan mengikuti promo khusus member.</p>
          <div class="benefit-pills">
            <span>Koleksi Batik Premium</span>
            <span>Membership Reward</span>
            <span>Promo Member</span>
          </div>
          <p class="auth-switch-text">Sudah punya akun?</p>
          <a href="{{ route('auth.login') }}" class="auth-btn-outline">Masuk</a>
        </section>
      </div>
    </main>
  </div>

  <script>
    (function(){
      const root=document.documentElement;
      function applyTheme(theme){const t=theme==='light'?'light':'dark';root.classList.toggle('customer-light',t==='light');try{localStorage.setItem('customerTheme',t)}catch(e){}}
      document.querySelectorAll('[data-customer-theme-toggle]').forEach(btn=>btn.addEventListener('click',()=>applyTheme(root.classList.contains('customer-light')?'dark':'light')));
    })();
    function togglePw(id, btn) {
      const input = document.getElementById(id);
      input.type = input.type === 'password' ? 'text' : 'password';
      btn.setAttribute('aria-label', input.type === 'password' ? 'Tampilkan password' : 'Sembunyikan password');
    }
  </script>
</body>
</html>
