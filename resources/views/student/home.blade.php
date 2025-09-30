@extends('layouts.student')
@section('title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="card-modern mb-4">
  <div class="card-body-modern">
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h2 class="card-title-modern">Welcome back, {{ auth()->user()->name ?? 'Student' }}!</h2>
        <p class="card-subtitle-modern">Manage your laptop borrowing requests and track your activity</p>
      </div>
      <div class="text-end">
        <div class="user-avatar" style="width: 60px; height: 60px; font-size: 24px;">
          {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mb-4">
  <div class="col-lg-6">
    <div class="card-modern h-100">
      <div class="card-body-modern text-center">
        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
             style="width: 80px; height: 80px; background: rgba(16, 185, 129, 0.1);">
          <i class="fas fa-handshake" style="font-size: 32px; color: var(--primary);"></i>
        </div>
        <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">Borrow a Laptop</h4>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Submit a request to borrow a laptop from the lab</p>
        <a href="{{ route('student.borrow') }}" class="btn btn-primary-modern">
          <i class="fas fa-plus"></i>
          <span>New Request</span>
        </a>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card-modern h-100">
      <div class="card-body-modern text-center">
        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
             style="width: 80px; height: 80px; background: rgba(100, 116, 139, 0.1);">
          <i class="fas fa-history" style="font-size: 32px; color: var(--secondary);"></i>
        </div>
        <h4 style="font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">View History</h4>
        <p style="color: var(--text-muted); margin-bottom: 20px;">Check your past borrowing requests and activity</p>
        <a href="{{ route('student.history') }}" class="btn btn-secondary-modern">
          <i class="fas fa-eye"></i>
          <span>View History</span>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Stats Overview -->
<div class="row g-4 mb-4">
  <div class="col-lg-3 col-md-6">
    <div class="card-modern">
      <div class="card-body-modern">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                 style="width: 60px; height: 60px; background: rgba(245, 158, 11, 0.1);">
              <i class="fas fa-clock" style="font-size: 24px; color: var(--warning);"></i>
            </div>
          </div>
          <div class="flex-grow-1 ms-3">
            <h3 class="mb-0" style="font-size: 28px; font-weight: 700; color: var(--text-primary);">
              {{ $stats['pending'] ?? 0 }}
            </h3>
            <p class="mb-0" style="color: var(--text-muted); font-size: 14px;">Pending</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6">
    <div class="card-modern">
      <div class="card-body-modern">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                 style="width: 60px; height: 60px; background: rgba(6, 182, 212, 0.1);">
              <i class="fas fa-check-circle" style="font-size: 24px; color: var(--info);"></i>
            </div>
          </div>
          <div class="flex-grow-1 ms-3">
            <h3 class="mb-0" style="font-size: 28px; font-weight: 700; color: var(--text-primary);">
              {{ $stats['approved'] ?? 0 }}
            </h3>
            <p class="mb-0" style="color: var(--text-muted); font-size: 14px;">Approved</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6">
    <div class="card-modern">
      <div class="card-body-modern">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                 style="width: 60px; height: 60px; background: rgba(37, 99, 235, 0.1);">
              <i class="fas fa-laptop" style="font-size: 24px; color: #2563eb;"></i>
            </div>
          </div>
          <div class="flex-grow-1 ms-3">
            <h3 class="mb-0" style="font-size: 28px; font-weight: 700; color: var(--text-primary);">
              {{ $stats['out'] ?? 0 }}
            </h3>
            <p class="mb-0" style="color: var(--text-muted); font-size: 14px;">Checked Out</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6">
    <div class="card-modern">
      <div class="card-body-modern">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                 style="width: 60px; height: 60px; background: rgba(16, 185, 129, 0.1);">
              <i class="fas fa-check-double" style="font-size: 24px; color: var(--success);"></i>
            </div>
          </div>
          <div class="flex-grow-1 ms-3">
            <h3 class="mb-0" style="font-size: 28px; font-weight: 700; color: var(--text-primary);">
              {{ $stats['returned'] ?? 0 }}
            </h3>
            <p class="mb-0" style="color: var(--text-muted); font-size: 14px;">Returned</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Ongoing Borrowings -->
@if($ongoing->count() > 0)
<div class="card-modern mb-4">
  <div class="card-header-modern">
    <h2 class="card-title-modern">Currently Borrowed</h2>
    <p class="card-subtitle-modern">Laptops you currently have checked out</p>
  </div>
  <div class="card-body-modern">
    <div class="device-grid">
      @foreach($ongoing as $b)
        @php
          $img = $b->laptop?->imageUrl() ?? asset('images/no-image.svg');
          $isCheckedOut = $b->status === 'checked_out';
          $running = in_array($b->status, ['approved','checked_out']) && $b->due_at;
        @endphp
        <div class="device-card">
          <img class="device-image" src="{{ $img }}" alt="Laptop"
               onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';" loading="lazy">
          <div class="device-info">
            <h3 class="device-name">{{ $b->laptop?->device_name ?? '—' }}</h3>
            
            <div class="device-status">
              <span class="status-badge {{ $isCheckedOut ? 'status-checked_out' : 'status-approved' }}">
                {{ $isCheckedOut ? 'Checked Out' : 'Approved' }}
              </span>
            </div>
            
            @if($b->due_at)
              <div class="mb-3">
                <div style="font-size: 14px; color: var(--text-muted); margin-bottom: 4px;">
                  <strong>Due:</strong> {{ $b->due_at->format('M d, Y h:i A') }}
                </div>
                @if($running)
                  <div style="font-size: 16px; font-weight: 600;">
                    <span class="countdown" data-due="{{ $b->due_at->toIso8601String() }}" style="color: var(--primary);">—</span>
                  </div>
                @endif
              </div>
            @endif
            
            @if($b->purpose)
              <p style="font-size: 14px; color: var(--text-muted); margin-bottom: 16px;">
                <strong>Purpose:</strong> {{ \Illuminate\Support\Str::limit($b->purpose, 100) }}
              </p>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endif

<!-- Recent Requests -->
<div class="card-modern">
  <div class="card-header-modern">
    <h2 class="card-title-modern">Recent Requests</h2>
    <p class="card-subtitle-modern">Your latest borrowing activity</p>
  </div>
  <div class="card-body-modern">
    @if($recent->count() > 0)
      <div class="row g-3">
        @foreach($recent as $b)
          @php
            $img = $b->laptop?->imageUrl() ?? asset('images/no-image.svg');
            $badgeMap = [
              'pending'=>'status-pending',
              'approved'=>'status-approved',
              'declined'=>'status-declined',
              'checked_out'=>'status-checked_out',
              'returned'=>'status-returned',
              'overdue'=>'status-declined'
            ];
            $badgeClass = $badgeMap[$b->status] ?? 'status-pending';
          @endphp
          <div class="col-lg-6">
            <div class="card-modern">
              <div class="card-body-modern">
                <div class="d-flex align-items-start gap-3">
                  <img src="{{ $img }}" alt="Laptop" 
                       style="width: 80px; height: 60px; object-fit: cover; border-radius: var(--radius-md);"
                       onerror="this.onerror=null;this.src='{{ asset('images/no-image.svg') }}';">
                  <div class="flex-grow-1">
                    <h5 style="font-weight: 600; margin: 0 0 8px 0; color: var(--text-primary);">
                      {{ $b->laptop?->device_name ?? 'Laptop' }}
                    </h5>
                    <div style="font-size: 14px; color: var(--text-muted); margin-bottom: 8px;">
                      <div><strong>Requested:</strong> {{ optional($b->requested_at)->format('M d, Y h:i A') ?? '—' }}</div>
                      @if($b->due_at)
                        <div><strong>Due:</strong> {{ $b->due_at->format('M d, Y h:i A') }}</div>
                      @endif
                    </div>
                    <span class="status-badge {{ $badgeClass }}">
                      {{ str_replace('_', ' ', $b->status) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-5">
        <i class="fas fa-inbox" style="font-size: 64px; color: var(--text-muted); margin-bottom: 16px;"></i>
        <h4 style="color: var(--text-secondary); margin-bottom: 8px;">No Recent Requests</h4>
        <p style="color: var(--text-muted);">You haven't made any borrowing requests yet.</p>
        <a href="{{ route('student.borrow') }}" class="btn btn-primary-modern mt-3">
          <i class="fas fa-plus"></i>
          <span>Make Your First Request</span>
        </a>
      </div>
    @endif
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
      if(diff <= 0) {
        el.style.color = 'var(--danger)';
        el.style.fontWeight = '700';
      } else {
        el.style.color = 'var(--primary)';
        el.style.fontWeight = '600';
      }
    });
  }
  
  tick(); 
  setInterval(tick, 1000);
})();
</script>
@endpush
@endsection
