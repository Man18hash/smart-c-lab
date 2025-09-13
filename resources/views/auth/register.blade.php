<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Student Sign Up</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { min-height:100vh; display:flex; align-items:center; justify-content:center; background:#0b0f19; }
    .card { width:100%; max-width:640px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,.25); }
  </style>
</head>
<body>
  <div class="card p-4 bg-white">
    <h4 class="mb-3 text-center">Student Registration</h4>

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('student.register.store') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Full Name</label>
          <input name="full_name" type="text" class="form-control" value="{{ old('full_name') }}" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Grade</label>
          <input name="grade" type="text" class="form-control" value="{{ old('grade') }}" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Section</label>
          <input name="section" type="text" class="form-control" value="{{ old('section') }}" required>
        </div>
        <div class="col-md-12">
          <label class="form-label">Address</label>
          <input name="address" type="text" class="form-control" value="{{ old('address') }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Adviser</label>
          <input name="adviser" type="text" class="form-control" value="{{ old('adviser') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Phone Number</label>
          <input name="phone_number" type="text" class="form-control" value="{{ old('phone_number') }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input name="email" type="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Password</label>
          <input name="password" type="password" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Confirm Password</label>
          <input name="password_confirmation" type="password" class="form-control" required>
        </div>
      </div>

      <div class="d-grid mt-4">
        <button class="btn btn-primary" type="submit">Create Account</button>
      </div>

      <div class="text-center mt-3">
        Already have an account? <a href="{{ route('student.login') }}">Sign in</a>
      </div>
    </form>
  </div>
</body>
</html>
