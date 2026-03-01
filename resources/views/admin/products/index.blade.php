@extends('layouts.app')

@section('content')
    <h1>Admin • Products</h1>
    <table>
        <thead><tr><th>SKU</th><th>Name</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($products as $product)
            <tr>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->status }}</td>
            </tr>
        @empty
            <tr><td colspan="3">No products found.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem;">
        {{ $products->links() }}
    </div>
@endsection
