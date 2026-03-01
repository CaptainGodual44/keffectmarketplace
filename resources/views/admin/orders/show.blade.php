@extends('layouts.app')

@section('content')
    <h1>Order {{ $order->order_number }}</h1>
    <p>Status: <strong>{{ $order->status }}</strong></p>
    <p>Customer: {{ $order->user?->email }}</p>
    <p>Total: L${{ $order->total_linden }}</p>

    @if(session('status'))
        <p class="card">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('admin.orders.status.update', $order) }}">
        @csrf
        @method('PATCH')
        <select name="status">
            @foreach(['pending_authorized_debit','paid','fulfilled','cancelled'] as $status)
                <option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <button type="submit">Update Status</button>
    </form>

    <h2>Items</h2>
    <table>
        <thead><tr><th>SKU</th><th>Name</th><th>Qty</th><th>Unit</th><th>Line</th></tr></thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_sku }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>L${{ $item->unit_price_linden }}</td>
                    <td>L${{ $item->line_total_linden }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
