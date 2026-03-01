@extends('layouts.app')

@section('content')
    <h1>{{ $product->name }}</h1>
    <p>SKU: {{ $product->sku }}</p>
    <p>{{ $product->description }}</p>
    <p><span class="badge">L${{ $product->price_linden }}</span></p>
    <button>Add to cart</button>
@endsection
