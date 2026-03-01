<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredProducts = [
            ['name' => 'Starter Avatar Outfit', 'price_l$' => 150],
            ['name' => 'Virtual Home Decor Pack', 'price_l$' => 320],
            ['name' => 'Marketplace Creator Tools', 'price_l$' => 500],
        ];

        return view('storefront.home', ['featuredProducts' => $featuredProducts]);
    }
}
