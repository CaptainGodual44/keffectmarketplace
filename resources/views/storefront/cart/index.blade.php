@extends('layouts.app')

@section('content')
    <h1>Your Cart</h1>

    @if(session('status'))
        <p class="card">{{ session('status') }}</p>
    @endif

    <table>
        <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Line Total</th></tr></thead>
        <tbody>
            @forelse($cart->items as $item)
                <tr>
                    <td>{{ $item->product?->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>L${{ $item->product?->price_linden }}</td>
                    <td>L${{ $item->quantity * ($item->product?->price_linden ?? 0) }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Your cart is empty.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p><strong>Total:</strong> L${{ $total }}</p>

    <form method="POST" action="{{ route('storefront.cart.checkout') }}">
        @csrf
        <button type="submit" @disabled($cart->items->isEmpty())>Checkout (Create L$ order)</button>
    </form>
@endsection
