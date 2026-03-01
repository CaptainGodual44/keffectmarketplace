<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Payments\Services\LindenWebhookService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class LindenWebhookController extends Controller
{
    public function __invoke(Request $request, LindenWebhookService $webhookService): JsonResponse
    {
        $signature = (string) $request->header('X-LINDEN-SIGNATURE', '');
        $payload = (string) $request->getContent();
        $expected = hash_hmac('sha256', $payload, (string) config('services.linden.webhook_secret'));

        if (!hash_equals($expected, $signature)) {
            return response()->json(['message' => 'Invalid webhook signature'], 403);
        }

        $providerTxnId = (string) $request->input('provider_txn_id');

        if ($providerTxnId === '') {
            return response()->json(['message' => 'Missing provider_txn_id'], 422);
        }

        $intentId = (string) $request->input('intent_id');
        if ($intentId === '') {
            return response()->json(['message' => 'Missing intent_id'], 422);
        }

        $intentExists = DB::table('payment_intents')
            ->where('intent_uuid', $intentId)
            ->exists();

        if (!$intentExists) {
            return response()->json(['message' => 'Unknown intent_id'], 422);
        }

        $processed = $webhookService->markTransactionProcessed($intentId, $providerTxnId, hash('sha256', $payload));

        if (!$processed) {
            return response()->json(['message' => 'Duplicate webhook ignored'], 202);
        }

        $updated = DB::table('payment_intents')
            ->where('intent_uuid', $intentId)
            ->update([
                'status' => 'paid',
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            DB::table('payment_webhook_events')
                ->where('provider_txn_id', $providerTxnId)
                ->delete();

            return response()->json(['message' => 'Unknown intent_id'], 422);
        }

        return response()->json(['message' => 'Webhook processed']);
    }
}
