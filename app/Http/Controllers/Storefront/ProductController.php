<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class ProductController extends Controller
{
    public function index(): View
    {
        $products = [
            ['sku' => 'PROD-001', 'name' => 'Starter Avatar Outfit', 'price_l$' => 150],
            ['sku' => 'PROD-002', 'name' => 'Virtual Home Decor Pack', 'price_l$' => 320],
            ['sku' => 'PROD-003', 'name' => 'Creator Tools', 'price_l$' => 500],
        ];

        return view('storefront.products.index', compact('products'));
    }

    public function show(string $sku): View
    {
        $product = [
            'sku' => $sku,
            'name' => 'Sample Product ' . $sku,
            'description' => 'Product detail placeholder for full catalog integration.',
            'price_l$' => 199,
        ];

        return view('storefront.products.show', compact('product'));
    }
}
