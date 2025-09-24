@extends('layouts.student')
@section('title', 'History')

@section('content')
<style>
  :root{
    --border:#e9eef5; --ink:#111114; --muted:#6b7280;
    --card:#fff; --soft:#f6f8fc; --blue:#0A84FF; --green:#34C759; --violet:#6E5DDC; --amber:#F59E0B;
  }
  @media (prefers-color-scheme:dark){
    :root{ --border:#2C2C2E; --card:#151517; --soft:#111114; --ink:#ECECEC; --muted:#9b9ba1; }
  }

  .panel { background:var(--card); border:1px solid var(--border); border-radius:16px; padding:16px; box-shadow:0 8px 24px rgba(0,0,0,.06); }
  .panel h6{ margin:0 0 10px; font-weight:800; }

  .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:14px; }
  .ongoing {
    border:1px solid var(--border); border-radius:14px; overflow:hidden; background:var(--card);
    box-shadow:0 6px 18px rgba(0,0,0,.06);
  }
  .thumb { width:100%; aspect-ratio:16/10; object-fit:cover; background:#f2f4f8; }
  .body { padding:12px 14px; }
  .title { font-weight:800; color:var(--ink); }
  .meta { color:var(--muted); font-size:.92rem; }
  .status-pill { border-radius:999px; font-size:.78rem; padding:6px 10px; font-weight:700; }
  .s-approved{ background:rgba(10,132,255,.14); border:1px solid rgba(10,132,255,.28); color:#0A84FF; }
  .s-checked{ background:rgba(14,165,233,.16); border:1px solid rgba(14,165,233,.28); color:#0284C7; }
  .countdown{ font-variant-numeric:tabular-nums; font-weight:800; }
  .countdown.overdue{ color:#dc3545; }

  .list-item { display:flex; gap:12px; padding:10px 0; border-bottom:1px dashed var(--border); }
  .list-item:last-child{ border-bottom:0; }
  .list-thumb{ width:60px; height:40px; object-fit:cover; border-radius:8px; background:#f2f4f8; }
</style>

<div class="row g-3">
  {{-- Ongoing (same UI as Home) --}}
  <div class="col-lg-6">
    <div class="panel">
      <h6>Ongoing</h6>
      @if($ongoing->count())
        <div class="grid">
          @foreach($ongoing as $b)
            @php
              $img = $b->laptop?->imageUrl() ?? asset('images/no-image.png');
              $isCheckedOut = $b->status === 'checked_out';
            @endphp
            <div class="ongoing">
              <img class="thumb" src="{{ $img }}" alt="Laptop"
                   onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';" loading="lazy">
              <div class="body">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="title">{{ $b->laptop?->device_name ?? '—' }}</div>
                  <span class="status-pill {{ $isCheckedOut ? 's-checked' : 's-approved' }}">
                    {{ $isCheckedOut ? 'Checked Out' : 'Approved' }}
                  </span>
                </div>
                <div class="meta mt-1">
                  @if($b->due_at)
                    <strong>Due:</strong> {{ $b->due_at->format('M d, Y h:i A') }}
                    <br>
                    <span class="countdown" data-due="{{ $b->due_at->toIso8601String() }}">—</span>
                  @else
                    <span class="text-muted">Due time not set</span>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="text-muted">No ongoing items.</div>
      @endif
    </div>
  </div>

  {{-- Recent Requests (same UI as Home, but paginated & longer list) --}}
  <div class="col-lg-6">
    <div class="panel">
      <h6>Recent Requests</h6>
      @if($recent->count())
        @foreach($recent as $b)
          @php
            $img = $b->laptop?->imageUrl() ?? asset('images/no-image.png');
            $badgeMap = [
              'pending'=>'warning','approved'=>'info','declined'=>'danger',
              'checked_out'=>'primary','returned'=>'success','overdue'=>'danger'
            ];
            $badge = $badgeMap[$b->status] ?? 'secondary';
          @endphp
          <div class="list-item">
            <img class="list-thumb" src="{{ $img }}" alt="Laptop"
                 onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">
            <div class="flex-grow-1">
              <div class="fw-semibold">{{ $b->laptop?->device_name ?? '—' }}</div>
              <div class="meta">
                <strong>Requested:</strong> {{ optional($b->requested_at)->format('M d, Y h:i A') ?? '—' }}
                @if($b->due_at) • <strong>Due:</strong> {{ $b->due_at->format('M d, Y h:i A') }} @endif
              </div>
            </div>
            <span class="badge bg-{{ $badge }}" style="text-transform:capitalize;">
              {{ str_replace('_',' ',$b->status) }}
            </span>
          </div>
        @endforeach

        <div class="mt-2">
          {{ $recent->withQueryString()->links() }}
        </div>
      @else
        <div class="text-muted">No recent requests.</div>
      @endif
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
    return (neg?'-':'') + parts.join(' ');
  }
  function tick(){
    const now = Date.now();
    document.querySelectorAll('.countdown[data-due]').forEach(el=>{
      const iso = el.getAttribute('data-due');
      if(!iso){ el.textContent = '—'; return; }
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
