<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;

final class ProductManagementController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->latest('id')
            ->paginate(25);

        return view('admin.products.index', compact('products'));
    }
}
