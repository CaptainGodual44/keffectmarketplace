@extends('layouts.app')

@section('content')
    <h1>{{ $product->name }}</h1>
    <p>SKU: {{ $product->sku }}</p>
    <p>{{ $product->description }}</p>
    <p><span class="badge">L${{ $product->price_linden }}</span></p>

    @auth
    <form method="POST" action="{{ route('storefront.cart.add', $product) }}">
        @csrf
        <label>Qty <input type="number" name="quantity" min="1" value="1" style="width:70px;"></label>
        <button type="submit">Add to cart</button>
    </form>
    @else
        <p><a href="{{ route('login') }}">Login</a> to add to cart.</p>
    @endauth
@endsection
