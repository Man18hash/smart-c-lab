@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<style>
  .cards { display:grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap:14px; margin-bottom:18px; }
  .card-kpi { background: rgba(255,255,255,.8); border:1px solid #eef1f6; border-radius:14px; padding:16px; display:flex; align-items:center; gap:12px; text-decoration:none; color:inherit; box-shadow: 0 6px 18px rgba(15,23,42,.06); }
  .kpi-icon { width:42px; height:42px; border-radius:12px; display:grid; place-items:center; color:#fff; }
  .kpi-blue  { background: linear-gradient(135deg,#0a84ff,#4f46e5);}
  .kpi-green { background: linear-gradient(135deg,#34c759,#16a34a);}
  .kpi-amber { background: linear-gradient(135deg,#f59e0b,#d97706);}
  .kpi-violet{ background: linear-gradient(135deg,#8b5cf6,#6d28d9);}
  .kpi-num { font-weight:800; font-size:1.35rem; line-height:1; }
  .kpi-label { color:#6b7280; font-size:.9rem; margin-top:2px; }

  .panel { background: rgba(255,255,255,.85); border:1px solid #eef1f6; border-radius:16px; padding:16px; box-shadow: 0 8px 24px rgba(15,23,42,.06); }
  .panel h6 { margin:0 0 10px; font-weight:800; }
  .item { display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px dashed #edf1f6; }
  .item:last-child { border-bottom:0; }
  .thumb { width:60px; height:40px; object-fit:cover; border-radius:10px; background:#f2f4f8; }
  .meta { font-size:.9rem; color:#6b7280; }
  .countdown { font-variant-numeric: tabular-nums; font-weight:700; }
  .countdown.overdue { color:#dc3545; }
  .link { text-decoration:none; font-weight:600; }
  .muted { color:#6b7280; }
</style>

@php
  // Lightweight, view-side metrics (okay for simple dashboards)
  $statLaptops = \App\Models\Laptop::count();
  $statStudents = \App\Models\Student::count();
  $statPending = \App\Models\Borrowing::where('status','pending')->count();
  $statOut     = \App\Models\Borrowing::where('status','checked_out')->count();

  $ongoing = \App\Models\Borrowing::with(['laptop','student'])
            ->where('status','checked_out')->orderBy('due_at')->limit(6)->get();

  $pending = \App\Models\Borrowing::with(['laptop','student'])
            ->where('status','pending')->orderByDesc('requested_at')->limit(6)->get();
@endphp

<div class="cards">
  <a class="card-kpi" href="{{ route('admin.laptop') }}">
    <div class="kpi-icon kpi-blue"><i class="fa-solid fa-laptop"></i></div>
    <div>
      <div class="kpi-num">{{ number_format($statLaptops) }}</div>
      <div class="kpi-label">Laptops</div>
    </div>
  </a>

  <a class="card-kpi" href="{{ route('admin.student') }}">
    <div class="kpi-icon kpi-green"><i class="fa-solid fa-user-graduate"></i></div>
    <div>
      <div class="kpi-num">{{ number_format($statStudents) }}</div>
      <div class="kpi-label">Students</div>
    </div>
  </a>

  <a class="card-kpi" href="{{ route('admin.borrower', ['status'=>'pending']) }}">
    <div class="kpi-icon kpi-amber"><i class="fa-solid fa-inbox"></i></div>
    <div>
      <div class="kpi-num">{{ number_format($statPending) }}</div>
      <div class="kpi-label">Pending Requests</div>
    </div>
  </a>

  <a class="card-kpi" href="{{ route('admin.borrower', ['status'=>'approved']) }}">
    <div class="kpi-icon kpi-violet"><i class="fa-solid fa-box-open"></i></div>
    <div>
      <div class="kpi-num">{{ number_format($statOut) }}</div>
      <div class="kpi-label">Checked Out</div>
    </div>
  </a>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="panel">
      <h6>Ongoing (Checked Out)</h6>
      @forelse($ongoing as $b)
        @php
          $img = $b->laptop?->imageUrl() ?? asset('images/no-image.png');
        @endphp
        <div class="item">
          <img class="thumb" src="{{ $img }}" alt="Laptop"
               onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">
          <div class="flex-grow-1">
            <div class="fw-semibold">{{ $b->laptop?->device_name ?? '—' }}</div>
            <div class="meta">
              {{ $b->student?->full_name ?? '—' }}
              @if($b->due_at)
                • Due: {{ $b->due_at->format('M d, Y h:i A') }}
              @endif
            </div>
          </div>
          <div class="text-end">
            @if($b->due_at)
              <div class="countdown" data-due="{{ $b->due_at->toIso8601String() }}">—</div>
            @else
              <span class="muted">—</span>
            @endif
            <div><a class="link" href="{{ route('admin.borrower', ['status'=>'approved','q'=>$b->laptop?->device_name]) }}">Open</a></div>
          </div>
        </div>
      @empty
        <div class="muted">No ongoing checkouts.</div>
      @endforelse
    </div>
  </div>

  <div class="col-lg-6">
    <div class="panel">
      <h6>Latest Pending</h6>
      @forelse($pending as $b)
        @php
          $img = $b->laptop?->imageUrl() ?? asset('images/no-image.png');
        @endphp
        <div class="item">
          <img class="thumb" src="{{ $img }}" alt="Laptop"
               onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">
          <div class="flex-grow-1">
            <div class="fw-semibold">{{ $b->laptop?->device_name ?? '—' }}</div>
            <div class="meta">
              {{ $b->student?->full_name ?? '—' }}
              @if($b->requested_at)
                • Requested: {{ $b->requested_at->format('M d, Y h:i A') }}
              @endif
            </div>
          </div>
          <div class="text-end">
            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.borrower', ['status'=>'pending','q'=>$b->student?->full_name]) }}">
              Review
            </a>
          </div>
        </div>
      @empty
        <div class="muted">No pending requests.</div>
      @endforelse
    </div>
  </div>
</div>

@push('scripts')
<script>
(function(){
  function fmt(ms){
    const neg = ms < 0; ms = Math.abs(ms);
    const s  = Math.floor(ms/1000);
    const d  = Math.floor(s/86400);
    const h  = Math.floor((s%86400)/3600);
    const m  = Math.floor((s%3600)/60);
    const ss = s%60;
    const parts = [];
    if(d) parts.push(d+'d');
    parts.push(String(h).padStart(2,'0')+'h');
    parts.push(String(m).padStart(2,'0')+'m');
    parts.push(String(ss).padStart(2,'0')+'s');
    return (neg?'-':'')+parts.join(' ');
  }
  function tick(){
    const now = Date.now();
    document.querySelectorAll('.countdown[data-due]').forEach(el=>{
      const iso = el.getAttribute('data-due'); if(!iso){el.textContent='—'; return;}
      const diff = new Date(iso).getTime() - now;
      el.textContent = fmt(diff);
      if(diff <= 0) el.classList.add('overdue'); else el.classList.remove('overdue');
    });
  }
  tick(); setInterval(tick, 1000);
})();
</script>
@endpush
@endsection
