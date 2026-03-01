<?php

namespace Tests\Feature;

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
}
