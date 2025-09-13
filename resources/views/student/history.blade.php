@extends('layouts.student')
@section('title', 'History')

@section('content')
<div class="bg-white rounded-3 shadow-sm p-4">
  <h5 class="mb-3">Borrowing History</h5>

  <p class="text-muted">This is a placeholder table. Later, load records from the <code>borrowings</code> and <code>borrow_events</code> tables filtered by the logged-in student.</p>

  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead>
        <tr>
          <th>Date</th>
          <th>Laptop</th>
          <th>IP</th>
          <th>Status</th>
          <th>Time In</th>
          <th>Time Out</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="6" class="text-muted">No records yet.</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endsection
