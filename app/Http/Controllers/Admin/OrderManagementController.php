<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;

final class OrderManagementController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()
            ->with('user:id,name,email')
            ->latest('id')
            ->paginate(25);

        return view('admin.orders.index', compact('orders'));
    }
}
