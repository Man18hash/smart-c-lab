<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login • Smart C Lab</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --primary: #1e40af;
      --primary-dark: #1e3a8a;
      --primary-light: #3b82f6;
      --secondary: #64748b;
      --success: #059669;
      --warning: #d97706;
      --danger: #dc2626;
      --info: #0891b2;
      
      --bg-primary: #f8fafc;
      --bg-secondary: #ffffff;
      --bg-tertiary: #f1f5f9;
      
      --text-primary: #0f172a;
      --text-secondary: #334155;
      --text-muted: #64748b;
      
      --border-light: #e2e8f0;
      --border-medium: #cbd5e1;
      
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
      
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

    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      position: relative;
      overflow: hidden;
    }

    .login-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
      opacity: 0.3;
    }

    .login-card {
      background: var(--bg-secondary);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-lg);
      padding: 40px;
      width: 100%;
      max-width: 420px;
      position: relative;
      z-index: 1;
    }

    .brand {
      text-align: center;
      margin-bottom: 32px;
    }

    .brand-logo {
      width: 64px;
      height: 64px;
      border-radius: var(--radius-lg);
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px;
      box-shadow: var(--shadow-md);
    }

    .brand-logo i {
      font-size: 28px;
      color: white;
    }

    .brand-title {
      font-size: 24px;
      font-weight: 700;
      margin: 0 0 8px 0;
      color: var(--text-primary);
      letter-spacing: -0.025em;
    }

    .brand-subtitle {
      font-size: 14px;
      color: var(--text-muted);
      margin: 0;
    }

    .login-form {
      margin-bottom: 24px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 8px;
      font-size: 14px;
    }

    .form-control {
      width: 100%;
      padding: 12px 16px;
      border: 1px solid var(--border-light);
      border-radius: var(--radius-md);
      font-size: 14px;
      font-weight: 500;
      background: var(--bg-secondary);
      color: var(--text-primary);
      transition: all 0.2s ease;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .btn-login {
      width: 100%;
      padding: 12px 24px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: var(--radius-md);
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-login:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    .divider {
      display: flex;
      align-items: center;
      margin: 24px 0;
      color: var(--text-muted);
      font-size: 14px;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--border-light);
    }

    .divider span {
      padding: 0 16px;
      background: var(--bg-secondary);
    }

    .signup-section {
      text-align: center;
      padding: 20px;
      background: var(--bg-tertiary);
      border-radius: var(--radius-lg);
      margin-top: 24px;
    }

    .signup-title {
      font-size: 16px;
      font-weight: 600;
      color: var(--text-primary);
      margin: 0 0 8px 0;
    }

    .signup-subtitle {
      font-size: 14px;
      color: var(--text-muted);
      margin: 0 0 16px 0;
    }

    .btn-signup {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      background: var(--success);
      color: white;
      text-decoration: none;
      border-radius: var(--radius-md);
      font-size: 14px;
      font-weight: 600;
      transition: all 0.2s ease;
    }

    .btn-signup:hover {
      background: #059669;
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
      color: white;
      text-decoration: none;
    }

    .error-alert {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.2);
      color: var(--danger);
      padding: 12px 16px;
      border-radius: var(--radius-md);
      font-size: 14px;
      margin-bottom: 20px;
    }

    .error-alert ul {
      margin: 0;
      padding-left: 20px;
    }

    .error-alert li {
      margin-bottom: 4px;
    }

    .error-alert li:last-child {
      margin-bottom: 0;
    }

    @media (max-width: 480px) {
      .login-card {
        padding: 24px;
        margin: 10px;
      }
      
      .brand-title {
        font-size: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-card">
      <div class="brand">
        <div class="brand-logo">
          <i class="fas fa-laptop"></i>
        </div>
        <h1 class="brand-title">Smart C Lab</h1>
        <p class="brand-subtitle">Laptop Management System</p>
      </div>

      @if($errors->any())
        <div class="error-alert">
          <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('login.attempt') }}" class="login-form">
        @csrf
        
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-user me-2"></i>
            Username or Email
          </label>
          <input type="text" 
                 name="username" 
                 class="form-control" 
                 placeholder="Enter your username or email"
                 value="{{ old('username') }}" 
                 required 
                 autofocus>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-lock me-2"></i>
            Password
          </label>
          <input type="password" 
                 name="password" 
                 class="form-control" 
                 placeholder="Enter your password"
                 required>
        </div>

        <button type="submit" class="btn-login">
          <i class="fas fa-sign-in-alt"></i>
          <span>Sign In</span>
        </button>
      </form>

      <div class="divider">
        <span>New Student?</span>
      </div>

      <div class="signup-section">
        <h3 class="signup-title">Student Registration</h3>
        <p class="signup-subtitle">Create your student account to borrow laptops</p>
        <a href="{{ route('student.register') }}" class="btn-signup">
          <i class="fas fa-user-plus"></i>
          <span>Sign Up as Student</span>
        </a>
      </div>
    </div>
  </div>
</body>
</html>
