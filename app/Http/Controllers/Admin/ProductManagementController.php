<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class ProductManagementController extends Controller
{
    public function index(): View
    {
        $products = [
            ['sku' => 'PROD-001', 'name' => 'Starter Avatar Outfit', 'status' => 'active'],
            ['sku' => 'PROD-002', 'name' => 'Virtual Home Decor Pack', 'status' => 'draft'],
        ];

        return view('admin.products.index', compact('products'));
    }
}
