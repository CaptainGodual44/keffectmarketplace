@extends('layouts.app')

@section('content')
    <h1>Admin • Orders</h1>
    <table>
        <thead><tr><th>Order</th><th>Status</th><th>Payment</th><th>Customer</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->payment_method }}</td>
                <td>{{ $order->user?->email }}</td>
                <td><a href="{{ route('admin.orders.show', $order) }}">View</a></td>
            </tr>
        @empty
            <tr><td colspan="5">No orders found.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem;">{{ $orders->links() }}</div>
@endsection
