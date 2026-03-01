@extends('layouts.app')

@section('content')
    <h1>Products</h1>

    <form method="GET" action="{{ route('storefront.products.index') }}" class="card" style="margin-bottom:1rem;">
        <label>Search
            <input type="text" name="q" value="{{ $search }}" placeholder="Name or SKU">
        </label>
        <label style="margin-left:1rem;">Sort
            <select name="sort">
                <option value="name_asc" @selected($sort==='name_asc')>Name A-Z</option>
                <option value="price_asc" @selected($sort==='price_asc')>Price Low-High</option>
                <option value="price_desc" @selected($sort==='price_desc')>Price High-Low</option>
                <option value="newest" @selected($sort==='newest')>Newest</option>
            </select>
        </label>
        <button type="submit" style="margin-left:1rem;">Apply</button>
    </form>

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
