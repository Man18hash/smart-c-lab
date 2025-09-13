<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Admin')</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  @stack('styles')
  <style>
    /* ---------- Design Tokens (Cupertino vibes) ---------- */
    :root{
      --sidebar-w: 280px;
      --bg: #F5F5F7;
      --surface: #FFFFFF;
      --surface-2:#FBFBFD;
      --text: #111114;
      --text-sub:#6C6C70;
      --stroke: #E5E5EA;
      --shadow-soft: 0 12px 30px rgba(0,0,0,.06);
      --shadow-card: 0 10px 26px rgba(0,0,0,.07);
      --blue: #0A84FF; /* iOS blue */
      --blue-pressed:#0066D6;
      --sel: rgba(10,132,255,.14);
    }
    @media (prefers-color-scheme: dark){
      :root{
        --bg:#0B0B0D;
        --surface:#151517;
        --surface-2:#111114;
        --text:#ECECEC;
        --text-sub:#9B9BA1;
        --stroke:#2C2C2E;
        --shadow-soft: 0 10px 24px rgba(0,0,0,.5);
        --shadow-card: 0 12px 28px rgba(0,0,0,.55);
        --sel: rgba(10,132,255,.24);
      }
    }
    html,body{ height:100%; }
    body{
      margin:0; background: var(--bg);
      font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "SF Pro Display",
        Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
      color: var(--text);
      -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;
    }

    /* ---------- Sidebar (glass) ---------- */
    .sidebar{
      width: var(--sidebar-w); height: 100vh; position: fixed; inset:0 auto 0 0; z-index: 1051;
      background: rgba(255,255,255,.55);
      border-right: 1px solid var(--stroke);
      backdrop-filter: saturate(180%) blur(22px);
      -webkit-backdrop-filter: saturate(180%) blur(22px);
      color: var(--text);
      overflow-y: auto; overflow-x: hidden;
      transition: transform .25s ease;
      box-shadow: var(--shadow-soft);
    }
    @media (prefers-color-scheme: dark){
      .sidebar{
        background: rgba(28,28,30,.55);
      }
    }
    .brand{
      display:flex; align-items:center; gap:.9rem; padding:18px 18px 14px;
    }
    .brand-logo{
      width:46px; height:46px; border-radius:12px; overflow:hidden;
      display:grid; place-items:center; background:#0b1220; border:1px solid rgba(255,255,255,.1);
      box-shadow: 0 10px 24px rgba(0,0,0,.2);
      position:relative;
    }
    .brand-logo img{ width:100%; height:100%; object-fit:contain; background:#0b1220; }
    .brand-fallback{ color:#fff; font-size:1.15rem; display:none; }
    .brand-title{ font-weight:700; letter-spacing:.2px; font-size:1.05rem; }
    .brand-sub{ font-size:.78rem; color: var(--text-sub); margin-top:2px; }

    .nav-title{ font-size:.72rem; text-transform:uppercase; letter-spacing:.12em; color: var(--text-sub);
      padding:10px 16px 6px; }
    .nav-link{
      display:flex; align-items:center; gap:.8rem;
      color: var(--text); text-decoration:none; padding:10px 14px; margin:6px 10px;
      border-radius:12px; transition: background .18s ease, transform .06s ease, color .18s ease;
    }
    .nav-link i{ width:22px; text-align:center; font-size:1.06rem; color: var(--text-sub); }
    .nav-link:hover{ background: var(--surface-2); transform: translateY(-1px); }
    .nav-link.active{
      background: var(--sel); color: var(--text);
      border: 1px solid rgba(10,132,255,.28);
    }
    .nav-sep{ height:1px; margin:10px 16px; background: var(--stroke); opacity:.7; border-radius:999px; }

    /* ---------- Content & Titlebar ---------- */
    .content{
      margin-left: var(--sidebar-w);
      min-height: 100vh;
      transition: margin-left .25s ease;
    }
    .titlebar{
      position: sticky; top:0; z-index:100;
      background: linear-gradient(180deg, rgba(255,255,255,.75), rgba(255,255,255,.55));
      border-bottom: 1px solid var(--stroke);
      backdrop-filter: saturate(180%) blur(20px);
      -webkit-backdrop-filter: saturate(180%) blur(20px);
    }
    @media (prefers-color-scheme: dark){
      .titlebar{
        background: linear-gradient(180deg, rgba(20,20,22,.9), rgba(20,20,22,.65));
      }
    }
    .titlebar-inner{
      max-width: 1240px; margin: 0 auto; padding: 18px 22px;
      display:flex; align-items:center; justify-content:space-between; gap:12px;
    }
    .page-title{ margin:0; font-weight:800; letter-spacing:.1px; font-size:1.35rem; }
    .clock-badge{
      background: var(--surface); border:1px solid var(--stroke);
      border-radius: 12px; padding:8px 12px; min-width: 212px; text-align:right;
      font-weight:600; font-size:.95rem; color: var(--text); box-shadow: var(--shadow-soft);
    }

    /* ---------- Main container ---------- */
    .page{
      max-width: 1240px; margin: 22px auto 40px; padding: 0 20px;
    }
    .card-cupertino{
      background: var(--surface); border: 1px solid var(--stroke); border-radius: 16px;
      box-shadow: var(--shadow-card);
    }

    /* ---------- Buttons (macOS blue) ---------- */
    .btn-primary{
      background: var(--blue); border-color: var(--blue);
      border-radius: 12px; font-weight:600;
    }
    .btn-primary:hover{ background: var(--blue-pressed); border-color: var(--blue-pressed); }
    .btn-outline-secondary, .btn-outline-danger, .btn-outline-success{
      border-radius: 12px; font-weight:600;
    }

    /* ---------- Mobile ---------- */
    .sidebar-toggle{
      display:none; position: fixed; top:14px; left:14px; z-index:1102;
      width:42px; height:42px; border-radius:50%; border:1px solid var(--stroke);
      background: var(--surface); box-shadow: var(--shadow-soft);
    }
    .sidebar-toggle i{ color: var(--text); }
    .sidebar-backdrop{ display:none; position:fixed; inset:0; z-index:1050; background: rgba(0,0,0,.28); }
    .sidebar-backdrop.active{ display:block; }

    @media (max-width: 991.98px){
      .sidebar{ transform: translateX(-100%); }
      .sidebar.show{ transform: translateX(0); }
      .content{ margin-left: 0; }
      .sidebar-toggle{ display:block; }
      .titlebar-inner{ padding-left: 68px; }
    }
  </style>
</head>
<body>
  <!-- Mobile hamburger -->
  <button class="sidebar-toggle" id="sidebarToggle" aria-label="Open Sidebar">
    <i class="fa-solid fa-bars"></i>
  </button>
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <div class="brand-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Logo"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';">
        <i class="fa-solid fa-laptop brand-fallback"></i>
      </div>
      <div>
        <div class="brand-title">Smart C Lab</div>
        <div class="brand-sub">Admin Panel</div>
      </div>
    </div>

    <div class="nav-title">Main</div>

    <a class="nav-link {{ request()->routeIs('admin.home') ? 'active' : '' }}" href="{{ route('admin.home') }}">
      <i class="fa-solid fa-gauge"></i> <span>Home</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.borrower') ? 'active' : '' }}" href="{{ route('admin.borrower') }}">
      <i class="fa-solid fa-handshake"></i> <span>Borrower</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.student') ? 'active' : '' }}" href="{{ route('admin.student') }}">
      <i class="fa-solid fa-user-graduate"></i> <span>Student</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.laptop') ? 'active' : '' }}" href="{{ route('admin.laptop') }}">
      <i class="fa-solid fa-laptop"></i> <span>Laptop</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.ip') ? 'active' : '' }}" href="{{ route('admin.ip') }}">
      <i class="fa-solid fa-network-wired"></i> <span>IP</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.history') ? 'active' : '' }}" href="{{ route('admin.history') }}">
      <i class="fa-solid fa-clock-rotate-left"></i> <span>History</span>
    </a>

    <div class="nav-sep"></div>

    <div class="p-3">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger w-100" style="border-radius:12px;">
          <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
        </button>
      </form>
    </div>
  </aside>

  <!-- Content -->
  <div class="content">
    <header class="titlebar">
      <div class="titlebar-inner">
        <h1 class="page-title">@yield('title','Admin')</h1>
        <div class="clock-badge" id="headerClock">--:-- -- --- ---, ----</div>
      </div>
    </header>

    <main class="page">
      @yield('content')
    </main>
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
  <script>
    // Pretty clock (Cupertino style)
    function updateHeaderClock() {
      const el = document.getElementById('headerClock');
      const now = new Date();
      let h = now.getHours();
      const m = String(now.getMinutes()).padStart(2,'0');
      const ampm = h >= 12 ? 'PM' : 'AM';
      h = h % 12 || 12;
      const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      el.textContent = `${h}:${m} ${ampm} ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}`;
    }
    setInterval(updateHeaderClock, 1000); updateHeaderClock();

    // Sidebar toggle on mobile
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('show');
      sidebarBackdrop.classList.toggle('active');
    });
    sidebarBackdrop.addEventListener('click', () => {
      sidebar.classList.remove('show');
      sidebarBackdrop.classList.remove('active');
    });
  </script>
</body>
</html>
