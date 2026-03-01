<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LslPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_intent_accepts_valid_signed_request(): void
    {
        $objectId = (string) Str::uuid();
        $secret = 'local-test-secret';

        \DB::table('lsl_objects')->insert([
            'object_uuid' => $objectId,
            'owner_uuid' => (string) Str::uuid(),
            'shared_secret_hash' => $secret,
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $timestamp = (string) time();
        $nonce = 'nonce-123';
        $payload = [
            'avatar_id' => (string) Str::uuid(),
            'product_sku' => 'PROD-001',
            'quantity' => 1,
            'currency' => 'L$',
            'amount' => 150,
        ];

        $encoded = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $encoded.'|'.$timestamp.'|'.$nonce, $secret);

        $response = $this->withHeaders([
            'X-LSL-OBJECT-ID' => $objectId,
            'X-LSL-TIMESTAMP' => $timestamp,
            'X-LSL-NONCE' => $nonce,
            'X-LSL-SIGNATURE' => $signature,
        ])->postJson('/api/lsl/purchase-intent', $payload);

        $response->assertOk()->assertJsonPath('status', 'pending_authorized_debit');
    }

    public function test_purchase_intent_rejects_replayed_nonce(): void
    {
        $objectId = (string) Str::uuid();
        $secret = 'local-test-secret';

        \DB::table('lsl_objects')->insert([
            'object_uuid' => $objectId,
            'owner_uuid' => (string) Str::uuid(),
            'shared_secret_hash' => $secret,
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $timestamp = (string) time();
        $nonce = 'replay-nonce';
        $payload = [
            'avatar_id' => (string) Str::uuid(),
            'product_sku' => 'PROD-001',
            'quantity' => 1,
            'currency' => 'L$',
            'amount' => 150,
        ];
        $encoded = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $encoded.'|'.$timestamp.'|'.$nonce, $secret);

        $headers = [
            'X-LSL-OBJECT-ID' => $objectId,
            'X-LSL-TIMESTAMP' => $timestamp,
            'X-LSL-NONCE' => $nonce,
            'X-LSL-SIGNATURE' => $signature,
        ];

        $this->withHeaders($headers)->postJson('/api/lsl/purchase-intent', $payload)->assertOk();
        $this->withHeaders($headers)->postJson('/api/lsl/purchase-intent', $payload)->assertStatus(409);
    }

    public function test_webhook_rejects_amount_mismatch(): void
    {
        config()->set('services.linden.webhook_secret', 'webhook-secret');

        $intentId = (string) Str::uuid();
        \DB::table('payment_intents')->insert([
            'intent_uuid' => $intentId,
            'user_uuid' => (string) Str::uuid(),
            'order_id' => null,
            'amount' => 150,
            'currency' => 'L$',
            'status' => 'pending_authorized_debit',
            'nonce' => 'n1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $payload = [
            'provider_txn_id' => 'txn-1',
            'intent_id' => $intentId,
            'amount' => 999,
            'currency' => 'L$',
        ];

        $encoded = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $encoded, 'webhook-secret');

        $this->withHeaders(['X-LINDEN-SIGNATURE' => $signature])
            ->postJson('/api/payments/linden/webhook', $payload)
            ->assertStatus(422);
    }
}
