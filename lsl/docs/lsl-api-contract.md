# LSL API Contract (Draft)

## 1) Purchase Intent Endpoint

- **URL**: `/api/lsl/purchase-intent`
- **Method**: `POST`
- **Headers**:
  - `X-LSL-OBJECT-ID`
  - `X-LSL-TIMESTAMP`
  - `X-LSL-NONCE`
  - `X-LSL-SIGNATURE`

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
