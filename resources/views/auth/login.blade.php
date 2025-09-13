<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login • Borrowing System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    :root{
      --fg:#0f172a; --muted:#8b97a8;
      --pill:#f3f4f6; --stroke:#e9edf3;
      --primary:#7C4DFF; --primary-press:#643BDB;
    }
    @media (prefers-color-scheme:dark){
      :root{ --fg:#e5e7eb; --muted:#aab4c3; --pill:#17181c; --stroke:#262b32; }
      body{ background:#0b0c10; }
    }

    html,body{height:100%}
    body{
      margin:0; color:var(--fg);
      font-family:-apple-system,BlinkMacSystemFont,"SF Pro Text","SF Pro Display",Segoe UI,Roboto,Helvetica,Arial;
      background:#ffffff;
      background-image:
        radial-gradient(160px 160px at 92% 5%, rgba(124,77,255,.16), transparent 60%),
        radial-gradient(160px 160px at 100% -20%, rgba(124,77,255,.16), transparent 60%);
    }
    .canvas{min-height:100%; display:grid; place-items:center; padding:24px}
    .sheet{
      width:min(360px, 92vw);
      text-align:center;
    }
    .plane{
      position:absolute; right:12px; top:12px; width:80px; opacity:.5
    }
    .brand{
      display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom:18px;
    }
    .brand img{width:32px; height:32px; object-fit:contain; border-radius:8px}
    .brand span{font-weight:800; letter-spacing:.2px}
    h1{font-size:1.5rem; font-weight:800; margin-bottom:16px}
    .hint{color:var(--muted); margin-bottom:18px}

    .pill{
      width:100%; border:none; outline:none; border-radius:999px;
      background:var(--pill); padding:14px 16px; font-weight:600; color:var(--fg);
      box-shadow: inset 0 0 0 1px var(--stroke);
    }
    .pill:focus{box-shadow: inset 0 0 0 2px rgba(124,77,255,.55)}

    .btn-primary{
      width:100%; border-radius:999px; padding:12px 16px; font-weight:800;
      background:var(--primary); border:1px solid var(--primary);
    }
    .btn-primary:hover{background:var(--primary-press); border-color:var(--primary-press)}

    .links{font-size:.92rem; margin-top:10px; color:var(--muted)}
    .links a{text-decoration:none; font-weight:700}
    .err{color:#d32f2f; font-size:.88rem; text-align:left; margin-top:6px}
  </style>
</head>
<body>
  <svg class="plane" viewBox="0 0 120 60" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
    <path d="M10 50 C45 40, 75 15, 115 5" stroke="#7C4DFF" stroke-width="2" stroke-linecap="round" stroke-dasharray="4 6"/>
    <path d="M110 8 l8 -4 -4 8 -4 -4z" fill="#7C4DFF"/>
  </svg>

  <div class="canvas">
    <div class="sheet">
      <div class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
        <span>Borrowing System</span>
      </div>

      <h1>Login</h1>
      <div class="hint">Admin access</div>

      @if($errors->any())
        <div class="alert alert-danger py-2 small text-start">
          <ul class="mb-0 ps-3">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('login.attempt') }}" class="d-grid gap-3 mt-3">
        @csrf
        <input class="pill" type="text" name="username" placeholder="Username" value="{{ old('username') }}" required autofocus>
        <input class="pill" type="password" name="password" placeholder="Password" required>

        <button type="submit" class="btn btn-primary">Login</button>

        <div class="links d-flex justify-content-between">
          <span>&nbsp;</span>
          {{-- If you add password resets later, link it here --}}
          {{-- <a href="{{ route('password.request') }}">Forgot Password?</a> --}}
        </div>

        <div class="links text-center">
          Sign in as student instead?
          <a href="{{ route('student.login') }}">Student Login</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
