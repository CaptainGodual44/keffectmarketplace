<?php

declare(strict_types=1);

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = Cart::query()->firstOrCreate(['user_id' => $request->user()->id]);
        $cart->load('items.product');

        $total = $cart->items->sum(fn ($item) => $item->quantity * ($item->product->price_linden ?? 0));

        return view('storefront.cart.index', ['cart' => $cart, 'total' => $total]);
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate(['quantity' => ['nullable', 'integer', 'min:1', 'max:99']]);
        $qty = (int) ($validated['quantity'] ?? 1);

        $cart = Cart::query()->firstOrCreate(['user_id' => $request->user()->id]);
        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = ($item->exists ? $item->quantity : 0) + $qty;
        $item->save();

        return redirect()->route('storefront.cart.index')->with('status', 'Added to cart.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $user = $request->user();
        $cart = Cart::query()->firstOrCreate(['user_id' => $user->id]);
        $cart->load('items.product');

        abort_if($cart->items->isEmpty(), 422, 'Cart is empty.');

        DB::transaction(function () use ($cart, $user): void {
            $total = $cart->items->sum(fn ($item) => $item->quantity * ($item->product->price_linden ?? 0));

            $order = Order::query()->create([
                'order_number' => 'ORD-'.str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'status' => 'pending_authorized_debit',
                'payment_method' => 'L$',
                'total_linden' => $total,
            ]);

            foreach ($cart->items as $item) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => (string) $item->product->name,
                    'product_sku' => (string) $item->product->sku,
                    'unit_price_linden' => (int) $item->product->price_linden,
                    'quantity' => (int) $item->quantity,
                    'line_total_linden' => (int) ($item->quantity * $item->product->price_linden),
                ]);
            }

            $cart->items()->delete();
        });

        return redirect()->route('storefront.account.dashboard')->with('status', 'Order created and awaiting L$ authorization.');
    }
}
