@extends('layouts.admin')
@section('title', 'Borrower Requests')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
  .ongoing-wrap{margin-bottom:18px}
  .ongoing-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:18px}
  .ongoing-card{position:relative;background:#fff;border-radius:14px;box-shadow:0 6px 22px rgba(0,0,0,.06);overflow:hidden;border:1px solid #edf1f6}
  .ongoing-img{width:100%;height:160px;object-fit:cover;background:#f5f7fb}
  .ongoing-body{padding:14px}
  .ongoing-title{font-weight:700;font-size:1rem;margin:0 0 4px}
  .ongoing-sub{color:#6c757d;font-size:.86rem;margin:0 0 10px}
  .ongoing-meta{display:grid;grid-template-columns:1fr auto;align-items:center}
  .pill{background:#eef1f6;color:#233;font-weight:600;border-radius:999px;padding:6px 10px;font-size:.82rem;display:inline-block}
  .link-pill{position:absolute;top:10px;right:12px;background:#ffffffd9;border-radius:999px;padding:6px 10px;font-size:.9rem;border:1px solid #e6ebf2;text-decoration:none}
  .link-pill:hover{background:#fff}
  .mini-map{height:120px;border-radius:10px;overflow:hidden;margin-top:8px;border:1px solid #eef1f6}

  .filter-pills .nav-link{ border-radius:999px; }
  .thumb{ width: 84px; height: 56px; object-fit:cover; border-radius:10px; background:#f2f4f8; }
  .table thead th{ white-space:nowrap; }
  .small-muted{font-size:.86rem;color:#6c757d}
  .countdown{font-variant-numeric:tabular-nums;font-weight:700}
  .countdown.overdue{color:#dc3545}
  .cd-inline{ margin-left:.35rem; font-variant-numeric:tabular-nums; }
  .cd-inline.overdue{ color:#dc3545; font-weight:600; }
</style>

@php
  use Illuminate\Contracts\Pagination\Paginator as Pagi;
  $items = $borrowings instanceof Pagi ? collect($borrowings->items()) : collect($borrowings);
  $ongoing = isset($ongoingBorrowings) ? collect($ongoingBorrowings) : $items->where('status','checked_out');
@endphp

@if($ongoing->count())
  <div class="ongoing-wrap">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="mb-0">Ongoing Checkouts</h5>
    </div>
    <div class="ongoing-grid">
      @foreach($ongoing as $b)
        @php
          $img = $b->laptop?->imageUrl() ?? asset('images/no-image.png');
          $locLabel = $b->ipAsset?->notes ?: $b->ipAsset?->name;
          $lat = $b->ipAsset->latitude ?? null;
          $lng = $b->ipAsset->longitude ?? null;
        @endphp
        <div class="ongoing-card">
          <img class="ongoing-img" src="{{ $img }}" alt="Laptop"
               onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';" loading="lazy">
          <a class="link-pill" href="#" data-bs-toggle="modal" data-bs-target="#viewBorrow-{{ $b->id }}">view more</a>
          <div class="ongoing-body">
            <div class="ongoing-title">{{ $b->laptop?->device_name ?? '—' }}</div>
            <div class="ongoing-sub">
              {{ $b->student?->full_name ?? '—' }}
              @if($locLabel) • {{ $locLabel }} @endif
            </div>
            <div class="ongoing-meta">
              <span class="pill">Ongoing</span>
              <span class="countdown"
                    data-due="{{ optional($b->due_at)->toIso8601String() }}"
                    id="card-{{ $b->id }}">—</span>
            </div>
            @if($lat && $lng)
              <div id="mini-map-{{ $b->id }}" class="mini-map"
                   data-lat="{{ $lat }}" data-lng="{{ $lng }}"></div>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  </div>
@endif

<div class="bg-white rounded-3 shadow-sm p-4">
  <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Borrower Requests</h5>
    <form class="d-flex" method="GET" action="{{ route('admin.borrower') }}">
      <input type="hidden" name="status" value="{{ $status }}">
      <input type="search" name="q" class="form-control me-2" placeholder="Search student or device" value="{{ $q }}">
      <button class="btn btn-outline-secondary" type="submit">Search</button>
    </form>
  </div>

  @php $tabs = ['pending'=>'Pending','active'=>'Active','done'=>'Done']; @endphp
  <ul class="nav nav-pills filter-pills justify-content-center mb-3">
    @foreach($tabs as $key=>$label)
      <li class="nav-item">
        <a class="nav-link {{ $status===$key ? 'active':'' }}"
           href="{{ route('admin.borrower', ['status'=>$key] + ($q ? ['q'=>$q] : [])) }}">
           {{ $label }}
        </a>
      </li>
    @endforeach
  </ul>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif

  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Image</th>
          <th>Device Details</th>
          <th>Borrower</th>
          <th>Time Remaining</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($borrowings as $b)
          @php
            $img = $b->laptop?->imageUrl() ?? asset('images/no-image.png');
            $running = in_array($b->status,['approved','checked_out']) && $b->due_at;
            $done    = $b->status === 'returned';
            $locLabel = $b->ipAsset?->notes ?: $b->ipAsset?->name;

            // derive requested hours if student picked hours on request
            $requestedHours = null;
            if ($b->requested_at && $b->due_at) {
              $mins = $b->requested_at->diffInMinutes($b->due_at, false);
              if ($mins > 0) $requestedHours = max(1, (int) ceil($mins / 60));
            }
          @endphp
          <tr>
            <td style="width:120px">
              <img class="thumb" src="{{ $img }}" alt="Laptop"
                   onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';" loading="lazy">
            </td>
            <td class="fw-semibold">
              {{ $b->laptop?->device_name ?? '—' }}

              @if($b->status === 'pending')
                @if($requestedHours)
                  <div class="small-muted">
                    Requested: {{ $requestedHours }}h <em>(starts when approved)</em>
                  </div>
                @endif
              @else
                @if($b->due_at)
                  <div class="small-muted">
                    Due: {{ $b->due_at->format('M d, Y h:i A') }}
                    @if(in_array($b->status,['approved','checked_out']))
                      <span class="cd-inline" data-due="{{ $b->due_at->toIso8601String() }}">(—)</span>
                    @endif
                  </div>
                @endif
              @endif

              @if($locLabel)<div class="small-muted">Location: {{ $locLabel }}</div>@endif
              @if($b->purpose)<div class="small-muted">Purpose: {{ $b->purpose }}</div>@endif
            </td>

            <td>
              <div class="fw-semibold">{{ $b->student?->full_name ?? '—' }}</div>
              <div class="small-muted">{{ $b->student?->email ?? '' }}</div>
              @if($b->requested_at)
                <div class="small-muted">Requested: {{ $b->requested_at->format('M d, Y h:i A') }}</div>
              @endif
            </td>

            <td style="min-width:170px">
              @if($running)
                <span class="countdown" data-due="{{ $b->due_at->toIso8601String() }}" id="row-{{ $b->id }}">—</span>
              @else
                —
              @endif
            </td>

            <td class="text-end">
              <button class="btn btn-sm btn-outline-secondary me-1" 
                      data-bs-toggle="modal"
                      data-bs-target="#viewBorrow-{{ $b->id }}"
                      onclick="console.log('View More clicked for borrowing {{ $b->id }}')">View More</button>

              @if($b->status==='pending')
                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal"
                        data-bs-target="#approveBorrow-{{ $b->id }}">Approve</button>
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                        data-bs-target="#declineBorrow-{{ $b->id }}">Decline</button>
              @elseif($b->status==='checked_out')
                <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                        data-bs-target="#terminateBorrow-{{ $b->id }}">Terminate</button>
                <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                        data-bs-target="#checkinBorrow-{{ $b->id }}">Check In</button>
              @elseif($done)
                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                        data-bs-target="#checkinAgain-{{ $b->id }}">Check-In Again</button>
              @endif
            </td>
          </tr>

        @empty
          <tr><td colspan="5" class="text-muted">No requests found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($borrowings instanceof \Illuminate\Contracts\Pagination\Paginator && $borrowings->hasPages())
    <div class="mt-2">@php echo $borrowings->links('pagination::bootstrap-5'); @endphp</div>
  @endif
</div>

{{-- Modals for all borrowings (both ongoing and table) --}}
@php
  // Collect all borrowings that need modals
  $allBorrowings = collect();
  
  // Add ongoing borrowings
  if (isset($ongoingBorrowings)) {
    $allBorrowings = $allBorrowings->merge($ongoingBorrowings);
  }
  
  // Add table borrowings (handle pagination)
  if ($borrowings instanceof \Illuminate\Contracts\Pagination\Paginator) {
    $allBorrowings = $allBorrowings->merge($borrowings->items());
  } else {
    $allBorrowings = $allBorrowings->merge($borrowings);
  }
  
  // Remove duplicates by ID
  $allBorrowings = $allBorrowings->unique('id');
@endphp

@foreach($allBorrowings as $b)
  @php
    $img = $b->laptop?->imageUrl() ?? asset('images/no-image.png');
    $locLabel = $b->ipAsset?->notes ?: $b->ipAsset?->name;
    $lat = $b->ipAsset->latitude ?? null;
    $lng = $b->ipAsset->longitude ?? null;
    
    // derive requested hours if student picked hours on request
    $requestedHours = null;
    if ($b->requested_at && $b->due_at) {
      $mins = $b->requested_at->diffInMinutes($b->due_at, false);
      if ($mins > 0) $requestedHours = max(1, (int) ceil($mins / 60));
    }
  @endphp

  {{-- View More Modal --}}
  <div class="modal fade" id="viewBorrow-{{ $b->id }}" tabindex="-1" aria-hidden="true" aria-labelledby="viewBorrowLabel-{{ $b->id }}">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h6 class="modal-title" id="viewBorrowLabel-{{ $b->id }}">Borrow Details - ID: {{ $b->id }}</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-5">
              <img class="w-100 rounded" src="{{ $img }}" alt="Laptop"
                   onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">
            </div>
            <div class="col-md-7">
              <div><strong>Laptop:</strong> {{ $b->laptop?->device_name ?? '—' }}</div>
              <div><strong>Borrower:</strong> {{ $b->student?->full_name ?? '—' }} ({{ $b->student?->email ?? '—' }})</div>
              <div><strong>Purpose:</strong> {{ $b->purpose ?: '—' }}</div>

              @if($b->status === 'pending')
                @if($requestedHours)
                  <div class="mt-2"><strong>Requested:</strong> {{ $requestedHours }}h <em>(starts when approved)</em></div>
                @endif
              @else
                <div class="mt-2">
                  <strong>Due:</strong>
                  {{ optional($b->due_at)->format('M d, Y h:i A') ?? '—' }}
                  @if($b->due_at && in_array($b->status,['approved','checked_out']))
                    <span class="cd-inline" data-due="{{ $b->due_at->toIso8601String() }}">(—)</span>
                  @endif
                </div>
              @endif

              <div><strong>Status:</strong> {{ ucfirst(str_replace('_',' ',$b->status)) }}</div>

              @if($locLabel)<div class="mt-2"><strong>Location:</strong> {{ $locLabel }}</div>@endif

              @if($lat && $lng)
                <div id="modal-map-{{ $b->id }}" class="w-100" style="height:260px;border-radius:10px;overflow:hidden;border:1px solid #eef1f6"
                     data-lat="{{ $lat }}" data-lng="{{ $lng }}"></div>
              @endif
            </div>
          </div>

          @if($b->due_at && in_array($b->status,['approved','checked_out']))
            <hr class="my-3">
            <form method="POST" action="{{ route('admin.borrowing.stop-timer', $b) }}"
                  onsubmit="return confirm('Stop timer (check-in now)?');">
              @csrf
              <button type="submit" class="btn btn-outline-danger">Terminate Time (Check-In Now)</button>
            </form>
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Approve Modal --}}
  @if($b->status === 'pending')
    <div class="modal fade" id="approveBorrow-{{ $b->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form method="POST" action="{{ route('admin.borrowing.approve', $b) }}">
            @csrf
            <div class="modal-header">
              <h6 class="modal-title">Approve & Check Out</h6>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Adjust Due (optional)</label>
                <input type="datetime-local" name="due_at" class="form-control"
                       value="">
                <div class="form-text">Leave blank to start the timer from now using the requested hours.</div>
              </div>

              <div class="mb-3">
                <label class="form-label">Assign IP (optional)</label>
                <select name="ip_asset_id" class="form-select">
                  <option value="">— No IP —</option>
                  @foreach($freeIps as $ip)
                    <option value="{{ $ip->id }}">{{ $ip->name }} ({{ $ip->ip_address }})</option>
                  @endforeach
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Remarks (optional)</label>
                <textarea name="remarks" class="form-control" rows="2"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Approve & Check Out</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Decline Modal --}}
    <div class="modal fade" id="declineBorrow-{{ $b->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form method="POST" action="{{ route('admin.borrowing.decline', $b) }}">
            @csrf
            <div class="modal-header">
              <h6 class="modal-title">Decline Request</h6>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <label class="form-label">Remarks (optional)</label>
              <textarea name="remarks" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-danger">Decline</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif

  {{-- Terminate Modal --}}
  @if($b->status === 'checked_out')
    <div class="modal fade" id="terminateBorrow-{{ $b->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form method="POST" action="{{ route('admin.borrowing.terminate', $b) }}">
            @csrf
            <div class="modal-header">
              <h6 class="modal-title">Terminate Borrowing</h6>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p class="mb-3">This will immediately return the laptop and end the borrowing session.</p>
              <label class="form-label">Remarks (optional)</label>
              <textarea name="remarks" class="form-control" rows="3" placeholder="Reason for termination..."></textarea>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-warning">Terminate</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Check-In Modal --}}
    <div class="modal fade" id="checkinBorrow-{{ $b->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form method="POST" action="{{ route('admin.borrowing.checkin', $b) }}">
            @csrf
            <div class="modal-header">
              <h6 class="modal-title">Check In Laptop</h6>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <label class="form-label">Remarks (optional)</label>
              <textarea name="remarks" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success">Check In</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif

  {{-- Check-In Again Modal --}}
  @if($b->status === 'returned')
    <div class="modal fade" id="checkinAgain-{{ $b->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form method="POST" action="{{ route('admin.borrowing.checkin', $b) }}">
            @csrf
            <div class="modal-header">
              <h6 class="modal-title">Check-In Again</h6>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <label class="form-label">Remarks (optional)</label>
              <textarea name="remarks" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success">Confirm</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
@endforeach

<script>
(function(){
  // Debug modal functionality
  console.log('Borrower page loaded');
  console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? 'Loaded' : 'Not loaded');
  
  // Check if modals exist
  document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('[id^="viewBorrow-"]');
    const buttons = document.querySelectorAll('[data-bs-target^="#viewBorrow-"]');
    
    console.log('Found', modals.length, 'view modals');
    console.log('Found', buttons.length, 'view more buttons');
    
    // Log all modal IDs
    modals.forEach(modal => {
      console.log('Modal ID:', modal.id);
    });
    
    // Log all button targets
    buttons.forEach(button => {
      console.log('Button target:', button.getAttribute('data-bs-target'));
    });
    
    // Add click event listeners to View More buttons
    buttons.forEach(button => {
      button.addEventListener('click', function(e) {
        const targetId = this.getAttribute('data-bs-target');
        const modal = document.querySelector(targetId);
        console.log('View More clicked, target:', targetId, 'modal exists:', !!modal);
        
        if (!modal) {
          console.error('Modal not found:', targetId);
          console.error('Available modals:', Array.from(modals).map(m => m.id));
          e.preventDefault();
          return false;
        }
        
        // Try to manually show the modal
        try {
          const bsModal = new bootstrap.Modal(modal);
          bsModal.show();
          console.log('Modal shown successfully');
        } catch (error) {
          console.error('Error showing modal:', error);
        }
      });
    });
  });

  function fmt(ms){
    const neg = ms < 0; ms = Math.abs(ms);
    const s  = Math.floor(ms/1000);
    const d  = Math.floor(s/86400);
    const h  = Math.floor((s%86400)/3600);
    const m  = Math.floor((s%3600)/60);
    const ss = s%60;
    const parts=[]; if(d) parts.push(d+'d');
    parts.push(String(h).padStart(2,'0')+'h');
    parts.push(String(m).padStart(2,'0')+'m');
    parts.push(String(ss).padStart(2,'0')+'s');
    return (neg?'-':'')+parts.join(' ');
  }
  function tick(){
    const now = Date.now();

    document.querySelectorAll('.countdown[data-due]').forEach(el=>{
      const iso = el.getAttribute('data-due'); if(!iso){el.textContent='—';return;}
      const diff = new Date(iso).getTime() - now;
      el.textContent = fmt(diff);
      if(diff<=0) el.classList.add('overdue'); else el.classList.remove('overdue');
    });

    document.querySelectorAll('.cd-inline[data-due]').forEach(el=>{
      const iso = el.getAttribute('data-due'); if(!iso){el.textContent='';return;}
      const diff = new Date(iso).getTime() - now;
      el.textContent = '(' + fmt(diff) + ')';
      if(diff<=0) el.classList.add('overdue'); else el.classList.remove('overdue');
    });

    // Check for expired borrowings and auto-return them
    const expiredElements = document.querySelectorAll('.countdown[data-due]');
    expiredElements.forEach(el => {
      const iso = el.getAttribute('data-due');
      if (iso) {
        const diff = new Date(iso).getTime() - now;
        if (diff <= 0 && !el.classList.contains('auto-returned')) {
          el.classList.add('auto-returned');
          // Auto-return expired borrowing
          fetch('{{ route("admin.borrowing.auto-return") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          }).then(response => response.json())
            .then(data => {
              console.log(data.message);
              // Reload page to reflect changes
              setTimeout(() => location.reload(), 2000);
            });
        }
      }
    });
  }
  tick(); setInterval(tick, 1000);

  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('[id^="mini-map-"][data-lat][data-lng]').forEach(el=>{
      const lat = parseFloat(el.dataset.lat), lng = parseFloat(el.dataset.lng);
      const map = L.map(el, { zoomControl:false, attributionControl:false }).setView([lat,lng], 16);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
      L.marker([lat,lng]).addTo(map);
    });
  });

  document.addEventListener('shown.bs.modal', function(e){
    const el = e.target.querySelector('[id^="modal-map-"][data-lat][data-lng]');
    if(!el) return;
    const lat = parseFloat(el.dataset.lat), lng = parseFloat(el.dataset.lng);
    const map = L.map(el).setView([lat,lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
    L.marker([lat,lng]).addTo(map);
    setTimeout(()=> map.invalidateSize(), 200);
  });
})();
</script>
@endsection
