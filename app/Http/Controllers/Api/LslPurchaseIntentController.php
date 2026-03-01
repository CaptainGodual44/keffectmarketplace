<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\LslBridge\Services\SignatureValidator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LslPurchaseIntentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

final class LslPurchaseIntentController extends Controller
{
    public function __invoke(LslPurchaseIntentRequest $request, SignatureValidator $validator): JsonResponse
    {
        $objectId = (string) $request->header('X-LSL-OBJECT-ID', '');
        $timestamp = (string) $request->header('X-LSL-TIMESTAMP', '');
        $nonce = (string) $request->header('X-LSL-NONCE', '');
        $signature = (string) $request->header('X-LSL-SIGNATURE', '');

        if ($objectId === '' || $timestamp === '' || $nonce === '' || $signature === '') {
            return response()->json(['message' => 'Missing required LSL signature headers'], 422);
        }

        $object = DB::table('lsl_objects')->where('object_uuid', $objectId)->where('active', true)->first();

        if (!$object) {
            return response()->json(['message' => 'Unknown LSL object'], 403);
        }

        if (!$validator->timestampWithinTolerance($timestamp)) {
            return response()->json(['message' => 'Stale timestamp'], 422);
        }

        $existingNonce = DB::table('lsl_request_nonces')
            ->where('object_uuid', $objectId)
            ->where('nonce', $nonce)
            ->exists();

        if ($existingNonce) {
            return response()->json(['message' => 'Replay detected'], 409);
        }

        $payload = (string) $request->getContent();
        $secret = (string) $object->shared_secret_hash;

        if (!$validator->isValid($payload, $timestamp, $nonce, $signature, $secret)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        DB::table('lsl_request_nonces')->insert([
            'object_uuid' => $objectId,
            'nonce' => $nonce,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $intentId = (string) str()->uuid();

        DB::table('payment_intents')->insert([
            'intent_uuid' => $intentId,
            'user_uuid' => $request->string('avatar_id')->toString(),
            'order_id' => null,
            'amount' => (int) $request->integer('amount'),
            'currency' => 'L$',
            'status' => 'pending_authorized_debit',
            'nonce' => $nonce,
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
