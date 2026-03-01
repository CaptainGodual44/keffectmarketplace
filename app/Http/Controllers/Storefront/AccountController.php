<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class AccountController extends Controller
{
    public function dashboard(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->latest('id')
            ->take(20)
            ->get();

        return view('storefront.account.dashboard', compact('orders'));
    }
}
