<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredProducts = Product::query()
            ->where('status', 'active')
            ->where('featured', true)
            ->latest('id')
            ->take(6)
            ->get();

        return view('storefront.home', ['featuredProducts' => $featuredProducts]);
    }
}
