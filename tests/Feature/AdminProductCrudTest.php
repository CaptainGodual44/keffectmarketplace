<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->actingAs($admin)->post(route('admin.products.store'), [
            'sku' => 'PROD-NEW',
            'name' => 'New Product',
            'description' => 'Test',
            'price_linden' => 333,
            'status' => 'active',
            'featured' => 1,
        ])->assertRedirect(route('admin.products.index'));

        $product = Product::query()->where('sku', 'PROD-NEW')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'sku' => 'PROD-NEW',
            'name' => 'Updated Product',
            'description' => 'Updated',
            'price_linden' => 555,
            'status' => 'draft',
            'featured' => 0,
        ])->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price_linden' => 555,
            'status' => 'draft',
        ]);
    }
}
