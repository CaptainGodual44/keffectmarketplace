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
        $signature = strtolower((string) $request->header('X-LINDEN-SIGNATURE', ''));
        $payload = (string) $request->getContent();
        $expected = hash_hmac('sha256', $payload, (string) config('services.linden.webhook_secret'));

        if ($signature === '' || !ctype_xdigit($signature) || !hash_equals($expected, $signature)) {
            return response()->json(['message' => 'Invalid webhook signature'], 403);
        }

        $intentId = (string) $request->input('intent_id');
        $providerTxnId = (string) $request->input('provider_txn_id');
        $amount = (int) $request->input('amount');
        $currency = (string) $request->input('currency');

        if ($providerTxnId === '' || $intentId === '') {
            return response()->json(['message' => 'Missing intent_id or provider_txn_id'], 422);
        }

        return DB::transaction(function () use ($intentId, $providerTxnId, $amount, $currency, $payload, $webhookService): JsonResponse {
            $intent = DB::table('payment_intents')
                ->where('intent_uuid', $intentId)
                ->lockForUpdate()
                ->first();

            if (!$intent) {
                return response()->json(['message' => 'Unknown payment intent'], 404);
            }

            if ((int) $intent->amount !== $amount || (string) $intent->currency !== $currency) {
                return response()->json([
                    'message' => 'Webhook payload does not match payment intent fields',
                ], 422);
            }

            if ((string) $intent->intent_uuid !== $intentId) {
                return response()->json(['message' => 'intent_id mismatch'], 422);
            }

            if ($intent->provider_txn_id !== null && (string) $intent->provider_txn_id !== $providerTxnId) {
                return response()->json([
                    'message' => 'provider_txn_id mismatch for this intent',
                ], 409);
            }

            $processed = $webhookService->markTransactionProcessed($intentId, $providerTxnId, hash('sha256', $payload));
            if (!$processed) {
                return response()->json(['message' => 'Duplicate webhook ignored'], 202);
            }

            DB::table('payment_intents')
                ->where('intent_uuid', $intentId)
                ->update([
                    'status' => 'paid',
                    'provider_txn_id' => $providerTxnId,
                    'updated_at' => now(),
                ]);

            return response()->json(['message' => 'Webhook processed']);
        });
    }
}
