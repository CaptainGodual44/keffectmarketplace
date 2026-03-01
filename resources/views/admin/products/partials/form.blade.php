<form method="POST" action="{{ $action }}" class="card">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    <label>SKU <input type="text" name="sku" value="{{ old('sku', $product?->sku) }}" required></label><br>
    <label>Name <input type="text" name="name" value="{{ old('name', $product?->name) }}" required></label><br>
    <label>Description <textarea name="description">{{ old('description', $product?->description) }}</textarea></label><br>
    <label>Price (L$) <input type="number" name="price_linden" min="1" value="{{ old('price_linden', $product?->price_linden) }}" required></label><br>
    <label>Status
        <select name="status">
            @foreach(['active','draft','archived'] as $status)
                <option value="{{ $status }}" @selected(old('status', $product?->status ?? 'active') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </label><br>
    <label><input type="checkbox" name="featured" value="1" @checked(old('featured', $product?->featured))> Featured</label><br>

    <button type="submit">Save</button>
    <a href="{{ route('admin.products.index') }}">Cancel</a>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
</form>
