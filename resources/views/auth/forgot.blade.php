<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password — Motifnesia</title>
  <meta name="description" content="Atur ulang password akun Motifnesia Anda.">
  <link rel="icon" type="image/png" href="{{ asset('images/motifnesia_logo.png') }}">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script>
    (function(){try{if(localStorage.getItem('customerTheme')==='light')document.documentElement.classList.add('customer-light')}catch(e){}})();
  </script>
  <style>
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;background:#111;min-height:100vh;display:flex;align-items:center;justify-content:center;animation:fadeIn .5s ease forwards;}
    @keyframes fadeIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
    @keyframes shimmer{0%,100%{opacity:.4}50%{opacity:.7}}
    .auth-card{display:flex;width:100%;max-width:960px;min-height:580px;border-radius:28px;overflow:hidden;border:1px solid rgba(255,255,255,.06);box-shadow:0 40px 80px rgba(0,0,0,.6);}
    .auth-left{flex:0 0 42%;background:linear-gradient(160deg,#1a1612 0%,#0e0c0a 100%);display:flex;flex-direction:column;justify-content:center;align-items:center;padding:56px 48px;position:relative;overflow:hidden;}
    .auth-left::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 30% 50%,rgba(201,168,76,.12) 0%,transparent 65%);}
    .auth-orb{position:absolute;border-radius:50%;filter:blur(60px);animation:shimmer 4s ease infinite;}
    .auth-right{flex:1;background:#181818;display:flex;align-items:center;justify-content:center;padding:48px 56px;overflow-y:auto;}
    .form-wrap{width:100%;max-width:380px;}
    .form-title{font-family:'Playfair Display',serif;font-size:1.9rem;font-weight:700;color:#fff;margin-bottom:4px;}
    .form-subtitle{color:rgba(255,255,255,.45);font-size:.875rem;margin-bottom:24px;}
    .alert-box{padding:12px 16px;border-radius:10px;font-size:.82rem;margin-bottom:16px;}
    .alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#fca5a5;}
    .alert-success{background:rgba(52,211,153,.08);border:1px solid rgba(52,211,153,.2);color:#6ee7b7;}
    .form-field{position:relative;margin-bottom:12px;}
    .form-input{width:100%;padding:13px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;font-family:'Inter',sans-serif;outline:none;transition:border-color .2s,background .2s;}
    .form-input::placeholder{color:rgba(255,255,255,.3);}
    .form-input:focus{border-color:#c9a84c;background:rgba(201,168,76,.05);}
    .form-input-icon{padding-right:44px;}
    .toggle-pw{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:rgba(255,255,255,.35);cursor:pointer;transition:color .2s;}
    .toggle-pw:hover{color:#c9a84c;}
    .form-select{width:100%;padding:13px 16px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:rgba(255,255,255,.7);font-size:.875rem;font-family:'Inter',sans-serif;outline:none;appearance:none;margin-bottom:12px;transition:border-color .2s;}
    .form-select:focus{border-color:#c9a84c;}
    .form-select option{background:#222;color:#fff;}
    .btn-primary{width:100%;padding:13px;background:linear-gradient(135deg,#c9a84c,#a8832d);border:none;border-radius:999px;color:#111;font-size:.95rem;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;transition:all .25s;letter-spacing:.03em;margin-top:8px;}
    .btn-primary:hover{opacity:.88;transform:translateY(-1px);box-shadow:0 8px 24px rgba(201,168,76,.35);}
    .auth-theme-floating{position:fixed;top:22px;right:22px;z-index:30;width:42px;height:42px;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(24,24,24,.76);backdrop-filter:blur(14px);color:rgba(255,255,255,.76);display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;}
    .auth-theme-floating:hover{color:#c9a84c;border-color:rgba(201,168,76,.34);background:rgba(201,168,76,.1);}
    .auth-theme-floating .theme-icon-moon{display:none;}
    html.customer-light{color-scheme:light;}
    html.customer-light body{background:#f6efe4;color:#241f18;}
    html.customer-light .auth-card{background:#fffaf2;border-color:rgba(78,61,37,.14);box-shadow:0 36px 80px rgba(78,61,37,.16);}
    html.customer-light .auth-right{background:rgba(255,250,242,.96);}
    html.customer-light .form-title{color:#241f18;}
    html.customer-light .form-subtitle{color:#746858;}
    html.customer-light .form-input,html.customer-light .form-select{background:#fffdf8;border-color:rgba(78,61,37,.16);color:#241f18;}
    html.customer-light .form-input::placeholder{color:rgba(36,31,24,.42);}
    html.customer-light .form-select option{background:#fffaf2;color:#241f18;}
    html.customer-light .auth-theme-floating{background:rgba(255,250,242,.9);border-color:rgba(78,61,37,.16);color:#5f5548;}
    html.customer-light .auth-theme-floating .theme-icon-sun{display:none;}
    html.customer-light .auth-theme-floating .theme-icon-moon{display:block;}
    @media(max-width:768px){.auth-left{display:none;}.auth-right{padding:40px 28px;}.auth-card{border-radius:0;min-height:100vh;}}
  </style>
</head>
<body>
  <button type="button" class="auth-theme-floating" data-customer-theme-toggle aria-label="Ganti mode tampilan" title="Ganti mode tampilan">
    <svg class="theme-icon-sun" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 3v2.25m0 13.5V21m8.25-9h-2.25M6 12H3.75m14.78-6.53-1.59 1.59M7.06 16.94l-1.59 1.59m13.06 0-1.59-1.59M7.06 7.06 5.47 5.47M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
    <svg class="theme-icon-moon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M21 12.79A8.25 8.25 0 1 1 11.21 3 6.75 6.75 0 0 0 21 12.79Z"/></svg>
  </button>
  <div class="auth-card" style="margin:24px;">
    {{-- Left Branding --}}
    <div class="auth-left">
      <div class="auth-orb" style="width:180px;height:180px;background:#c9a84c;opacity:.05;top:-50px;right:-50px;"></div>
      <div style="position:relative;z-index:1;text-align:center;">
          <div style="width:72px;height:72px;background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.25);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;">
              <svg width="32" height="32" fill="none" stroke="#c9a84c" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
          </div>
          <div style="font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:900;color:#c9a84c;margin-bottom:12px;">Motifnesia.</div>
          <div style="width:40px;height:2px;background:linear-gradient(90deg,#c9a84c,transparent);margin:0 auto 16px;"></div>
          <p style="color:rgba(255,255,255,.5);font-size:.875rem;line-height:1.7;max-width:200px;">Atur ulang password Anda dengan mudah dan aman</p>
          <a href="{{ route('auth.login') }}" style="display:inline-block;margin-top:28px;padding:10px 28px;border:1.5px solid rgba(201,168,76,.4);border-radius:999px;color:#c9a84c;font-size:.82rem;font-weight:600;text-decoration:none;letter-spacing:.05em;transition:all .25s;"
             onmouseenter="this.style.background='rgba(201,168,76,.1)';"
             onmouseleave="this.style.background='transparent';">← Kembali Login</a>
      </div>
    </div>

    {{-- Right Form --}}
    <div class="auth-right">
      <div class="form-wrap">
        <h1 class="form-title">Reset Password</h1>
        <p class="form-subtitle">Masukkan informasi untuk mengatur ulang password</p>

        @if(session('error'))
          <div class="alert-box alert-error">{{ session('error') }}</div>
        @endif
        @if(session('success'))
          <div class="alert-box alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('auth.doForgot') }}" method="POST" id="resetForm">
          @csrf
          <div class="form-field">
            <input type="text" name="username" placeholder="Username" required class="form-input" />
          </div>
          <select name="secret_question" required class="form-select">
            <option value="">-- Pilih Pertanyaan Rahasia --</option>
            <option value="makanan">Apa makanan favoritmu?</option>
            <option value="hewan">Apa hewan peliharaan pertamamu?</option>
            <option value="hobi">Apa hobimu?</option>
          </select>
          <div class="form-field">
            <input type="text" name="secret_answer" placeholder="Jawaban Rahasia" required class="form-input" />
          </div>
          <div class="form-field">
            <input type="password" id="newPassword" name="new_password" placeholder="Password Baru" required class="form-input form-input-icon" />
            <button type="button" onclick="togglePw('newPassword',this)" class="toggle-pw">
              <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
          <div class="form-field">
            <input type="password" id="confirmPassword" name="new_password_confirmation" placeholder="Konfirmasi Password Baru" required class="form-input form-input-icon" />
            <button type="button" onclick="togglePw('confirmPassword',this)" class="toggle-pw">
              <svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
          <button type="submit" class="btn-primary">RESET PASSWORD</button>
        </form>

        <p style="text-align:center;color:rgba(255,255,255,.35);font-size:.78rem;margin-top:16px;">
          Ingat password Anda? <a href="{{ route('auth.login') }}" style="color:#c9a84c;font-weight:600;text-decoration:none;">Masuk</a>
        </p>
      </div>
    </div>
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
    }
    document.getElementById('resetForm').addEventListener('submit', function(e) {
      const pw = document.getElementById('newPassword').value;
      const cpw = document.getElementById('confirmPassword').value;
      if (pw.length < 6) { alert('Password minimal 6 karakter!'); e.preventDefault(); }
      else if (pw !== cpw) { alert('Konfirmasi password tidak cocok!'); e.preventDefault(); }
    });
  </script>
</body>
</html>
