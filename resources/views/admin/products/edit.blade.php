@extends('layouts.app')

@section('content')
    <h1>Edit Product</h1>
    @include('admin.products.partials.form', ['action' => route('admin.products.update', $product), 'method' => 'PUT', 'product' => $product])
@endsection
