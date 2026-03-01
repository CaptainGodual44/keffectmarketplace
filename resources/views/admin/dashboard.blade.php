@extends('layouts.app')

@section('content')
    <h1>Admin Dashboard</h1>
    <div class="grid grid-3">
        <div class="card"><strong>Orders Today</strong><br>{{ $metrics['orders_today'] }}</div>
        <div class="card"><strong>Revenue (L$)</strong><br>{{ $metrics['revenue_l$'] }}</div>
        <div class="card"><strong>Pending Reviews</strong><br>{{ $metrics['pending_reviews'] }}</div>
    </div>
    <div class="card" style="margin-top:1rem;">
        <strong>Open Support Threads:</strong> {{ $metrics['open_support_threads'] }}
    </div>
@endsection
