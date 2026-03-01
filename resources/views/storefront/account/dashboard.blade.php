@extends('layouts.app')

@section('content')
    <h1>Account Dashboard</h1>
    <h2>Recent Orders</h2>
    <table>
        <thead>
            <tr><th>Order</th><th>Status</th><th>Total</th></tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order['id'] }}</td>
                    <td>{{ $order['status'] }}</td>
                    <td>L${{ $order['total_l$'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
