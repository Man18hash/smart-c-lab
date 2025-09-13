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
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.student.store') }}">
        @csrf
        <div class="modal-header">
          <h6 class="modal-title">Add Student</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Grade</label>
              <input type="text" name="grade" class="form-control" value="{{ old('grade') }}" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Section</label>
              <input type="text" name="section" class="form-control" value="{{ old('section') }}" required>
            </div>

            <div class="col-md-12">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Adviser (optional)</label>
              <input type="text" name="adviser" class="form-control" value="{{ old('adviser') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone (optional)</label>
              <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}">
            </div>

            <div class="col-md-8">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select name="status" class="form-select" required>
                <option value="active" @selected(old('status')==='active')>Active</option>
                <option value="inactive" @selected(old('status')==='inactive')>Inactive</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Student</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
