@extends('layouts.app')

@section('content')
    <h1>Products</h1>
    <div class="grid grid-3">
        @foreach($products as $product)
            <div class="card">
                <h3>{{ $product['name'] }}</h3>
                <p>SKU: {{ $product['sku'] }}</p>
                <p><span class="badge">L${{ $product['price_l$'] }}</span></p>
                <a href="/products/{{ $product['sku'] }}">View details</a>
            </div>
        @endforeach
    </div>
@endsection
