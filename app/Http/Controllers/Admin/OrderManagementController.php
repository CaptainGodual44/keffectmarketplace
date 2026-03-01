<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class OrderManagementController extends Controller
{
    public function index(): View
    {
        $orders = Order::query()->with('user:id,name,email')->latest('id')->paginate(25);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load('user:id,name,email', 'items');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending_authorized_debit', 'paid', 'fulfilled', 'cancelled'])],
        ]);

        $order->update(['status' => $validated['status']]);

        return redirect()->route('admin.orders.show', $order)->with('status', 'Order status updated.');
    }
}
