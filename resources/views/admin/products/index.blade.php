@extends('layouts.app')

@section('content')
    <h1>Admin • Products</h1>
    <p><a href="{{ route('admin.products.create') }}">+ New Product</a></p>

    @if(session('status'))
        <p class="card">{{ session('status') }}</p>
    @endif

    <table>
        <thead><tr><th>SKU</th><th>Name</th><th>Status</th><th>Price</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($products as $product)
            <tr>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->status }}</td>
                <td>L${{ $product->price_linden }}</td>
                <td>
                    <a href="{{ route('admin.products.edit', $product) }}">Edit</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete product?')">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No products found.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem;">
        {{ $products->links() }}
    </div>
@endsection
