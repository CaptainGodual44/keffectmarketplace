<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductUpsertRequest;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class ProductManagementController extends Controller
{
    public function index(): View
    {
        $products = Product::query()->latest('id')->paginate(25);
        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(ProductUpsertRequest $request): RedirectResponse
    {
        Product::query()->create($request->validated() + ['featured' => (bool) $request->boolean('featured')]);
        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(ProductUpsertRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated() + ['featured' => (bool) $request->boolean('featured')]);
        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('status', 'Product deleted.');
    }
}
