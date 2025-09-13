@extends('layouts.admin')
@section('title', 'Admin Dashboard')

@section('content')
<style>
.dashboard-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 22px;
    margin-top: 20px;
}
.card-dashboard {
    flex: 1 1 230px;
    background: #fff;
    border-radius: 12px;
    padding: 26px 22px 18px 22px;
    box-shadow: 0 4px 16px 0 rgba(0,0,0,0.07);
    min-width: 210px;
    max-width: 250px;
    min-height: 125px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-left: 7px solid #ffc107;
    margin-bottom: 14px;
}
.card-dashboard .icon { font-size: 2.1rem; margin-bottom: 10px; }
.card-dashboard .count { font-size: 2.4rem; font-weight: bold; color: #222; }
.card-dashboard .label { font-size: 1.08rem; color: #555; margin-bottom: 6px; }
.card-dashboard .view-link { font-size: 0.97rem; color: #1767f2; font-weight: 500; text-decoration: none; }
.card-dashboard.home     { border-left-color: #0ea5e9; }
.card-dashboard.borrower { border-left-color: #f59e0b; }
.card-dashboard.students { border-left-color: #22c55e; }
.card-dashboard.laptop   { border-left-color: #06b6d4; } /* NEW */
.card-dashboard.ip       { border-left-color: #8b5cf6; }
.card-dashboard.history  { border-left-color: #ef4444; }
</style>

<h2>Admin Dashboard Overview</h2>
<div class="dashboard-cards">

    <div class="card-dashboard home">
        <div class="icon"><i class="fas fa-gauge text-info"></i></div>
        <div class="count">&nbsp;</div>
        <div class="label">Home</div>
        <a href="{{ route('admin.home') }}" class="view-link">Open &rarr;</a>
    </div>

    <div class="card-dashboard borrower">
        <div class="icon"><i class="fas fa-handshake text-warning"></i></div>
        <div class="count">&nbsp;</div>
        <div class="label">Borrower</div>
        <a href="{{ route('admin.borrower') }}" class="view-link">Manage &rarr;</a>
    </div>

    <div class="card-dashboard students">
        <div class="icon"><i class="fas fa-user-graduate text-success"></i></div>
        <div class="count">&nbsp;</div>
        <div class="label">Student</div>
        <a href="{{ route('admin.student') }}" class="view-link">View &rarr;</a>
    </div>

    <!-- NEW: Laptop card -->
    <div class="card-dashboard laptop">
        <div class="icon"><i class="fas fa-laptop" style="color:#06b6d4"></i></div>
        <div class="count">&nbsp;</div>
        <div class="label">Laptop</div>
        <a href="{{ route('admin.laptop') }}" class="view-link">Manage &rarr;</a>
    </div>

    <div class="card-dashboard ip">
        <div class="icon"><i class="fas fa-network-wired" style="color:#8b5cf6"></i></div>
        <div class="count">&nbsp;</div>
        <div class="label">IP</div>
        <a href="{{ route('admin.ip') }}" class="view-link">Manage &rarr;</a>
    </div>

    <div class="card-dashboard history">
        <div class="icon"><i class="fas fa-clock-rotate-left text-danger"></i></div>
        <div class="count">&nbsp;</div>
        <div class="label">History</div>
        <a href="{{ route('admin.history') }}" class="view-link">View &rarr;</a>
    </div>

</div>
@endsection
