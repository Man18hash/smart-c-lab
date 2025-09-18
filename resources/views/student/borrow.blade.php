@extends('layouts.student')
@section('title', 'Borrow a Laptop')

@section('content')
<style>
  :root{
    --card-bg:#ffffff;
    --card-border:#e9eef5;
    --soft:#f6f8fc;
    --ink:#111114;
    --muted:#6b7280;
    --brand:#0A84FF;
    --brand-soft:rgba(10,132,255,.14);
  }
  @media (prefers-color-scheme:dark){
    :root{ --card-bg:#151517; --card-border:#2C2C2E; --soft:#111114; --ink:#ECECEC; --muted:#9b9ba1; }
  }

  .section-card{
    background:var(--card-bg);
    border:1px solid var(--card-border);
    border-radius:16px;
    padding:1.25rem;
    box-shadow:0 8px 24px rgba(0,0,0,.06);
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
    background:var(--card-bg);
    transition:transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    cursor:pointer;
    position:relative;
  }
  .laptop-card:hover{ transform: translateY(-2px); box-shadow:0 12px 28px rgba(0,0,0,.10); }
  .laptop-card input[type="radio"]{ position:absolute; inset:0; opacity:0; cursor:pointer; }
  .laptop-thumb{ width:100%; aspect-ratio: 16/10; object-fit:cover; background:#f2f4f8; }
  .laptop-body{ padding:.8rem .9rem 1rem .9rem; }
  .laptop-name{ font-weight:800; color:var(--ink); font-size:1rem; line-height:1.2; }
  .laptop-meta{ font-size:.9rem; color:var(--muted); }
  .laptop-card.active{ border-color: var(--brand); box-shadow: 0 0 0 3px var(--brand-soft); }

  .duration-wrap{
    display:grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap:12px;
  }
  .pill-input{
    border-radius:12px; border:1px solid var(--card-border); background:var(--soft);
    padding:10px 12px; font-weight:700; color:var(--ink);
  }
  .hint{ color:var(--muted); }

  .preview{
    border:1px dashed var(--card-border);
    border-radius:12px; padding:10px 12px; background:transparent; color:var(--ink);
    display:flex; align-items:center; justify-content:space-between; gap:10px;
  }
  .preview .label{ font-weight:800; }
  .preview .value{ font-variant-numeric: tabular-nums; font-weight:800; }

  .status-badge { text-transform: capitalize; }
  .req-grid{ display:grid; gap:16px; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); }
  .req-card{
    border:1px solid var(--card-border);
    border-radius:14px; overflow:hidden; background:var(--card-bg);
    box-shadow:0 6px 16px rgba(0,0,0,.06);
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

    {{-- Duration: Hours + Minutes (any; min total 1 minute) --}}
    <div class="mb-3">
      <label class="form-label fw-bold">2) Set duration</label>
      <div class="duration-wrap">
        <div>
          <label class="form-label">Hours</label>
          <input type="number" name="duration_h" id="durationH"
                 class="form-control pill-input"
                 min="0" step="1"
                 value="{{ old('duration_h', 0) }}">
        </div>
        <div>
          <label class="form-label">Minutes</label>
          <input type="number" name="duration_m" id="durationM"
                 class="form-control pill-input"
                 min="0" max="59" step="1"
                 value="{{ old('duration_m', 30) }}">
        </div>
      </div>
      <div class="hint mt-2">
        Minimum 1 minute. No maximum hours. (Minutes field supports 0–59.)
      </div>

      {{-- Live due-time preview --}}
      <div class="preview mt-2" id="durationPreview" hidden>
        <div class="label">Will be due at</div>
        <div class="value" id="duePreview">—</div>
      </div>
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

  // Live due-time preview
  const H = document.getElementById('durationH');
  const M = document.getElementById('durationM');
  const previewWrap = document.getElementById('durationPreview');
  const duePreview  = document.getElementById('duePreview');

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
      previewWrap.hidden = false;
    } else {
      previewWrap.hidden = true;
    }
  }

  H.addEventListener('input', updatePreview);
  M.addEventListener('input', updatePreview);
  updatePreview();
</script>
@endpush
@endsection
