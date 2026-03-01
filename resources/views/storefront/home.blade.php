@extends('layouts.app')

@section('content')
    <h1>Marketplace Home</h1>
    <p>Browse virtual goods, manage your account, and complete secure purchases in Linden Dollars (L$).</p>

    <div class="grid grid-3">
        @forelse($featuredProducts as $item)
            <div class="card">
                <h3>{{ $item->name }}</h3>
                <p><span class="badge">L${{ $item->price_linden }}</span></p>
                <a href="{{ route('storefront.products.show', $item->sku) }}">View details</a>
            </div>
        @empty
            <div class="card">No featured products yet.</div>
        @endforelse
    </div>
@endsection
