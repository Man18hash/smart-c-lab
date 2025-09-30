@extends('layouts.student')
@section('title', 'Borrow a Laptop')

@section('content')
<!-- Borrow Form -->
<div class="card-modern mb-4">
  <div class="card-header-modern">
    <h2 class="card-title-modern">Borrow a Laptop</h2>
    <p class="card-subtitle-modern">Select a laptop and set your borrowing duration</p>
  </div>

  <div class="card-body-modern">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
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

    <form method="POST" action="{{ route('student.borrow.store') }}" id="borrowForm">
      @csrf

      <!-- Laptop Selection -->
      <div class="mb-4">
        <label class="form-label" style="font-weight: 600; font-size: 16px; margin-bottom: 16px;">
          <i class="fas fa-laptop me-2"></i>
          Choose a Laptop
        </label>
        
        @if($availableLaptops->count())
          <div class="device-grid" id="laptopGrid">
            @foreach($availableLaptops as $l)
              @php $img = $l->imageUrl(); @endphp
              <label class="device-card" data-id="{{ $l->id }}" style="cursor: pointer;">
                <input type="radio" name="laptop_id" value="{{ $l->id }}" 
                       @checked(old('laptop_id')==$l->id) 
                       style="position: absolute; opacity: 0; pointer-events: none;">
                <img class="device-image" src="{{ $img }}" alt="Laptop image"
                     loading="lazy"
                     onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">
                <div class="device-info">
                  <h3 class="device-name">{{ $l->device_name }}</h3>
                  <div class="device-status">
                    <span class="status-badge status-returned">Available</span>
                  </div>
                  @if($l->notes)
                    <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 0;">
                      {{ \Illuminate\Support\Str::limit($l->notes, 80) }}
                    </p>
                  @endif
                </div>
              </label>
            @endforeach
          </div>
        @else
          <div class="text-center py-5">
            <i class="fas fa-laptop" style="font-size: 64px; color: var(--text-muted); margin-bottom: 16px;"></i>
            <h4 style="color: var(--text-secondary); margin-bottom: 8px;">No Laptops Available</h4>
            <p style="color: var(--text-muted);">All laptops are currently checked out or under maintenance.</p>
          </div>
        @endif
      </div>

      <!-- Duration Selection -->
      <div class="mb-4">
        <label class="form-label" style="font-weight: 600; font-size: 16px; margin-bottom: 16px;">
          <i class="fas fa-clock me-2"></i>
          Set Duration
        </label>
        
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label" style="font-weight: 500;">Hours</label>
            <input type="number" name="duration_h" id="durationH"
                   class="form-control" 
                   style="border-radius: var(--radius-md); padding: 12px 16px; font-weight: 500;"
                   min="0" step="1"
                   value="{{ old('duration_h', 0) }}"
                   placeholder="0">
          </div>
          <div class="col-md-6">
            <label class="form-label" style="font-weight: 500;">Minutes</label>
            <input type="number" name="duration_m" id="durationM"
                   class="form-control"
                   style="border-radius: var(--radius-md); padding: 12px 16px; font-weight: 500;"
                   min="0" max="59" step="1"
                   value="{{ old('duration_m', 30) }}"
                   placeholder="30">
          </div>
        </div>
        
        <div style="font-size: 14px; color: var(--text-muted); margin-top: 8px;">
          <i class="fas fa-info-circle me-1"></i>
          Minimum 1 minute. No maximum limit.
        </div>

        <!-- Live Due Time Preview -->
        <div class="card-modern mt-3" id="durationPreview" style="display: none;">
          <div class="card-body-modern">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h6 style="font-weight: 600; margin: 0; color: var(--text-primary);">Due Time</h6>
                <p style="font-size: 14px; color: var(--text-muted); margin: 4px 0 0 0;">Your laptop will be due at:</p>
              </div>
              <div class="text-end">
                <div id="duePreview" style="font-size: 18px; font-weight: 700; color: var(--primary);">—</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Purpose -->
      <div class="mb-4">
        <label class="form-label" style="font-weight: 600; font-size: 16px; margin-bottom: 16px;">
          <i class="fas fa-edit me-2"></i>
          Purpose (Optional)
        </label>
        <input type="text" name="purpose" class="form-control" maxlength="255"
               style="border-radius: var(--radius-md); padding: 12px 16px;"
               value="{{ old('purpose') }}" 
               placeholder="e.g., Research project, assignment, presentation, etc.">
        <div style="font-size: 14px; color: var(--text-muted); margin-top: 8px;">
          <i class="fas fa-lightbulb me-1"></i>
          Help us understand how you'll be using the laptop
        </div>
      </div>

      <div class="d-flex justify-content-end">
        <button class="btn btn-primary-modern" type="submit" id="submitBtn" 
                style="padding: 12px 24px; font-size: 16px; font-weight: 600;"
                {{ $availableLaptops->count() ? '' : 'disabled' }}>
          <i class="fas fa-paper-plane"></i>
          <span>Submit Request</span>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- My Requests -->
