@extends('layouts.app')

@section('content')
    <h1>Create Product</h1>
    @include('admin.products.partials.form', ['action' => route('admin.products.store'), 'method' => 'POST', 'product' => null])
@endsection
