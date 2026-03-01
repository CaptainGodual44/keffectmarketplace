<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class PaymentEndpointSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_replayed_nonce_for_same_object(): void
    {
        $objectUuid = (string) str()->uuid();
        DB::table('lsl_objects')->insert([
            'object_uuid' => $objectUuid,
            'owner_uuid' => (string) str()->uuid(),
            'shared_secret_hash' => hash('sha256', 'legacy-secret'),
            'shared_secret_encrypted' => Crypt::encryptString('live-secret'),
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $timestamp = (string) time();
        $nonce = 'nonce-123';
        $payload = [
            'avatar_id' => (string) str()->uuid(),
            'product_sku' => 'SKU-1',
            'quantity' => 1,
            'currency' => 'L$',
            'amount' => 150,
        ];
        $json = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $json . '|' . $timestamp . '|' . $nonce, 'live-secret');

        $headers = [
            'X-LSL-OBJECT-ID' => $objectUuid,
            'X-LSL-TIMESTAMP' => $timestamp,
            'X-LSL-NONCE' => $nonce,
            'X-LSL-SIGNATURE' => $signature,
        ];

        $this->postJson('/api/lsl/purchase-intent', $payload, $headers)->assertOk();
        $this->postJson('/api/lsl/purchase-intent', $payload, $headers)
            ->assertStatus(409)
            ->assertJsonPath('message', 'Replay detected: nonce already used');
    }

    public function test_rejects_stale_timestamp_with_clock_drift_message(): void
    {
        $objectUuid = (string) str()->uuid();
        DB::table('lsl_objects')->insert([
            'object_uuid' => $objectUuid,
            'owner_uuid' => (string) str()->uuid(),
            'shared_secret_hash' => hash('sha256', 'legacy-secret'),
            'shared_secret_encrypted' => Crypt::encryptString('live-secret'),
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $timestamp = (string) (time() - 3600);
        $nonce = 'nonce-123';
        $payload = [
            'avatar_id' => (string) str()->uuid(),
            'product_sku' => 'SKU-1',
            'quantity' => 1,
            'currency' => 'L$',
            'amount' => 150,
        ];
        $json = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $json . '|' . $timestamp . '|' . $nonce, 'live-secret');

        $this->postJson('/api/lsl/purchase-intent', $payload, [
            'X-LSL-OBJECT-ID' => $objectUuid,
            'X-LSL-TIMESTAMP' => $timestamp,
            'X-LSL-NONCE' => $nonce,
            'X-LSL-SIGNATURE' => $signature,
        ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Stale timestamp')
            ->assertJsonPath('detail', fn (string $detail): bool => str_contains($detail, 'exceeds allowed skew'));
    }

    public function test_rejects_bad_webhook_signature(): void
    {
        config()->set('services.linden.webhook_secret', 'expected-secret');

        $this->postJson('/api/payments/linden/webhook', [
            'intent_id' => (string) str()->uuid(),
            'provider_txn_id' => 'txn-1',
            'amount' => 150,
            'currency' => 'L$',
        ], [
            'X-LINDEN-SIGNATURE' => 'deadbeef',
        ])->assertStatus(403)
            ->assertJsonPath('message', 'Invalid webhook signature');
    }

    public function test_rejects_webhook_when_secret_is_unset(): void
    {
        config()->set('services.linden.webhook_secret', '   ');

        $this->postJson('/api/payments/linden/webhook', [
            'intent_id' => (string) str()->uuid(),
            'provider_txn_id' => 'txn-1',
            'amount' => 150,
            'currency' => 'L$',
        ], [
            'X-LINDEN-SIGNATURE' => hash_hmac('sha256', '{"intent_id":"fake"}', ''),
        ])->assertStatus(503)
            ->assertJsonPath('message', 'Webhook signing secret is not configured');
    }

    public function test_duplicate_webhook_is_idempotent(): void
    {
        config()->set('services.linden.webhook_secret', 'expected-secret');

        $intentUuid = (string) str()->uuid();
        $payload = [
            'intent_id' => $intentUuid,
            'provider_txn_id' => 'txn-123',
            'amount' => 150,
            'currency' => 'L$',
        ];

        DB::table('payment_intents')->insert([
            'intent_uuid' => $intentUuid,
            'user_uuid' => (string) str()->uuid(),
            'order_id' => null,
            'amount' => 150,
            'currency' => 'L$',
            'status' => 'pending_authorized_debit',
            'provider_txn_id' => null,
            'nonce' => 'n-1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $json = json_encode($payload, JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $json, 'expected-secret');

        $headers = ['X-LINDEN-SIGNATURE' => $signature];

        $this->postJson('/api/payments/linden/webhook', $payload, $headers)
            ->assertOk();

        $this->postJson('/api/payments/linden/webhook', $payload, $headers)
            ->assertStatus(202)
            ->assertJsonPath('message', 'Duplicate webhook ignored');
    }
}
