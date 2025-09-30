<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Smart C Lab - Laptop Management System')</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  @stack('styles')
  <style>
    /* ---------- Modern Laptop Management System Design ---------- */
    :root{
      --sidebar-w: 280px;
      --primary: #2563eb;
      --primary-dark: #1d4ed8;
      --primary-light: #3b82f6;
      --secondary: #64748b;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --info: #06b6d4;
      
      --bg-primary: #f8fafc;
      --bg-secondary: #ffffff;
      --bg-tertiary: #f1f5f9;
      
      --text-primary: #0f172a;
      --text-secondary: #475569;
      --text-muted: #94a3b8;
      
      --border-light: #e2e8f0;
      --border-medium: #cbd5e1;
      
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
      --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
      
      --radius-sm: 6px;
      --radius-md: 8px;
      --radius-lg: 12px;
      --radius-xl: 16px;
    }

    * {
      box-sizing: border-box;
    }

    html, body { 
      height: 100%; 
      margin: 0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--bg-primary);
      color: var(--text-primary);
      line-height: 1.6;
    }

    /* ---------- Sidebar ---------- */
    .sidebar{
      width: var(--sidebar-w); 
      height: 100vh; 
      position: fixed; 
      top: 0; left: 0; 
      z-index: 1000;
      background: var(--bg-secondary);
      border-right: 1px solid var(--border-light);
      box-shadow: var(--shadow-lg);
      overflow-y: auto;
      transition: transform 0.3s ease;
    }

    .brand{
      padding: 24px 20px;
      border-bottom: 1px solid var(--border-light);
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
    }

    .brand-logo{
      width: 48px; 
      height: 48px; 
      border-radius: var(--radius-lg);
      background: rgba(255,255,255,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
      backdrop-filter: blur(10px);
    }

    .brand-logo i{
      font-size: 24px;
      color: white;
    }

    .brand-title{
      font-size: 20px;
      font-weight: 700;
      margin: 0;
      letter-spacing: -0.025em;
    }

    .brand-sub{
      font-size: 14px;
      opacity: 0.8;
      margin: 4px 0 0 0;
      font-weight: 400;
    }

    .nav-section{
      padding: 20px 0;
    }

    .nav-title{
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-muted);
      padding: 0 20px 12px;
      margin: 0;
    }

    .nav-link{
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 20px;
      color: var(--text-secondary);
      text-decoration: none;
      font-weight: 500;
      font-size: 14px;
      transition: all 0.2s ease;
      border-left: 3px solid transparent;
    }

    .nav-link:hover{
      background: var(--bg-tertiary);
      color: var(--text-primary);
      border-left-color: var(--primary-light);
    }

    .nav-link.active{
      background: rgba(37, 99, 235, 0.1);
      color: var(--primary);
      border-left-color: var(--primary);
    }

    .nav-link i{
      width: 20px;
      text-align: center;
      font-size: 16px;
    }

    .nav-divider{
      height: 1px;
      background: var(--border-light);
      margin: 20px 0;
    }

    /* ---------- Content Area ---------- */
    .content{
      margin-left: var(--sidebar-w);
      min-height: 100vh;
      transition: margin-left 0.3s ease;
    }

    .header{
      background: var(--bg-secondary);
      border-bottom: 1px solid var(--border-light);
      padding: 20px 32px;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: var(--shadow-sm);
    }

    .header-content{
      display: flex;
      align-items: center;
      justify-content: space-between;
      max-width: 1400px;
      margin: 0 auto;
    }

    .page-title{
      font-size: 28px;
      font-weight: 700;
      margin: 0;
      color: var(--text-primary);
      letter-spacing: -0.025em;
    }

    .header-actions{
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .clock-widget{
      background: var(--bg-tertiary);
      border: 1px solid var(--border-light);
      border-radius: var(--radius-lg);
      padding: 12px 16px;
      font-weight: 600;
      font-size: 14px;
      color: var(--text-secondary);
      min-width: 200px;
      text-align: center;
    }

    /* ---------- Main Content ---------- */
    .main-content{
      padding: 32px;
      max-width: 1400px;
      margin: 0 auto;
    }

    /* ---------- Cards ---------- */
    .card-modern{
      background: var(--bg-secondary);
      border: 1px solid var(--border-light);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-sm);
      overflow: hidden;
      transition: all 0.2s ease;
    }

    .card-modern:hover{
      box-shadow: var(--shadow-md);
    }

    .card-header-modern{
      padding: 24px 24px 0;
      border-bottom: none;
      background: transparent;
    }

    .card-body-modern{
      padding: 24px;
    }

    .card-title-modern{
      font-size: 18px;
      font-weight: 600;
      margin: 0 0 8px 0;
      color: var(--text-primary);
    }

    .card-subtitle-modern{
      font-size: 14px;
      color: var(--text-muted);
      margin: 0 0 20px 0;
    }

    /* ---------- Buttons ---------- */
    .btn-modern{
      border-radius: var(--radius-md);
      font-weight: 500;
      font-size: 14px;
      padding: 10px 16px;
      border: none;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-primary-modern{
      background: var(--primary);
      color: white;
    }

    .btn-primary-modern:hover{
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    .btn-secondary-modern{
      background: var(--bg-tertiary);
      color: var(--text-secondary);
      border: 1px solid var(--border-medium);
    }

    .btn-secondary-modern:hover{
      background: var(--border-light);
      color: var(--text-primary);
    }

    .btn-success-modern{
      background: var(--success);
      color: white;
    }

    .btn-warning-modern{
      background: var(--warning);
      color: white;
    }

    .btn-danger-modern{
      background: var(--danger);
      color: white;
    }

    /* ---------- Tables ---------- */
    .table-modern{
      margin: 0;
    }

    .table-modern thead th{
      background: var(--bg-tertiary);
      border: none;
      padding: 16px 20px;
      font-weight: 600;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-secondary);
    }

    .table-modern tbody td{
      padding: 16px 20px;
      border-top: 1px solid var(--border-light);
      vertical-align: middle;
    }

    .table-modern tbody tr:hover{
      background: var(--bg-tertiary);
    }

    /* ---------- Status Badges ---------- */
    .status-badge{
      padding: 6px 12px;
      border-radius: var(--radius-sm);
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .status-available{
      background: rgba(16, 185, 129, 0.1);
      color: var(--success);
    }

    .status-reserved{
      background: rgba(245, 158, 11, 0.1);
      color: var(--warning);
    }

    .status-out{
      background: rgba(239, 68, 68, 0.1);
      color: var(--danger);
    }

    .status-maintenance{
      background: rgba(100, 116, 139, 0.1);
      color: var(--secondary);
    }

    /* ---------- Device Cards ---------- */
    .device-grid{
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 24px;
      margin-top: 24px;
    }

    .device-card{
      background: var(--bg-secondary);
      border: 1px solid var(--border-light);
      border-radius: var(--radius-xl);
      overflow: hidden;
      transition: all 0.2s ease;
      box-shadow: var(--shadow-sm);
    }

    .device-card:hover{
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .device-image{
      width: 100%;
      height: 200px;
      object-fit: cover;
      background: var(--bg-tertiary);
    }

    .device-info{
      padding: 20px;
    }

    .device-name{
      font-size: 18px;
      font-weight: 600;
      margin: 0 0 8px 0;
      color: var(--text-primary);
    }

    .device-status{
      margin-bottom: 16px;
    }

    .device-actions{
      display: flex;
      gap: 8px;
    }

    /* ---------- Mobile Responsive ---------- */
    .sidebar-toggle{
      display: none;
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 1100;
      width: 44px;
      height: 44px;
      border-radius: var(--radius-md);
      border: 1px solid var(--border-medium);
      background: var(--bg-secondary);
      box-shadow: var(--shadow-md);
      align-items: center;
      justify-content: center;
    }

    .sidebar-backdrop{
      display: none;
      position: fixed;
      inset: 0;
      z-index: 999;
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(4px);
    }

    @media (max-width: 1024px) {
      .sidebar{
        transform: translateX(-100%);
      }
      
      .sidebar.show{
        transform: translateX(0);
      }
      
      .content{
        margin-left: 0;
      }
      
      .sidebar-toggle{
        display: flex;
      }
      
      .header{
        padding-left: 80px;
      }
      
      .main-content{
        padding: 20px;
      }
    }

    @media (max-width: 768px) {
      .device-grid{
        grid-template-columns: 1fr;
      }
      
      .header-content{
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
      }
      
      .page-title{
        font-size: 24px;
      }
    }
  </style>
</head>
<body>
  <!-- Mobile Sidebar Toggle -->
  <button class="sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
  </button>
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <div class="brand-logo">
        <i class="fas fa-laptop"></i>
      </div>
      <h1 class="brand-title">Smart C Lab</h1>
      <p class="brand-sub">Laptop Management System</p>
    </div>

    <div class="nav-section">
      <h6 class="nav-title">Main Navigation</h6>
      
      <a class="nav-link {{ request()->routeIs('admin.home') ? 'active' : '' }}" href="{{ route('admin.home') }}">
        <i class="fas fa-chart-line"></i>
        <span>Dashboard</span>
      </a>
      
      <a class="nav-link {{ request()->routeIs('admin.borrower') ? 'active' : '' }}" href="{{ route('admin.borrower') }}">
        <i class="fas fa-handshake"></i>
        <span>Borrower Requests</span>
      </a>
      
      <a class="nav-link {{ request()->routeIs('admin.student') ? 'active' : '' }}" href="{{ route('admin.student') }}">
        <i class="fas fa-user-graduate"></i>
        <span>Student Management</span>
      </a>
      
      <a class="nav-link {{ request()->routeIs('admin.laptop') ? 'active' : '' }}" href="{{ route('admin.laptop') }}">
        <i class="fas fa-laptop"></i>
        <span>Laptop Inventory</span>
      </a>
      
      <a class="nav-link {{ request()->routeIs('admin.ip') ? 'active' : '' }}" href="{{ route('admin.ip') }}">
        <i class="fas fa-network-wired"></i>
        <span>IP Assets</span>
      </a>
      
      <a class="nav-link {{ request()->routeIs('admin.history') ? 'active' : '' }}" href="{{ route('admin.history') }}">
        <i class="fas fa-history"></i>
        <span>Transaction History</span>
      </a>
    </div>

    <div class="nav-divider"></div>

    <div style="padding: 20px;">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger-modern w-100">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </button>
      </form>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="content">
    <header class="header">
      <div class="header-content">
        <h1 class="page-title">@yield('title', 'Dashboard')</h1>
        <div class="header-actions">
          <div class="clock-widget" id="headerClock">
            <i class="fas fa-clock me-2"></i>
            <span id="clockTime">--:-- --</span>
          </div>
        </div>
      </div>
    </header>

    <main class="main-content">
      @yield('content')
    </main>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
  
  <script>
    // Real-time clock
    function updateClock() {
      const now = new Date();
      const time = now.toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit',
        hour12: true 
      });
      const date = now.toLocaleDateString('en-US', { 
        weekday: 'short',
        month: 'short', 
        day: 'numeric' 
      });
      
      document.getElementById('clockTime').textContent = `${time} • ${date}`;
    }
    
    setInterval(updateClock, 1000);
    updateClock();

    // Sidebar toggle for mobile
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

    // Close sidebar when clicking on nav links (mobile)
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth <= 1024) {
          sidebar.classList.remove('show');
          sidebarBackdrop.classList.remove('active');
        }
      });
    });
  </script>
</body>
</html>
