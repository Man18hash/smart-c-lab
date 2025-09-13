<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign In • Borrowing System</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root{
      --fg:#0f172a;
      --stroke:rgba(0,0,0,.08);
      --btn:#0A84FF;
      --btn-press:#0066D6;
    }
    html,body{height:100%}
    body{
      margin:0; color:var(--fg);
      font-family:-apple-system,BlinkMacSystemFont,"SF Pro Text","SF Pro Display",Segoe UI,Roboto,Helvetica,Arial;
      /* Lighter background: white veil over the photo */
      background:
        linear-gradient(rgba(255,255,255,.70), rgba(255,255,255,.70)),
        url('{{ asset('images/bldg.jpg') }}') center/cover no-repeat fixed;
    }

    .wrap{min-height:100%; display:grid; place-items:center; padding:24px}
    .panel{
      width:min(520px, 92vw);
      background:rgba(255,255,255,.85);
      border:1px solid var(--stroke);
      border-radius:20px;
      backdrop-filter: blur(10px) saturate(120%);
      -webkit-backdrop-filter: blur(10px) saturate(120%);
      padding:22px;
    }

    .brand{display:flex; align-items:center; gap:10px; margin-bottom:14px}
    .brand img{width:40px; height:40px; object-fit:contain; border-radius:10px}
    .brand span{font-weight:800}

    .btn-big{
      width:100%;
      padding:14px 16px;
      border-radius:14px;
      font-weight:700;
    }
    .btn-primary{ background:var(--btn); border-color:var(--btn); }
    .btn-primary:hover{ background:var(--btn-press); border-color:var(--btn-press); }
    .btn-plain{
      background:#fff; border:1px solid var(--stroke); color:var(--fg);
    }
    .btn-plain:hover{ background:#f5f5f7; }

    .divider{height:1px; background:var(--stroke); margin:16px 0}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="panel">

      <!-- small brand row -->
      <div class="brand">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
        <span>Borrowing System</span>
      </div>

      <!-- exactly three choices -->
      <div class="d-grid gap-2">
        <a href="{{ route('login') }}" class="btn btn-primary btn-big">Log in as Admin</a>
        <a href="{{ route('student.login') }}" class="btn btn-plain btn-big">Log in as Student</a>
      </div>

      <div class="divider"></div>

      <a href="{{ route('student.register') }}" class="btn btn-plain btn-big">Sign up as Student</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
