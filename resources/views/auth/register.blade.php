<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Registration • Smart C Lab</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --primary: #10b981;
      --primary-dark: #059669;
      --primary-light: #34d399;
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

    .register-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      position: relative;
      overflow: hidden;
    }

    .register-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
      opacity: 0.3;
    }

    .register-card {
      background: var(--bg-secondary);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-lg);
      padding: 40px;
      width: 100%;
      max-width: 800px;
      position: relative;
      z-index: 1;
      max-height: 90vh;
      overflow-y: auto;
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
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }

    .btn-register {
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

    .btn-register:hover {
      background: var(--primary-dark);
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    .btn-register:active {
      transform: translateY(0);
    }

    .btn-back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      background: var(--bg-tertiary);
      color: var(--text-secondary);
      text-decoration: none;
      border-radius: var(--radius-md);
      font-size: 14px;
      font-weight: 600;
      transition: all 0.2s ease;
      border: 1px solid var(--border-light);
    }

    .btn-back:hover {
      background: var(--border-light);
      color: var(--text-primary);
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

    .section-title {
      font-size: 18px;
      font-weight: 600;
      color: var(--text-primary);
      margin: 24px 0 16px 0;
      padding-bottom: 8px;
      border-bottom: 2px solid var(--border-light);
    }

    .section-title:first-child {
      margin-top: 0;
    }

    @media (max-width: 768px) {
      .register-card {
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
  <div class="register-container">
    <div class="register-card">
      <div class="brand">
        <div class="brand-logo">
          <i class="fas fa-user-graduate"></i>
        </div>
        <h1 class="brand-title">Student Registration</h1>
        <p class="brand-subtitle">Create your account to borrow laptops</p>
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

      <form method="POST" action="{{ route('student.register.store') }}">
        @csrf
        
        <h3 class="section-title">
          <i class="fas fa-user me-2"></i>
          Personal Information
        </h3>
        
        <div class="row g-3">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Full Name</label>
              <input name="full_name" type="text" class="form-control" value="{{ old('full_name') }}" required>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="form-label">Grade</label>
              <input name="grade" type="text" class="form-control" value="{{ old('grade') }}" required>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="form-label">Section</label>
              <input name="section" type="text" class="form-control" value="{{ old('section') }}" required>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Address</label>
          <input name="address" type="text" class="form-control" value="{{ old('address') }}" required>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Adviser</label>
              <input name="adviser" type="text" class="form-control" value="{{ old('adviser') }}">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Phone Number</label>
              <input name="phone_number" type="text" class="form-control" value="{{ old('phone_number') }}">
            </div>
          </div>
        </div>

        <h3 class="section-title">
          <i class="fas fa-lock me-2"></i>
          Account Information
        </h3>

        <div class="row g-3">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control" value="{{ old('email') }}" required>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="form-label">Password</label>
              <input name="password" type="password" class="form-control" required>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="form-label">Confirm Password</label>
              <input name="password_confirmation" type="password" class="form-control" required>
            </div>
          </div>
        </div>

        <div class="d-flex gap-3 mt-4">
          <a href="{{ route('login') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Login</span>
          </a>
          <button class="btn-register" type="submit">
            <i class="fas fa-user-plus"></i>
            <span>Create Account</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
