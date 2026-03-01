# Linden Dollar (L$) Payment Design with LSL Authorization

## Goals

- Use L$ as primary virtual currency payment flow.
- Ensure **explicit debit permission** is requested via LSL before transaction processing.
- Validate incoming webhook/event notifications before marking orders as paid.

## Flow

1. LSL vendor object receives `touch_start` from avatar.
2. Script requests debit permission from user using LSL permissions model.
3. On permission grant, object sends signed purchase-intent payload to Laravel.
4. Laravel creates `pending` payment record with idempotency key.
5. Debit transaction is initiated/recorded through integrated L$ transaction mechanism.
6. Provider/webhook callback arrives at Laravel endpoint.
7. Backend validates:
   - Signature/secret
   - Timestamp tolerance
   - Nonce uniqueness
   - Amount/product correlation
8. Payment transitions to `paid` and order fulfillment event is dispatched.
9. LSL object receives delivery-ready status and confirms completion.

## Anti-Fraud Requirements

- Reject stale timestamp beyond allowed window.
- Reject reused nonces.
- Require transaction amount and currency match expected order total.
- Enforce idempotent webhook processing by `provider_txn_id`.

## Data Model (minimum)

- `lsl_objects` (object_uuid, owner_uuid, shared_secret_hash, active)
- `payment_intents` (intent_uuid, user_uuid, order_id, amount, currency=L$, status)
- `payment_webhook_events` (event_id, provider_txn_id, payload_hash, processed_at)
- `lsl_delivery_receipts` (order_id, object_uuid, delivered_at, confirmation_payload)

## Operational Recommendations

- Queue webhook handling to avoid request timeouts.
- Add dead-letter queue for failed webhook parse/validation jobs.
- Emit alerts on repeated failed signature validations.
