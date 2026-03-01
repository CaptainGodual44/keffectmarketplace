<?php

declare(strict_types=1);

namespace App\Domain\LslBridge\Services;

final class SignatureValidator
{
    public function isValid(string $payload, string $timestamp, string $nonce, string $signature, string $secret): bool
    {
        if ($signature === '' || !ctype_xdigit($signature)) {
            return false;
        }

        $computed = hash_hmac('sha256', $this->canonicalSigningString($payload, $timestamp, $nonce), $secret);

        return hash_equals($computed, strtolower($signature));
    }

    public function timestampWithinTolerance(string $timestamp, int $allowedSkewSeconds = 300): bool
    {
        if (!ctype_digit($timestamp)) {
            return false;
        }

        $ts = (int) $timestamp;

        return abs(time() - $ts) <= $allowedSkewSeconds;
    }

    private function canonicalSigningString(string $payload, string $timestamp, string $nonce): string
    {
        return $payload . '|' . $timestamp . '|' . $nonce;
    }
}
