<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class AccountController extends Controller
{
    public function dashboard(): View
    {
        $orders = [
            ['id' => 'ORD-1001', 'status' => 'paid', 'total_l$' => 350],
            ['id' => 'ORD-1002', 'status' => 'pending', 'total_l$' => 120],
        ];

        return view('storefront.account.dashboard', compact('orders'));
    }
}
