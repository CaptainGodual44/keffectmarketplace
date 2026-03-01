<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'public_uuid' => (string) Str::uuid(),
                'name' => 'Admin User',
                'password' => 'password',
                'role' => 'admin',
                'status' => 'active',
            ],
        );

        $customer = User::query()->updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'public_uuid' => (string) Str::uuid(),
                'name' => 'Customer User',
                'password' => 'password',
                'role' => 'customer',
                'status' => 'active',
            ],
        );

        Product::query()->upsert([
            [
                'sku' => 'PROD-001',
                'name' => 'Starter Avatar Outfit',
                'description' => 'Starter pack for avatar customization.',
                'price_linden' => 150,
                'status' => 'active',
                'featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'PROD-002',
                'name' => 'Virtual Home Decor Pack',
                'description' => 'Decor bundle for virtual spaces.',
                'price_linden' => 320,
                'status' => 'active',
                'featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sku' => 'PROD-003',
                'name' => 'Marketplace Creator Tools',
                'description' => 'Tools for advanced creators.',
                'price_linden' => 500,
                'status' => 'active',
                'featured' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['sku'], ['name', 'description', 'price_linden', 'status', 'featured', 'updated_at']);

        Order::query()->upsert([
            [
                'order_number' => 'ORD-1001',
                'user_id' => $customer->id,
                'status' => 'paid',
                'payment_method' => 'L$',
                'total_linden' => 350,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'order_number' => 'ORD-1002',
                'user_id' => $customer->id,
                'status' => 'pending_authorized_debit',
                'payment_method' => 'L$',
                'total_linden' => 120,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['order_number'], ['status', 'payment_method', 'total_linden', 'updated_at']);

        // Keep example test user for compatibility.
        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'public_uuid' => (string) Str::uuid(),
                'name' => 'Test User',
                'password' => 'password',
                'role' => 'customer',
                'status' => 'active',
            ],
        );
    }
}
