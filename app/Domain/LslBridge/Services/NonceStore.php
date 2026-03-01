<?php

declare(strict_types=1);

namespace App\Domain\LslBridge\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

final class NonceStore
{
    public function claim(string $objectUuid, string $nonce, int $timestamp, int $windowSeconds = 300): bool
    {
        DB::table('lsl_request_nonces')
            ->where('expires_at', '<', now())
            ->delete();

        $expiresAt = now()->addSeconds($windowSeconds);

        try {
            DB::table('lsl_request_nonces')->insert([
                'object_uuid' => $objectUuid,
                'nonce' => $nonce,
                'request_timestamp' => $timestamp,
                'expires_at' => $expiresAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        } catch (QueryException $queryException) {
            if ($this->isUniqueConstraintViolation($queryException)) {
                return false;
            }

            throw $queryException;
        }
    }

    private function isUniqueConstraintViolation(QueryException $queryException): bool
    {
        $sqlState = $queryException->errorInfo[0] ?? null;

        return in_array($sqlState, ['23000', '23505'], true);
    }
}
