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

    /**
     * @return array{valid: bool, drift_seconds: int|null}
     */
    public function validateTimestamp(string $timestamp, int $allowedSkewSeconds = 300): array
    {
        if (!ctype_digit($timestamp)) {
            return ['valid' => false, 'drift_seconds' => null];
        }

        $driftSeconds = abs(time() - (int) $timestamp);

        return [
            'valid' => $driftSeconds <= $allowedSkewSeconds,
            'drift_seconds' => $driftSeconds,
        ];
    }

    public function timestampWithinTolerance(string $timestamp, int $allowedSkewSeconds = 300): bool
    {
        return $this->validateTimestamp($timestamp, $allowedSkewSeconds)['valid'];
    }

    private function canonicalSigningString(string $payload, string $timestamp, string $nonce): string
    {
        return $payload . '|' . $timestamp . '|' . $nonce;
    }
}
