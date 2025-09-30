@extends('layouts.admin')
@section('title', 'Students')

@section('content')
<style>
  .table thead th { white-space: nowrap; }
</style>

<div class="bg-white rounded-3 shadow-sm p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Students</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStudentModal">
      <i class="fa-solid fa-plus me-1"></i> Add Student
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Full Name</th>
          <th>Grade</th>
          <th>Section</th>
          <th>Address</th>
          <th>Adviser</th>
          <th>Phone</th>
          <th>Email</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($students as $student)
          <tr>
            <td class="fw-semibold">{{ $student->full_name }}</td>
            <td>{{ $student->grade }}</td>
            <td>{{ $student->section }}</td>
            <td style="max-width:300px;"><span class="text-muted">{{ $student->address }}</span></td>
            <td>{{ $student->adviser ?? '—' }}</td>
            <td>{{ $student->phone_number ?? '—' }}</td>
            <td>{{ $student->email }}</td>
            <td>
              @php
                $cls = $student->status === 'active' ? 'success' : 'secondary';
              @endphp
              <span class="badge bg-{{ $cls }}">{{ ucfirst($student->status) }}</span>
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editStudentModal-{{ $student->id }}">
                <i class="fa-solid fa-pen-to-square"></i>
              </button>
              <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#changePasswordModal-{{ $student->id }}">
                <i class="fa-solid fa-key"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteStudentModal-{{ $student->id }}">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editStudentModal-{{ $student->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <form method="POST" action="{{ route('admin.student.update', $student) }}">
                  @csrf
                  @method('PUT')
                  <div class="modal-header">
                    <h6 class="modal-title">Edit Student</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $student->full_name) }}" required>
                      </div>
                      <div class="col-md-3">
                        <label class="form-label">Grade</label>
                        <input type="text" name="grade" class="form-control" value="{{ old('grade', $student->grade) }}" required>
                      </div>
                      <div class="col-md-3">
                        <label class="form-label">Section</label>
                        <input type="text" name="section" class="form-control" value="{{ old('section', $student->section) }}" required>
                      </div>

                      <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $student->address) }}" required>
                      </div>

                      <div class="col-md-6">
                        <label class="form-label">Adviser (optional)</label>
                        <input type="text" name="adviser" class="form-control" value="{{ old('adviser', $student->adviser) }}">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Phone (optional)</label>
                        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $student->phone_number) }}">
                      </div>

                      <div class="col-md-8">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                          <option value="active" @selected(old('status', $student->status) === 'active')>Active</option>
                          <option value="inactive" @selected(old('status', $student->status) === 'inactive')>Inactive</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Change Password Modal -->
          <div class="modal fade" id="changePasswordModal-{{ $student->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form method="POST" action="{{ route('admin.student.change-password', $student) }}">
                  @csrf
                  @method('PUT')
                  <div class="modal-header">
                    <h6 class="modal-title">Change Password for {{ $student->full_name }}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">New Password</label>
                      <input type="password" name="password" class="form-control" required minlength="8">
                      <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Confirm New Password</label>
                      <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Change Password</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Delete Modal -->
          <div class="modal fade" id="deleteStudentModal-{{ $student->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form method="POST" action="{{ route('admin.student.destroy', $student) }}">
                  @csrf
                  @method('DELETE')
                  <div class="modal-header">
                    <h6 class="modal-title">Delete Student</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    Are you sure you want to delete <strong>{{ $student->full_name }}</strong>?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

        @empty
          <tr>
            <td colspan="9" class="text-muted">No students yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($students instanceof \Illuminate\Contracts\Pagination\Paginator && $students->hasPages())
    <div class="mt-2">
      @php echo $students->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5'); @endphp
    </div>
  @endif
</div>

