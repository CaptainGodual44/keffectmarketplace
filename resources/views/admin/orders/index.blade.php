@extends('layouts.app')

@section('content')
    <h1>Admin • Orders</h1>
    <table>
        <thead><tr><th>Order</th><th>Status</th><th>Payment</th><th>Customer</th></tr></thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->payment_method }}</td>
                <td>{{ $order->user?->email }}</td>
            </tr>
        @empty
            <tr><td colspan="4">No orders found.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem;">
        {{ $orders->links() }}
    </div>
@endsection
