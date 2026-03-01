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
        $webhookSecret = trim((string) config('services.linden.webhook_secret', ''));

        if ($webhookSecret === '') {
            return response()->json(['message' => 'Webhook signing secret is not configured'], 503);
        }

        $expected = hash_hmac('sha256', $payload, $webhookSecret);

        if (!hash_equals($expected, $signature)) {
            return response()->json(['message' => 'Invalid webhook signature'], 403);
        }

        $providerTxnId = (string) $request->input('provider_txn_id');
        $intentId = (string) $request->input('intent_id');
        $amount = (int) $request->input('amount', 0);
        $currency = (string) $request->input('currency', '');

        if ($providerTxnId === '' || $intentId === '') {
            return response()->json(['message' => 'Missing provider_txn_id or intent_id'], 422);
        }

        $intent = DB::table('payment_intents')->where('intent_uuid', $intentId)->first();
        if (!$intent) {
            return response()->json(['message' => 'Unknown payment intent'], 404);
        }

        if ($currency !== 'L$' || $amount !== (int) $intent->amount) {
            return response()->json(['message' => 'Amount/currency mismatch'], 422);
        }

        $processed = $webhookService->markTransactionProcessed($providerTxnId, hash('sha256', $payload));
        if (!$processed) {
            return response()->json(['message' => 'Duplicate webhook ignored'], 202);
        }

        DB::table('payment_intents')
            ->where('intent_uuid', $intentId)
            ->update([
                'status' => 'paid',
                'updated_at' => now(),
            ]);

        return response()->json(['message' => 'Webhook processed']);
    }
}
