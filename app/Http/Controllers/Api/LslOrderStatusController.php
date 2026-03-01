<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

final class LslOrderStatusController extends Controller
{
    public function __invoke(string $intentId): JsonResponse
    {
        $intent = DB::table('payment_intents')->where('intent_uuid', $intentId)->first();

        if (!$intent) {
            return response()->json(['message' => 'Intent not found'], 404);
        }

        return response()->json([
            'intent_id' => $intentId,
            'payment_status' => $intent->status,
            'delivery_status' => $intent->status === 'paid' ? 'ready' : 'pending',
        ]);
    }
}
