# LSL API Contract

## 1) Purchase Intent Endpoint

- **URL**: `/api/lsl/purchase-intent`
- **Method**: `POST`
- **Content-Type**: `application/json`
- **Headers**:
  - `X-LSL-OBJECT-ID`: UUID of the registered object (`lsl_objects.object_uuid`).
  - `X-LSL-TIMESTAMP`: Unix epoch seconds as a base-10 integer string.
  - `X-LSL-NONCE`: Unique per-request nonce string.
  - `X-LSL-SIGNATURE`: Lowercase hexadecimal HMAC-SHA256 signature.

### Signature Canonicalization (normative)

All producers and validators **must** use the exact same signing input:

```text
canonical_string = payload + "|" + timestamp + "|" + nonce
signature = hex(HMAC_SHA256(secret, canonical_string))
```

Rules:

1. `payload` is the exact raw HTTP body bytes sent on the wire (no reformatting, no key reordering).
2. `timestamp` is used exactly as sent in `X-LSL-TIMESTAMP`.
3. `nonce` is used exactly as sent in `X-LSL-NONCE`.
4. Delimiter is the literal ASCII pipe character (`|`) between each segment.
5. Signature encoding is lowercase hexadecimal and sent in `X-LSL-SIGNATURE`.
6. Secret is the per-object shared secret provisioned out-of-band and associated with `X-LSL-OBJECT-ID`.

### Body (JSON)

```json
{
  "avatar_id": "uuid",
  "product_sku": "PROD-001",
  "quantity": 1,
  "currency": "L$",
  "amount": 150
}
```

### Success Response

```json
{
  "intent_id": "pi_uuid",
  "status": "pending_authorized_debit",
  "message": "Permission granted, waiting for payment confirmation"
}
```

## 2) Webhook Endpoint

- **URL**: `/api/payments/linden/webhook`
- **Method**: `POST`
- **Validation**:
  - Signature verification with `LSL_WEBHOOK_SECRET`
  - Idempotency by provider transaction ID

## 3) Delivery Status Endpoint

- **URL**: `/api/lsl/orders/{intent_id}/status`
- **Method**: `GET`
- **Response**:

```json
{
  "intent_id": "pi_uuid",
  "payment_status": "paid",
  "delivery_status": "ready"
}
```
