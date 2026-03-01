<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_to_cart_and_checkout(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $product = Product::query()->create([
            'sku' => 'PROD-T-1',
            'name' => 'Test Product',
            'description' => 'Desc',
            'price_linden' => 200,
            'status' => 'active',
            'featured' => false,
        ]);

        $this->actingAs($user)
            ->post(route('storefront.cart.add', $product), ['quantity' => 2])
            ->assertRedirect(route('storefront.cart.index'));

        $this->actingAs($user)
            ->post(route('storefront.cart.checkout'))
            ->assertRedirect(route('storefront.account.dashboard'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => 'L$',
            'status' => 'pending_authorized_debit',
            'total_linden' => 400,
        ]);

        $this->assertDatabaseCount('order_items', 1);
    }

    public function test_non_active_products_cannot_be_added_to_cart(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $draftProduct = Product::query()->create([
            'sku' => 'PROD-DRAFT-1',
            'name' => 'Draft Product',
            'description' => 'Desc',
            'price_linden' => 100,
            'status' => 'draft',
            'featured' => false,
        ]);

        $this->actingAs($user)
            ->post(route('storefront.cart.add', $draftProduct), ['quantity' => 1])
            ->assertNotFound();

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_checkout_is_idempotent_when_submitted_twice(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $product = Product::query()->create([
            'sku' => 'PROD-T-2',
            'name' => 'Checkout Product',
            'description' => 'Desc',
            'price_linden' => 200,
            'status' => 'active',
            'featured' => false,
        ]);

        $this->actingAs($user)
            ->post(route('storefront.cart.add', $product), ['quantity' => 1])
            ->assertRedirect(route('storefront.cart.index'));

        $this->actingAs($user)
            ->post(route('storefront.cart.checkout'))
            ->assertRedirect(route('storefront.account.dashboard'));

        $this->actingAs($user)
            ->post(route('storefront.cart.checkout'))
            ->assertStatus(422);

        $this->assertSame(1, Order::query()->where('user_id', $user->id)->count());
    }

    public function test_order_items_are_retained_when_product_is_deleted(): void
    {
        $user = User::factory()->create(['role' => 'customer', 'status' => 'active']);
        $product = Product::query()->create([
            'sku' => 'PROD-T-3',
            'name' => 'Snapshot Product',
            'description' => 'Desc',
            'price_linden' => 150,
            'status' => 'active',
            'featured' => false,
        ]);

        $this->actingAs($user)
            ->post(route('storefront.cart.add', $product), ['quantity' => 2])
            ->assertRedirect(route('storefront.cart.index'));

        $this->actingAs($user)
            ->post(route('storefront.cart.checkout'))
            ->assertRedirect(route('storefront.account.dashboard'));

        $product->delete();

        $this->assertDatabaseCount('order_items', 1);
        $this->assertDatabaseHas('order_items', [
            'product_id' => null,
            'product_name' => 'Snapshot Product',
            'product_sku' => 'PROD-T-3',
            'quantity' => 2,
            'line_total_linden' => 300,
        ]);
    }
}
