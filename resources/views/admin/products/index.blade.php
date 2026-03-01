@extends('layouts.app')

@section('content')
    <h1>Admin • Products</h1>
    <table>
        <thead><tr><th>SKU</th><th>Name</th><th>Status</th></tr></thead>
        <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{ $product['sku'] }}</td>
                <td>{{ $product['name'] }}</td>
                <td>{{ $product['status'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
