@extends('layouts.student')
@section('title', 'Borrow a Laptop')

@section('content')
<style>
  :root{
    --card-bg:#ffffff;
    --card-border: #e9eef5;
    --soft:#f1f5fb;
    --ink:#12161f;
    --muted:#6b7280;
    --brand:#2563eb;
    --brand-soft:#e8f0ff;
  }
  .section-card{
    background:var(--card-bg);
    border:1px solid var(--card-border);
    border-radius:16px;
    padding:1.25rem;
    box-shadow:0 6px 18px rgba(18,22,31,0.06);
  }
  .laptop-grid{
    display:grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap:16px;
  }
  .laptop-card{
    border:1px solid var(--card-border);
    border-radius:14px;
    overflow:hidden;
    background:#fff;
    transition:transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    cursor:pointer;
    position:relative;
  }
  .laptop-card:hover{
    transform: translateY(-2px);
    box-shadow:0 10px 24px rgba(18,22,31,0.08);
  }
  .laptop-card input[type="radio"]{
    position:absolute; inset:0; opacity:0; cursor:pointer;
  }
  .laptop-thumb{
    width:100%; aspect-ratio: 16/10; object-fit:cover; background:#f2f4f8;
  }
  .laptop-body{
    padding:.8rem .9rem 1rem .9rem;
  }
  .laptop-name{
    font-weight:700; color:var(--ink); font-size:1rem; line-height:1.2;
  }
  .laptop-meta{
    font-size:.9rem; color:var(--muted);
  }
  .laptop-card.active{
    border-color: var(--brand);
    box-shadow: 0 0 0 3px var(--brand-soft);
  }

  .slider-wrap{
    display:flex; align-items:center; gap:14px;
  }
  .slider-wrap .value-badge{
    min-width:56px; text-align:center; background:var(--brand);
    color:#fff; font-weight:700; border-radius:999px; padding:.35rem .6rem;
  }

  .status-badge { text-transform: capitalize; }
  .req-grid{
    display:grid; gap:16px;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
  }
  .req-card{
    border:1px solid var(--card-border);
    border-radius:14px; overflow:hidden; background:#fff;
    box-shadow:0 6px 16px rgba(18,22,31,0.06);
  }
  .req-thumb{ width:100%; aspect-ratio: 16/10; object-fit:cover; background:#f2f4f8; }
  .req-body{ padding:.9rem 1rem 1.1rem 1rem; }
  .req-title{ font-weight:800; color:var(--ink); }
  .req-meta{ font-size:.92rem; color:var(--muted); }
</style>

{{-- Borrow Form --}}
<div class="section-card">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Borrow a Laptop</h5>
  </div>

  @if(session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger mb-3">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('student.borrow.store') }}" id="borrowForm">
    @csrf

    {{-- Laptop selection --}}
    <div class="mb-3">
      <label class="form-label fw-bold">1) Choose a laptop</label>
      @if($availableLaptops->count())
        <div class="laptop-grid" id="laptopGrid">
          @foreach($availableLaptops as $l)
            @php $img = $l->imageUrl(); @endphp
            <label class="laptop-card" data-id="{{ $l->id }}">
              <input type="radio" name="laptop_id" value="{{ $l->id }}" @checked(old('laptop_id')==$l->id)>
              <img class="laptop-thumb" src="{{ $img }}" alt="Laptop image"
                   loading="lazy"
                   onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">
              <div class="laptop-body">
                <div class="laptop-name">{{ $l->device_name }}</div>
                <div class="laptop-meta">Status: Available</div>
              </div>
            </label>
          @endforeach
        </div>
      @else
        <div class="alert alert-warning">No laptops are currently available.</div>
      @endif
    </div>

    {{-- Hours --}}
    <div class="mb-3">
      <label class="form-label fw-bold">2) Number of hours</label>
      <div class="slider-wrap">
        <input type="range" min="1" max="12" step="1" id="hoursRange" name="duration_hours"
               value="{{ old('duration_hours', 4) }}" class="form-range" style="max-width:340px;">
        <span class="value-badge" id="hoursBadge">{{ old('duration_hours', 4) }}h</span>
      </div>
      <div class="form-text ms-1">Hold & drag. Min 1 hr, max 12 hrs.</div>
    </div>

    {{-- Purpose --}}
    <div class="mb-3">
      <label class="form-label fw-bold">3) Purpose (optional)</label>
      <input type="text" name="purpose" class="form-control" maxlength="255"
             value="{{ old('purpose') }}" placeholder="e.g., Research project, assignment, etc.">
    </div>

    <button class="btn btn-primary" type="submit" id="submitBtn" {{ $availableLaptops->count() ? '' : 'disabled' }}>
      Submit Request
    </button>
  </form>
</div>

{{-- My Requests --}}
<div class="section-card mt-4">
  <h5 class="mb-3">My Borrow Requests</h5>

  @php
    $statusMap = [
      'pending'     => 'warning',
      'approved'    => 'info',
      'declined'    => 'danger',
      'checked_out' => 'primary',
      'returned'    => 'success',
      'overdue'     => 'danger',
    ];
  @endphp

  @if($borrowings->count())
    <div class="req-grid">
      @foreach($borrowings as $b)
        @php
          $badge = $statusMap[$b->status] ?? 'secondary';
          $img   = $b->laptop?->imageUrl() ?? asset('images/no-image.png');
        @endphp
        <div class="req-card">
          <img class="req-thumb" src="{{ $img }}" alt="Laptop"
               loading="lazy"
               onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">
          <div class="req-body">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <div class="req-title">{{ $b->laptop?->device_name ?? 'Laptop' }}</div>
              <span class="badge bg-{{ $badge }} status-badge">{{ str_replace('_',' ',$b->status) }}</span>
            </div>
            <div class="req-meta">
              <div><strong>Requested:</strong> {{ optional($b->requested_at)->format('M d, Y h:i A') ?? '—' }}</div>
              <div><strong>Due:</strong> {{ optional($b->due_at)->format('M d, Y h:i A') ?? '—' }}</div>
              @if($b->purpose)
                <div class="mt-1"><strong>Purpose:</strong> <span class="text-muted">{{ \Illuminate\Support\Str::limit($b->purpose, 90) }}</span></div>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-3">
      @php echo $borrowings->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5'); @endphp
    </div>
  @else
    <div class="text-muted">No requests yet.</div>
  @endif
</div>

@push('scripts')
<script>
  // Toggle card active state when picking a laptop
  const grid = document.getElementById('laptopGrid');
  if (grid){
    grid.addEventListener('change', (e) => {
      if (e.target && e.target.name === 'laptop_id') {
        [...grid.querySelectorAll('.laptop-card')].forEach(c => c.classList.remove('active'));
        e.target.closest('.laptop-card').classList.add('active');
      }
    });
    // Re-apply selection after validation errors
    const checked = grid.querySelector('input[name="laptop_id"]:checked');
    if (checked) checked.closest('.laptop-card').classList.add('active');
  }

  // Hours slider badge
  const hoursRange = document.getElementById('hoursRange');
  const hoursBadge = document.getElementById('hoursBadge');
  if (hoursRange && hoursBadge){
    const sync = () => hoursBadge.textContent = hoursRange.value + 'h';
    hoursRange.addEventListener('input', sync);
    sync();
  }
</script>
@endpush
@endsection
