<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;

final class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->paginate(12);

        return view('storefront.products.index', compact('products'));
    }

    public function show(string $sku): View
    {
        $product = Product::query()
            ->where('sku', $sku)
            ->firstOrFail();

        return view('storefront.products.show', compact('product'));
    }
}