<div class="card-modern">
  <div class="card-header-modern">
    <h2 class="card-title-modern">My Borrow Requests</h2>
    <p class="card-subtitle-modern">Track your current and past borrowing requests</p>
  </div>

  <div class="card-body-modern">
    @if($borrowings->count())
      <div class="device-grid">
        @foreach($borrowings as $b)
          @php
            $badgeMap = [
              'pending'     => 'status-pending',
              'approved'    => 'status-approved',
              'declined'    => 'status-declined',
              'checked_out' => 'status-checked_out',
              'returned'    => 'status-returned',
              'overdue'     => 'status-declined',
            ];
            $badgeClass = $badgeMap[$b->status] ?? 'status-pending';
            $img = $b->laptop?->imageUrl() ?? asset('images/no-image.svg');
          @endphp
          <div class="device-card">
            <img class="device-image" src="{{ $img }}" alt="Laptop"
                 loading="lazy"
                 onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">
            <div class="device-info">
              <h3 class="device-name">{{ $b->laptop?->device_name ?? 'Laptop' }}</h3>
              
              <div class="device-status">
                <span class="status-badge {{ $badgeClass }}">
                  {{ str_replace('_', ' ', $b->status) }}
                </span>
              </div>
              
              <div style="font-size: 14px; color: var(--text-muted); margin-bottom: 12px;">
                <div><strong>Requested:</strong> {{ optional($b->requested_at)->format('M d, Y h:i A') ?? '—' }}</div>
                @if($b->due_at)
                  <div><strong>Due:</strong> {{ $b->due_at->format('M d, Y h:i A') }}</div>
                @endif
              </div>
              
              @if($b->purpose)
                <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 0;">
                  <strong>Purpose:</strong> {{ \Illuminate\Support\Str::limit($b->purpose, 100) }}
                </p>
              @endif
            </div>
          </div>
        @endforeach
      </div>

      @if($borrowings instanceof \Illuminate\Contracts\Pagination\Paginator && $borrowings->hasPages())
        <div class="mt-4">
          {{ $borrowings->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
      @endif
    @else
      <div class="text-center py-5">
        <i class="fas fa-inbox" style="font-size: 64px; color: var(--text-muted); margin-bottom: 16px;"></i>
        <h4 style="color: var(--text-secondary); margin-bottom: 8px;">No Requests Yet</h4>
        <p style="color: var(--text-muted);">You haven't made any borrowing requests yet.</p>
      </div>
    @endif
  </div>
</div>

@push('scripts')
<script>
  // Toggle card active state when picking a laptop
  const grid = document.getElementById('laptopGrid');
  if (grid){
    grid.addEventListener('change', (e) => {
      if (e.target && e.target.name === 'laptop_id') {
        [...grid.querySelectorAll('.device-card')].forEach(c => c.classList.remove('active'));
        e.target.closest('.device-card').classList.add('active');
      }
    });
    
    // Re-apply selection after validation errors
    const checked = grid.querySelector('input[name="laptop_id"]:checked');
    if (checked) checked.closest('.device-card').classList.add('active');
  }

  // Live due-time preview
  const H = document.getElementById('durationH');
  const M = document.getElementById('durationM');
  const previewWrap = document.getElementById('durationPreview');
  const duePreview = document.getElementById('duePreview');

  function fmtDate(dt){
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    let h = dt.getHours();
    const m = String(dt.getMinutes()).padStart(2,'0');
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return `${months[dt.getMonth()]} ${dt.getDate()}, ${dt.getFullYear()} ${h}:${m} ${ampm}`;
  }

  function updatePreview(){
    const hh = parseInt(H.value || '0', 10);
    const mm = parseInt(M.value || '0', 10);
    const total = (hh * 60) + mm;

    if (total >= 1){
      const now = new Date();
      const due = new Date(now.getTime() + total*60000);
      duePreview.textContent = fmtDate(due);
      previewWrap.style.display = 'block';
    } else {
      previewWrap.style.display = 'none';
    }
  }

  H.addEventListener('input', updatePreview);
  M.addEventListener('input', updatePreview);
  updatePreview();

  // Add active state styling for selected laptop cards
  const style = document.createElement('style');
  style.textContent = `
    .device-card.active {
      border-color: var(--primary) !important;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
      transform: translateY(-2px);
    }
  `;
  document.head.appendChild(style);
</script>
@endpush
@endsection
