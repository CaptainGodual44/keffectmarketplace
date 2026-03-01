@extends('layouts.app')

@section('content')
    <h1>Products</h1>
    <div class="grid grid-3">
        @forelse($products as $product)
            <div class="card">
                <h3>{{ $product->name }}</h3>
                <p>SKU: {{ $product->sku }}</p>
                <p><span class="badge">L${{ $product->price_linden }}</span></p>
                <a href="{{ route('storefront.products.show', $product->sku) }}">View details</a>
            </div>
        @empty
            <div class="card">No active products found.</div>
        @endforelse
    </div>

    <div style="margin-top: 1rem;">
        {{ $products->links() }}
    </div>
@endsection
