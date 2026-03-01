<?php

declare(strict_types=1);

namespace App\Domain\Payments\Services;

use Illuminate\Support\Facades\DB;

final class LindenWebhookService
{
    public function markTransactionProcessed(string $intentId, string $providerTxnId, string $payloadHash): bool
    {
        $existing = DB::table('payment_webhook_events')
            ->where('provider_txn_id', $providerTxnId)
            ->exists();

        if ($existing) {
            return false;
        }

        DB::table('payment_webhook_events')->insert([
            'event_id' => (string) str()->uuid(),
            'intent_uuid' => $intentId,
            'provider_txn_id' => $providerTxnId,
            'payload_hash' => $payloadHash,
            'processed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }
}
