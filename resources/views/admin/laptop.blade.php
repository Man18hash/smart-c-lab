@extends('layouts.admin')
@section('title', 'Laptop Inventory')

@section('content')
<div class="card-modern">
  <div class="card-header-modern">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h2 class="card-title-modern">Laptop Inventory</h2>
        <p class="card-subtitle-modern">Manage and track all laptops in the system</p>
      </div>
      <button class="btn btn-primary-modern" data-bs-toggle="modal" data-bs-target="#createLaptopModal">
        <i class="fas fa-plus"></i>
        <span>Add New Laptop</span>
      </button>
    </div>
  </div>

  <div class="card-body-modern">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <ul class="mb-0">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <!-- Laptop Grid View -->
    <div class="device-grid">
      @forelse($laptops as $laptop)
        @php 
          $img = $laptop->imageUrl();
          $statusClass = 'status-' . $laptop->status;
        @endphp
        <div class="device-card">
          <img class="device-image" 
               src="{{ $img }}" 
               alt="{{ $laptop->device_name }}"
               loading="lazy"
               onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';"
               style="cursor: pointer;"
               data-bs-toggle="modal" 
               data-bs-target="#imageModal-{{ $laptop->id }}">
          
          <div class="device-info">
            <h3 class="device-name">{{ $laptop->device_name }}</h3>
            
            <div class="device-status">
              <span class="status-badge {{ $statusClass }}">{{ ucfirst($laptop->status) }}</span>
            </div>
            
            @if($laptop->notes)
              <p class="text-muted mb-3" style="font-size: 14px;">
                {{ \Illuminate\Support\Str::limit($laptop->notes, 100) }}
              </p>
            @endif
            
            <div class="device-actions">
              <button class="btn btn-secondary-modern btn-sm" 
                      data-bs-toggle="modal" 
                      data-bs-target="#editLaptopModal-{{ $laptop->id }}">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
              </button>
              <button class="btn btn-danger-modern btn-sm" 
                      data-bs-toggle="modal" 
                      data-bs-target="#deleteLaptopModal-{{ $laptop->id }}">
                <i class="fas fa-trash"></i>
                <span>Delete</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editLaptopModal-{{ $laptop->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: var(--radius-xl); border: none; box-shadow: var(--shadow-xl);">
              <form method="POST" action="{{ route('admin.laptop.update', $laptop) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header" style="border-bottom: 1px solid var(--border-light);">
                  <h5 class="modal-title" style="font-weight: 600;">Edit Laptop</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 24px;">
                  <div class="mb-3">
                    <label class="form-label" style="font-weight: 500;">Device Name</label>
                    <input type="text" name="device_name" class="form-control" 
                           value="{{ old('device_name', $laptop->device_name) }}" 
                           required style="border-radius: var(--radius-md);">
                  </div>

                  <div class="mb-3">
                    <label class="form-label" style="font-weight: 500;">Status</label>
                    <select name="status" class="form-select" required style="border-radius: var(--radius-md);">
                      @foreach (['available','reserved','out','maintenance'] as $s)
                        <option value="{{ $s }}" @selected(old('status', $laptop->status) === $s)>
                          {{ ucfirst($s) }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  <div class="mb-3">
                    <label class="form-label" style="font-weight: 500;">Notes</label>
                    <textarea name="notes" rows="3" class="form-control" 
                              placeholder="Optional notes about this laptop"
                              style="border-radius: var(--radius-md);">{{ old('notes', $laptop->notes) }}</textarea>
                  </div>

                  <div class="mb-3">
                    <label class="form-label" style="font-weight: 500;">Image</label>
                    @if($laptop->image_path)
                      <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $img }}" 
                             class="rounded" 
                             style="width: 80px; height: 80px; object-fit: cover;"
                             alt="Current"
                             onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" value="1" 
                                 id="rm-{{ $laptop->id }}" name="remove_image">
                          <label class="form-check-label" for="rm-{{ $laptop->id }}">
                            Remove current image
                          </label>
                        </div>
                      </div>
                    @endif
                    <input type="file" name="image" class="form-control" accept="image/*"
                           style="border-radius: var(--radius-md);">
                    <div class="form-text">Max 2MB. Uploading a new image replaces the current one.</div>
                  </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-light);">
                  <button type="button" class="btn btn-secondary-modern" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary-modern">Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteLaptopModal-{{ $laptop->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: var(--radius-xl); border: none; box-shadow: var(--shadow-xl);">
              <form method="POST" action="{{ route('admin.laptop.destroy', $laptop) }}">
                @csrf
                @method('DELETE')
                <div class="modal-header" style="border-bottom: 1px solid var(--border-light);">
                  <h5 class="modal-title" style="font-weight: 600; color: var(--danger);">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Delete Laptop
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 24px;">
                  <p class="mb-0">Are you sure you want to delete <strong>{{ $laptop->device_name }}</strong>?</p>
                  <p class="text-muted mt-2 mb-0">This action cannot be undone.</p>
                  
                  @if($laptop->borrowings()->count() > 0)
                    <div class="alert alert-warning mt-3">
                      <i class="fas fa-exclamation-triangle me-2"></i>
                      <strong>Warning:</strong> This laptop has {{ $laptop->borrowings()->count() }} borrowing record(s). 
                      Deleting it will also remove all associated borrowing history.
                    </div>
                  @endif
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-light);">
                  <button type="button" class="btn btn-secondary-modern" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-danger-modern">Delete Laptop</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Image Modal -->
        <div class="modal fade" id="imageModal-{{ $laptop->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: var(--radius-xl); border: none; box-shadow: var(--shadow-xl);">
              <div class="modal-header" style="border-bottom: 1px solid var(--border-light);">
                <h5 class="modal-title" style="font-weight: 600;">{{ $laptop->device_name }} - Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body text-center" style="padding: 24px;">
                <img src="{{ $img }}" 
                     alt="{{ $laptop->device_name }}" 
                     class="img-fluid rounded"
                     style="max-height: 70vh; border-radius: var(--radius-lg);"
                     onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">
              </div>
            </div>
          </div>
        </div>

      @empty
        <div class="col-12">
          <div class="text-center py-5">
            <i class="fas fa-laptop" style="font-size: 64px; color: var(--text-muted); margin-bottom: 16px;"></i>
            <h4 style="color: var(--text-secondary); margin-bottom: 8px;">No Laptops Found</h4>
            <p style="color: var(--text-muted);">Get started by adding your first laptop to the inventory.</p>
            <button class="btn btn-primary-modern mt-3" data-bs-toggle="modal" data-bs-target="#createLaptopModal">
              <i class="fas fa-plus"></i>
              <span>Add First Laptop</span>
            </button>
          </div>
        </div>
      @endforelse
    </div>

    @if($laptops instanceof \Illuminate\Contracts\Pagination\Paginator && $laptops->hasPages())
      <div class="mt-4">
        {{ $laptops->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createLaptopModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: var(--radius-xl); border: none; box-shadow: var(--shadow-xl);">
      <form method="POST" action="{{ route('admin.laptop.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header" style="border-bottom: 1px solid var(--border-light);">
          <h5 class="modal-title" style="font-weight: 600;">Add New Laptop</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="padding: 24px;">
          <div class="mb-3">
            <label class="form-label" style="font-weight: 500;">Device Name</label>
            <input type="text" name="device_name" class="form-control" 
                   value="{{ old('device_name') }}" 
                   required style="border-radius: var(--radius-md);"
                   placeholder="e.g., Dell Latitude 5520">
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight: 500;">Status</label>
            <select name="status" class="form-select" required style="border-radius: var(--radius-md);">
              <option value="available" @selected(old('status')==='available')>Available</option>
              <option value="reserved" @selected(old('status')==='reserved')>Reserved</option>
              <option value="out" @selected(old('status')==='out')>Out</option>
              <option value="maintenance" @selected(old('status')==='maintenance')>Maintenance</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight: 500;">Notes</label>
            <textarea name="notes" rows="3" class="form-control" 
                      placeholder="Optional notes about this laptop"
                      style="border-radius: var(--radius-md);">{{ old('notes') }}</textarea>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight: 500;">Image</label>
            <input type="file" name="image" class="form-control" accept="image/*"
                   style="border-radius: var(--radius-md);">
            <div class="form-text">Optional. Max 2MB. JPG, PNG, or WebP formats.</div>
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid var(--border-light);">
          <button type="button" class="btn btn-secondary-modern" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary-modern">Add Laptop</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
