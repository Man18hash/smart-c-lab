@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<!-- Dashboard Stats -->
<div class="row g-4 mb-4">
  <div class="col-lg-3 col-md-6">
    <div class="card-modern">
      <div class="card-body-modern">
        <div class="d-flex align-items-center">
          <div class="flex-shrink-0">
            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                 style="width: 60px; height: 60px; background: rgba(37, 99, 235, 0.1);">
              <i class="fas fa-laptop" style="font-size: 24px; color: var(--primary);"></i>
            </div>
          </div>
          <div class="flex-grow-1 ms-3">
            <h3 class="mb-0" style="font-size: 28px; font-weight: 700; color: var(--text-primary);">
              {{ \App\Models\Laptop::count() }}
            </h3>
            <p class="mb-0" style="color: var(--text-muted); font-size: 14px;">Total Laptops</p>
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
              <i class="fas fa-check-circle" style="font-size: 24px; color: var(--success);"></i>
            </div>
          </div>
          <div class="flex-grow-1 ms-3">
            <h3 class="mb-0" style="font-size: 28px; font-weight: 700; color: var(--text-primary);">
              {{ \App\Models\Laptop::where('status', 'available')->count() }}
            </h3>
            <p class="mb-0" style="color: var(--text-muted); font-size: 14px;">Available</p>
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
                 style="width: 60px; height: 60px; background: rgba(245, 158, 11, 0.1);">
              <i class="fas fa-handshake" style="font-size: 24px; color: var(--warning);"></i>
            </div>
          </div>
          <div class="flex-grow-1 ms-3">
            <h3 class="mb-0" style="font-size: 28px; font-weight: 700; color: var(--text-primary);">
              {{ \App\Models\Borrowing::where('status', 'pending')->count() }}
            </h3>
            <p class="mb-0" style="color: var(--text-muted); font-size: 14px;">Pending Requests</p>
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
                 style="width: 60px; height: 60px; background: rgba(239, 68, 68, 0.1);">
              <i class="fas fa-external-link-alt" style="font-size: 24px; color: var(--danger);"></i>
            </div>
          </div>
          <div class="flex-grow-1 ms-3">
            <h3 class="mb-0" style="font-size: 28px; font-weight: 700; color: var(--text-primary);">
              {{ \App\Models\Borrowing::where('status', 'checked_out')->count() }}
            </h3>
            <p class="mb-0" style="color: var(--text-muted); font-size: 14px;">Currently Out</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="card-modern mb-4">
  <div class="card-header-modern">
    <h2 class="card-title-modern">Quick Actions</h2>
    <p class="card-subtitle-modern">Common tasks and shortcuts</p>
  </div>
  <div class="card-body-modern">
    <div class="row g-3">
      <div class="col-lg-3 col-md-6">
        <a href="{{ route('admin.laptop') }}" class="text-decoration-none">
          <div class="card-modern h-100" style="transition: all 0.2s ease; cursor: pointer;">
            <div class="card-body-modern text-center">
              <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                   style="width: 80px; height: 80px; background: rgba(37, 99, 235, 0.1);">
                <i class="fas fa-laptop" style="font-size: 32px; color: var(--primary);"></i>
              </div>
              <h5 style="font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">Laptop Inventory</h5>
              <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Manage laptop devices</p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-lg-3 col-md-6">
        <a href="{{ route('admin.borrower') }}" class="text-decoration-none">
          <div class="card-modern h-100" style="transition: all 0.2s ease; cursor: pointer;">
            <div class="card-body-modern text-center">
              <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                   style="width: 80px; height: 80px; background: rgba(245, 158, 11, 0.1);">
                <i class="fas fa-handshake" style="font-size: 32px; color: var(--warning);"></i>
              </div>
              <h5 style="font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">Borrower Requests</h5>
              <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Review and approve requests</p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-lg-3 col-md-6">
        <a href="{{ route('admin.student') }}" class="text-decoration-none">
          <div class="card-modern h-100" style="transition: all 0.2s ease; cursor: pointer;">
            <div class="card-body-modern text-center">
              <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                   style="width: 80px; height: 80px; background: rgba(16, 185, 129, 0.1);">
                <i class="fas fa-user-graduate" style="font-size: 32px; color: var(--success);"></i>
              </div>
              <h5 style="font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">Student Management</h5>
              <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Manage student accounts</p>
            </div>
          </div>
        </a>
      </div>

      <div class="col-lg-3 col-md-6">
        <a href="{{ route('admin.history') }}" class="text-decoration-none">
          <div class="card-modern h-100" style="transition: all 0.2s ease; cursor: pointer;">
            <div class="card-body-modern text-center">
              <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                   style="width: 80px; height: 80px; background: rgba(100, 116, 139, 0.1);">
                <i class="fas fa-history" style="font-size: 32px; color: var(--secondary);"></i>
              </div>
              <h5 style="font-weight: 600; color: var(--text-primary); margin-bottom: 8px;">Transaction History</h5>
              <p style="color: var(--text-muted); font-size: 14px; margin: 0;">View past transactions</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Recent Activity -->
