<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class OrderManagementController extends Controller
{
    public function index(): View
    {
        $orders = [
            ['id' => 'ORD-1001', 'status' => 'paid', 'payment_method' => 'L$'],
            ['id' => 'ORD-1002', 'status' => 'pending_authorized_debit', 'payment_method' => 'L$'],
        ];

        return view('admin.orders.index', compact('orders'));
    }
}
