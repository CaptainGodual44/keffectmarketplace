@extends('layouts.app')

@section('content')
    <h1>Account Dashboard</h1>
    <h2>Recent Orders</h2>
    <table>
        <thead>
            <tr><th>Order</th><th>Status</th><th>Total</th></tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->status }}</td>
                    <td>L${{ $order->total_linden }}</td>
                </tr>
            @empty
                <tr><td colspan="3">No orders yet.</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
