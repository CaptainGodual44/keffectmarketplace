<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\LslBridge\Services\NonceStore;
use App\Domain\LslBridge\Services\SignatureValidator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LslPurchaseIntentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

final class LslPurchaseIntentController extends Controller
{
    private const ALLOWED_CLOCK_SKEW_SECONDS = 300;

    public function __invoke(
        LslPurchaseIntentRequest $request,
        SignatureValidator $validator,
        NonceStore $nonceStore,
    ): JsonResponse {
        $objectId = (string) $request->header('X-LSL-OBJECT-ID', '');
        $timestamp = (string) $request->header('X-LSL-TIMESTAMP', '');
        $nonce = (string) $request->header('X-LSL-NONCE', '');
        $signature = (string) $request->header('X-LSL-SIGNATURE', '');

        $object = DB::table('lsl_objects')->where('object_uuid', $objectId)->where('active', true)->first();

        if (!$object) {
            return response()->json(['message' => 'Unknown LSL object'], 403);
        }

        $timestampValidation = $validator->validateTimestamp($timestamp, self::ALLOWED_CLOCK_SKEW_SECONDS);
        if (!$timestampValidation['valid']) {
            $drift = $timestampValidation['drift_seconds'];

            return response()->json([
                'message' => 'Stale timestamp',
                'detail' => $drift === null
                    ? 'Timestamp must be a valid unix epoch string.'
                    : sprintf(
                        'Request clock drift (%d seconds) exceeds allowed skew (%d seconds).',
                        $drift,
                        self::ALLOWED_CLOCK_SKEW_SECONDS,
                    ),
            ], 422);
        }

        if ($nonce === '') {
            return response()->json(['message' => 'Missing nonce'], 422);
        }

        $payload = (string) $request->getContent();

        try {
            $secret = Crypt::decryptString((string) ($object->shared_secret_encrypted ?? ''));
        } catch (\Throwable) {
            return response()->json([
                'message' => 'LSL object secret is not configured for HMAC validation',
            ], 500);
        }

        if (!$validator->isValid($payload, $timestamp, $nonce, $signature, $secret)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        if (!$nonceStore->claim($objectId, $nonce, (int) $timestamp, self::ALLOWED_CLOCK_SKEW_SECONDS)) {
            return response()->json(['message' => 'Replay detected: nonce already used'], 409);
        }

        $intentId = (string) str()->uuid();

        DB::table('payment_intents')->insert([
            'intent_uuid' => $intentId,
            'user_uuid' => $request->string('avatar_id')->toString(),
            'order_id' => null,
            'amount' => (int) $request->integer('amount'),
            'currency' => 'L$',
            'status' => 'pending_authorized_debit',
            'nonce' => $nonce,
            'provider_txn_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'intent_id' => $intentId,
            'status' => 'pending_authorized_debit',
            'message' => 'Permission granted, waiting for payment confirmation',
        ]);
    }
}
