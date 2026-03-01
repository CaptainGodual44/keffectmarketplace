# Linden Dollar (L$) Payment Design with LSL Authorization

## Goals

- Use L$ as primary virtual currency payment flow.
- Ensure **explicit debit permission** is requested via LSL before transaction processing.
- Validate incoming webhook/event notifications before marking orders as paid.

## Canonical Request Signature

Purchase intent requests to `/api/lsl/purchase-intent` use:

`HMAC_SHA256(payload + "|" + timestamp + "|" + nonce, shared_secret)`

Headers required:
- `X-LSL-OBJECT-ID`
- `X-LSL-TIMESTAMP`
- `X-LSL-NONCE`
- `X-LSL-SIGNATURE`

## Flow

1. LSL vendor object receives `touch_start` from avatar.
2. Script requests debit permission from user using LSL permissions model.
3. On permission grant, object sends signed purchase-intent payload to Laravel.
4. Laravel validates timestamp + signature and rejects replayed nonces.
5. Laravel creates `pending_authorized_debit` payment intent.
6. Provider/webhook callback arrives at Laravel endpoint.
7. Backend validates:
   - Webhook signature
   - Intent existence
   - Amount and currency (`L$`) match intent values
   - Webhook idempotency by provider transaction id
8. Payment transitions to `paid` and order fulfillment event is dispatched.
9. LSL object receives delivery-ready status and confirms completion.

## Anti-Fraud Requirements

- Reject stale timestamp beyond allowed window.
- Reject reused nonces for each object UUID.
- Require transaction amount and currency match expected order total.
- Enforce idempotent webhook processing by `provider_txn_id`.

## Data Model (minimum)

- `lsl_objects` (object_uuid, owner_uuid, shared_secret_hash, active)
- `lsl_request_nonces` (object_uuid, nonce, expires_at)
- `payment_intents` (intent_uuid, user_uuid, order_id, amount, currency=L$, status)
- `payment_webhook_events` (event_id, provider_txn_id, payload_hash, processed_at)
- `lsl_delivery_receipts` (order_id, object_uuid, delivered_at, confirmation_payload)

## Operational Recommendations

- Queue webhook handling to avoid request timeouts.
- Add dead-letter queue for failed webhook parse/validation jobs.
- Emit alerts on repeated failed signature validations.
