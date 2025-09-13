@extends('layouts.admin')
@section('title','History')

@section('content')
<style>
  .thumb{ width: 84px; height: 56px; object-fit:cover; border-radius:10px; background:#f2f4f8; }
  .small-muted{font-size:.9rem;color:#6c6c70}
  .card-cupertino{overflow:hidden}
</style>

<div class="card-cupertino p-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Returned Borrowings</h5>
    <form class="d-flex gap-2 flex-wrap" method="GET" action="{{ route('admin.history') }}">
      <input type="search" name="q" class="form-control"
             placeholder="Search borrower, device, purpose" value="{{ $q ?? '' }}">
      <input type="date"  name="from" class="form-control" value="{{ $from ?? '' }}">
      <input type="date"  name="to"   class="form-control" value="{{ $to ?? '' }}">
      <button class="btn btn-primary">Filter</button>
    </form>
  </div>

  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Image</th>
          <th>Device</th>
          <th>Borrower</th>
          <th>Purpose</th>
          <th>Out</th>
          <th>Returned</th>
        </tr>
      </thead>
      <tbody>
        @forelse($history as $b)
          @php
            $img = $b->laptop?->imageUrl() ?? asset('images/no-image.png');
          @endphp
          <tr>
            <td style="width:120px">
              <img class="thumb" src="{{ $img }}" alt="Laptop"
                   onerror="this.onerror=null;this.src='{{ asset('images/no-image.png') }}';">
            </td>
            <td class="fw-semibold">
              {{ $b->laptop?->device_name ?? '—' }}
              @if($b->remarks)<div class="small-muted">Remarks: {{ $b->remarks }}</div>@endif
            </td>
            <td>
              <div class="fw-semibold">{{ $b->student?->full_name ?? '—' }}</div>
              <div class="small-muted">{{ $b->student?->email ?? '' }}</div>
            </td>
            <td>{{ $b->purpose ?: '—' }}</td>
            <td>{{ optional($b->checked_out_at)->format('M d, Y h:i A') ?? '—' }}</td>
            <td>{{ optional($b->returned_at)->format('M d, Y h:i A') ?? '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-muted">No returned records found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($history instanceof \Illuminate\Contracts\Pagination\Paginator && $history->hasPages())
    <div class="mt-2">
      @php echo $history->links('pagination::bootstrap-5'); @endphp
    </div>
  @endif
</div>
@endsection
