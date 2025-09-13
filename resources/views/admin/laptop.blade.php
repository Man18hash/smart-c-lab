@extends('layouts.admin')
@section('title', 'Laptop')

@section('content')
<style>
  .status-badge { text-transform: capitalize; }
  .table thead th { white-space: nowrap; }
  .thumb {
      width: 60px; height: 60px; object-fit: cover; border-radius: 8px;
      background:#f2f2f2;
  }
</style>

<div class="bg-white rounded-3 shadow-sm p-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Laptops</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createLaptopModal">
      <i class="fa-solid fa-plus me-1"></i> Add Laptop
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
          <th>Image</th>
          <th>Device Name</th>
          <th>Status</th>
          <th>Notes</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($laptops as $laptop)
          @php $img = $laptop->imageUrl(); @endphp
          <tr>
            <td>
              <img class="thumb"
                   src="{{ $img }}"
                   alt="Laptop"
                   loading="lazy"
                   onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">
            </td>
            <td class="fw-semibold">{{ $laptop->device_name }}</td>
            <td>
              @php
                $map = [
                  'available'   => 'success',
                  'reserved'    => 'warning',
                  'out'         => 'danger',
                  'maintenance' => 'secondary'
                ];
                $cls = $map[$laptop->status] ?? 'secondary';
              @endphp
              <span class="badge bg-{{ $cls }} status-badge">{{ $laptop->status }}</span>
            </td>
            <td style="max-width:420px;">
              <span class="text-muted">{{ \Illuminate\Support\Str::limit($laptop->notes, 120) }}</span>
            </td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editLaptopModal-{{ $laptop->id }}">
                <i class="fa-solid fa-pen-to-square"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteLaptopModal-{{ $laptop->id }}">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editLaptopModal-{{ $laptop->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form method="POST" action="{{ route('admin.laptop.update', $laptop) }}" enctype="multipart/form-data">
                  @csrf
                  @method('PUT')
                  <div class="modal-header">
                    <h6 class="modal-title">Edit Laptop</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">Device Name</label>
                      <input type="text" name="device_name" class="form-control" value="{{ old('device_name', $laptop->device_name) }}" required>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Status</label>
                      <select name="status" class="form-select" required>
                        @foreach (['available','reserved','out','maintenance'] as $s)
                          <option value="{{ $s }}" @selected(old('status', $laptop->status) === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Notes</label>
                      <textarea name="notes" rows="3" class="form-control" placeholder="Optional">{{ old('notes', $laptop->notes) }}</textarea>
                    </div>

                    <div class="mb-2">
                      <label class="form-label d-block">Image</label>
                      @if($laptop->image_path)
                        <div class="d-flex align-items-center gap-3 mb-2">
                          <img src="{{ $img }}" class="thumb" alt="Current"
                               onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">
                          <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="rm-{{ $laptop->id }}" name="remove_image">
                            <label class="form-check-label" for="rm-{{ $laptop->id }}">Remove current image</label>
                          </div>
                        </div>
                      @endif
                      <input type="file" name="image" class="form-control" accept="image/*">
                      <div class="form-text">Max 2MB. Uploading a new image replaces the current one.</div>
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
          <div class="modal fade" id="deleteLaptopModal-{{ $laptop->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <form method="POST" action="{{ route('admin.laptop.destroy', $laptop) }}">
                  @csrf
                  @method('DELETE')
                  <div class="modal-header">
                    <h6 class="modal-title">Delete Laptop</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    Are you sure you want to delete <strong>{{ $laptop->device_name }}</strong>?
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
            <td colspan="5" class="text-muted">No laptops yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($laptops instanceof \Illuminate\Contracts\Pagination\Paginator && $laptops->hasPages())
    <div class="mt-2">
      @php echo $laptops->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5'); @endphp
    </div>
  @endif
</div>

<!-- Create Modal -->
<div class="modal fade" id="createLaptopModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.laptop.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h6 class="modal-title">Add Laptop</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Device Name</label>
            <input type="text" name="device_name" class="form-control" value="{{ old('device_name') }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
              <option value="available" @selected(old('status')==='available')>Available</option>
              <option value="reserved" @selected(old('status')==='reserved')>Reserved</option>
              <option value="out" @selected(old('status')==='out')>Out</option>
              <option value="maintenance" @selected(old('status')==='maintenance')>Maintenance</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Notes</label>
            <textarea name="notes" rows="3" class="form-control" placeholder="Optional">{{ old('notes') }}</textarea>
          </div>

          <div class="mb-2">
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <div class="form-text">Optional. Max 2MB.</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Laptop</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