<!-- Create Modal -->
<div class="modal fade" id="createStudentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.student.store') }}" id="createStudentForm">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">
            <i class="fas fa-user-plus me-2"></i>
            Add New Student
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <!-- Personal Information Section -->
          <div class="mb-4">
            <h6 class="text-primary border-bottom pb-2 mb-3">
              <i class="fas fa-user me-2"></i>
              Personal Information
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fas fa-user me-1 text-muted"></i>
                  Full Name <span class="text-danger">*</span>
                </label>
                <input type="text" name="full_name" class="form-control form-control-lg" 
                       value="{{ old('full_name') }}" required 
                       placeholder="Enter student's full name"
                       autocomplete="name">
                <div class="form-text">Enter the complete name as it appears on official records</div>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold">
                  <i class="fas fa-graduation-cap me-1 text-muted"></i>
                  Grade <span class="text-danger">*</span>
                </label>
                <select name="grade" class="form-select form-select-lg" required>
                  <option value="">Select Grade</option>
                  <option value="Grade 7" @selected(old('grade')==='Grade 7')>Grade 7</option>
                  <option value="Grade 8" @selected(old('grade')==='Grade 8')>Grade 8</option>
                  <option value="Grade 9" @selected(old('grade')==='Grade 9')>Grade 9</option>
                  <option value="Grade 10" @selected(old('grade')==='Grade 10')>Grade 10</option>
                  <option value="Grade 11" @selected(old('grade')==='Grade 11')>Grade 11</option>
                  <option value="Grade 12" @selected(old('grade')==='Grade 12')>Grade 12</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label fw-semibold">
                  <i class="fas fa-users me-1 text-muted"></i>
                  Section <span class="text-danger">*</span>
                </label>
                <input type="text" name="section" class="form-control form-control-lg" 
                       value="{{ old('section') }}" required 
                       placeholder="e.g., A, B, C"
                       autocomplete="off">
              </div>
            </div>
          </div>

          <!-- Contact Information Section -->
          <div class="mb-4">
            <h6 class="text-primary border-bottom pb-2 mb-3">
              <i class="fas fa-address-book me-2"></i>
              Contact Information
            </h6>
            <div class="row g-3">
              <div class="col-md-12">
                <label class="form-label fw-semibold">
                  <i class="fas fa-map-marker-alt me-1 text-muted"></i>
                  Address <span class="text-danger">*</span>
                </label>
                <textarea name="address" class="form-control" rows="2" required 
                          placeholder="Enter complete address">{{ old('address') }}</textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fas fa-envelope me-1 text-muted"></i>
                  Email Address <span class="text-danger">*</span>
                </label>
                <input type="email" name="email" class="form-control form-control-lg" 
                       value="{{ old('email') }}" required 
                       placeholder="student@example.com"
                       autocomplete="email">
                <div class="form-text">This will be used for login credentials</div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fas fa-phone me-1 text-muted"></i>
                  Phone Number
                </label>
                <input type="tel" name="phone_number" class="form-control form-control-lg" 
                       value="{{ old('phone_number') }}" 
                       placeholder="+63 912 345 6789"
                       autocomplete="tel">
              </div>
            </div>
          </div>

          <!-- Academic Information Section -->
          <div class="mb-4">
            <h6 class="text-primary border-bottom pb-2 mb-3">
              <i class="fas fa-chalkboard-teacher me-2"></i>
              Academic Information
            </h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fas fa-user-tie me-1 text-muted"></i>
                  Class Adviser
                </label>
                <input type="text" name="adviser" class="form-control form-control-lg" 
                       value="{{ old('adviser') }}" 
                       placeholder="Enter adviser's name"
                       autocomplete="off">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">
                  <i class="fas fa-toggle-on me-1 text-muted"></i>
                  Account Status <span class="text-danger">*</span>
                </label>
                <select name="status" class="form-select form-select-lg" required>
                  <option value="active" @selected(old('status')==='active')>
                    <i class="fas fa-check-circle text-success"></i> Active
                  </option>
                  <option value="inactive" @selected(old('status')==='inactive')>
                    <i class="fas fa-times-circle text-danger"></i> Inactive
                  </option>
                </select>
                <div class="form-text">Active students can borrow laptops</div>
              </div>
            </div>
          </div>

          <!-- Account Creation Notice -->
          <div class="alert alert-info d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            <div>
              <strong>Note:</strong> A user account will be automatically created with the email address as username. 
              The student will need to set their password on first login.
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary btn-lg" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>
            Cancel
          </button>
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-user-plus me-2"></i>
            Create Student Account
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .modal-xl {
    max-width: 900px;
  }
  
  .form-control-lg, .form-select-lg {
    padding: 0.75rem 1rem;
    font-size: 1rem;
  }
  
  .form-label {
    margin-bottom: 0.5rem;
    color: #374151;
  }
  
  .form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
  }
  
  .text-primary {
    color: #3b82f6 !important;
  }
  
  .border-bottom {
    border-bottom: 2px solid #e5e7eb !important;
  }
  
  .btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
  }
</style>
@endsection
