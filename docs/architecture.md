# System Architecture

## Stack

- **App Framework**: Laravel 11+
- **Frontend**: Blade + Livewire (or Inertia alternative)
- **Database**: MySQL 8+ or PostgreSQL
- **Cache/Queue**: Redis + Laravel Queue/Horizon
- **Search**: Scout + Meilisearch/OpenSearch
- **Storage**: S3-compatible object storage
- **Payments**: Linden Dollar (L$) primary flow via LSL authorization and webhook validation

## Major Domains

1. **Catalog Domain**
   - Products, categories, variants, tags, pricing
2. **Identity Domain**
   - Accounts, roles, permissions, profile data
3. **Checkout Domain**
   - Cart, order intents, coupon/tax/shipping rules
4. **Order Domain**
   - Orders, order items, statuses, fulfillment, refunds
5. **Community Domain**
   - Reviews, ratings, wishlists
6. **Support Domain**
   - Threaded messaging/ticketing
7. **LSL Bridge Domain**
   - In-world object registration, signatures, permissions, webhook callbacks, delivery confirmations

## Runtime Interaction Model

1. Customer browses storefront and selects product.
2. If in-world purchase is initiated, LSL object sends signed purchase intent.
3. LSL script requests authorization to debit L$ from user.
4. Backend stores pending transaction intent with nonce/idempotency key.
5. Payment provider/webhook confirms debit; Laravel validates signature + payload integrity.
6. Backend marks transaction paid and triggers delivery event.
7. LSL object receives status and confirms item delivery.

## Security Posture

- HTTPS-only external endpoints
- HMAC signatures for LSL-to-Laravel requests
- Nonce + timestamp replay protection
- Webhook signature validation
- Strict RBAC for admin features
- Audit logs for admin and payment-sensitive actions