<div class="row g-4">
  <div class="col-lg-8">
    <div class="card-modern">
      <div class="card-header-modern">
        <h2 class="card-title-modern">Recent Borrowing Activity</h2>
        <p class="card-subtitle-modern">Latest laptop borrowing requests and activities</p>
      </div>
      <div class="card-body-modern">
        @php
          $recentBorrowings = \App\Models\Borrowing::with(['student', 'laptop'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        @endphp

        @if($recentBorrowings->count() > 0)
          <div class="list-group list-group-flush">
            @foreach($recentBorrowings as $borrowing)
              <div class="list-group-item" style="border: none; padding: 16px 0; border-bottom: 1px solid var(--border-light);">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 40px; height: 40px; background: var(--bg-tertiary);">
                      <i class="fas fa-laptop" style="font-size: 16px; color: var(--text-muted);"></i>
                    </div>
                  </div>
                  <div class="flex-grow-1 ms-3">
                    <h6 style="font-weight: 600; margin: 0; color: var(--text-primary);">
                      {{ $borrowing->student?->full_name ?? 'Unknown Student' }}
                    </h6>
                    <p style="font-size: 14px; color: var(--text-muted); margin: 4px 0 0 0;">
                      Requested {{ $borrowing->laptop?->device_name ?? 'Unknown Device' }}
                    </p>
                  </div>
                  <div class="flex-shrink-0">
                    <span class="status-badge status-{{ $borrowing->status }}">
                      {{ ucfirst($borrowing->status) }}
                    </span>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-4">
            <i class="fas fa-inbox" style="font-size: 48px; color: var(--text-muted); margin-bottom: 16px;"></i>
            <h5 style="color: var(--text-secondary); margin-bottom: 8px;">No Recent Activity</h5>
            <p style="color: var(--text-muted);">No borrowing requests have been made yet.</p>
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card-modern">
      <div class="card-header-modern">
        <h2 class="card-title-modern">System Status</h2>
        <p class="card-subtitle-modern">Current system health and statistics</p>
      </div>
      <div class="card-body-modern">
        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span style="font-weight: 500; color: var(--text-primary);">Laptop Availability</span>
            <span style="font-weight: 600; color: var(--text-primary);">
              {{ \App\Models\Laptop::where('status', 'available')->count() }} / {{ \App\Models\Laptop::count() }}
            </span>
          </div>
          <div class="progress" style="height: 8px; border-radius: var(--radius-sm);">
            <div class="progress-bar" 
                 style="background: var(--success); border-radius: var(--radius-sm);"
                 role="progressbar" 
                 style="width: {{ \App\Models\Laptop::count() > 0 ? (\App\Models\Laptop::where('status', 'available')->count() / \App\Models\Laptop::count()) * 100 : 0 }}%">
            </div>
          </div>
        </div>

        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span style="font-weight: 500; color: var(--text-primary);">Active Borrowings</span>
            <span style="font-weight: 600; color: var(--text-primary);">
              {{ \App\Models\Borrowing::whereIn('status', ['checked_out', 'approved'])->count() }}
            </span>
          </div>
          <div class="progress" style="height: 8px; border-radius: var(--radius-sm);">
            <div class="progress-bar" 
                 style="background: var(--warning); border-radius: var(--radius-sm);"
                 role="progressbar" 
                 style="width: {{ \App\Models\Laptop::count() > 0 ? (\App\Models\Borrowing::whereIn('status', ['checked_out', 'approved'])->count() / \App\Models\Laptop::count()) * 100 : 0 }}%">
            </div>
          </div>
        </div>

        <div class="text-center pt-3">
          <a href="{{ route('admin.borrower') }}" class="btn btn-primary-modern">
            <i class="fas fa-eye"></i>
            <span>View All Requests</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.card-modern:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.list-group-item:last-child {
  border-bottom: none !important;
}

.progress {
  background-color: var(--bg-tertiary);
}
</style>
@endsection
