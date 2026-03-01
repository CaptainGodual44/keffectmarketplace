<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()->where('status', 'active');

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $sort = (string) $request->query('sort', 'name_asc');
        match ($sort) {
            'price_asc' => $query->orderBy('price_linden'),
            'price_desc' => $query->orderByDesc('price_linden'),
            'newest' => $query->latest('id'),
            default => $query->orderBy('name'),
        };

        $products = $query->paginate(12)->withQueryString();

        return view('storefront.products.index', compact('products', 'search', 'sort'));
    }

    public function show(string $sku): View
    {
        $product = Product::query()->where('sku', $sku)->firstOrFail();
        return view('storefront.products.show', compact('product'));
    }
}
