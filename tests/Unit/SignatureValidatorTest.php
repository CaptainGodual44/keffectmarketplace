<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\LslBridge\Services\SignatureValidator;
use PHPUnit\Framework\TestCase;

final class SignatureValidatorTest extends TestCase
{
    public function test_accepts_valid_hmac_sha256_signature_for_canonical_payload_timestamp_nonce(): void
    {
        $validator = new SignatureValidator();
        $payload = '{"avatar_id":"u-1","product_sku":"PROD-001","quantity":1,"currency":"L$","amount":150}';
        $timestamp = '1710000000';
        $nonce = 'abc123';
        $secret = 'super-secret';
        $signature = hash_hmac('sha256', $payload . '|' . $timestamp . '|' . $nonce, $secret);

        self::assertTrue($validator->isValid($payload, $timestamp, $nonce, $signature, $secret));
    }

    public function test_rejects_signature_when_payload_changes(): void
    {
        $validator = new SignatureValidator();
        $payload = '{"avatar_id":"u-1","product_sku":"PROD-001","quantity":1,"currency":"L$","amount":150}';
        $tamperedPayload = '{"avatar_id":"u-1","product_sku":"PROD-002","quantity":1,"currency":"L$","amount":150}';
        $timestamp = '1710000000';
        $nonce = 'abc123';
        $secret = 'super-secret';
        $signature = hash_hmac('sha256', $payload . '|' . $timestamp . '|' . $nonce, $secret);

        self::assertFalse($validator->isValid($tamperedPayload, $timestamp, $nonce, $signature, $secret));
    }

    public function test_rejects_non_hex_signature(): void
    {
        $validator = new SignatureValidator();

        self::assertFalse($validator->isValid('{"amount":150}', '1710000000', 'abc123', 'this-is-not-hex', 'super-secret'));
    }

    public function test_accepts_uppercase_hex_signature_by_normalizing_input(): void
    {
        $validator = new SignatureValidator();
        $payload = '{"amount":150}';
        $timestamp = '1710000000';
        $nonce = 'abc123';
        $secret = 'super-secret';
        $signature = strtoupper(hash_hmac('sha256', $payload . '|' . $timestamp . '|' . $nonce, $secret));

        self::assertTrue($validator->isValid($payload, $timestamp, $nonce, $signature, $secret));
    }
}
