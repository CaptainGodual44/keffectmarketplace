@extends('layouts.app')

@section('content')
    <h1>Admin • Orders</h1>
    <table>
        <thead><tr><th>Order</th><th>Status</th><th>Payment</th></tr></thead>
        <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $order['id'] }}</td>
                <td>{{ $order['status'] }}</td>
                <td>{{ $order['payment_method'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
