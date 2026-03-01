# Implementation Guide: Front-End, Admin, and Linden (LSL/L$) Scaffolding

This guide documents the implemented scaffold and how to continue building all parts.

## 1. Implemented Components

### Storefront (Front-End)
- `routes/web.php`
  - `/` home page
  - `/products` product listing
  - `/products/{sku}` product details
  - `/account` user dashboard
- Controllers in `app/Http/Controllers/Storefront/`
  - `HomeController`
  - `ProductController`
  - `AccountController`
- Blade views in `resources/views/storefront/`
  - Home, product list/detail, account dashboard
- Shared layout in `resources/views/layouts/app.blade.php`

### Admin (Back-End)
- `routes/admin.php`
  - `/admin` dashboard
  - `/admin/products`
  - `/admin/orders`
  - `/admin/users`
- Controllers in `app/Http/Controllers/Admin/`
  - `DashboardController`
  - `ProductManagementController`
  - `OrderManagementController`
  - `UserManagementController`
- Blade views in `resources/views/admin/`
  - dashboard, products, orders, users tables

### Linden (LSL/L$) Payment Scaffolding
- API endpoints in `routes/api.php`
  - `POST /api/lsl/purchase-intent`
  - `POST /api/payments/linden/webhook`
  - `GET /api/lsl/orders/{intentId}/status`
- Validation + controllers
  - `app/Http/Requests/Api/LslPurchaseIntentRequest.php`
  - `app/Http/Controllers/Api/LslPurchaseIntentController.php`
  - `app/Http/Controllers/Api/LindenWebhookController.php`
  - `app/Http/Controllers/Api/LslOrderStatusController.php`
- Services
  - `app/Domain/LslBridge/Services/SignatureValidator.php`
  - `app/Domain/Payments/Services/LindenWebhookService.php`
- Migrations
  - `database/migrations/2026_03_01_000001_create_lsl_objects_table.php`
  - `database/migrations/2026_03_01_000002_create_payment_intents_table.php`
  - `database/migrations/2026_03_01_000003_create_payment_webhook_events_table.php`
  - `database/migrations/2026_03_01_000004_create_lsl_delivery_receipts_table.php`
- LSL assets/docs
  - `lsl/vendors/marketplace_vendor.lsl`
  - `lsl/docs/lsl-api-contract.md`

## 2. How to Wire Routes in a Full Laravel App

1. Ensure `routes/web.php` and `routes/api.php` are loaded by `RouteServiceProvider`.
2. Add loading for `routes/admin.php` in `RouteServiceProvider` with web middleware and auth/admin middleware.
3. Add authentication middleware to `/account` and all `/admin/*` routes.

## 3. Environment Configuration

Add in `.env`:
- `LSL_SHARED_SECRET`
- `LSL_WEBHOOK_SECRET`
- `LINDEN_PAYMENT_PROVIDER_URL`

Add `services.linden.webhook_secret` mapping in `config/services.php`.

## 4. Security Hardening To Implement Next

1. Replace plaintext/placeholder secret usage with proper secret hashing and retrieval strategy.
2. Store and enforce unique nonce use per object/timestamp window.
3. Add request throttling to all LSL/webhook endpoints.
4. Add auth/authorization for admin controllers and account routes.
5. Add robust order-amount verification before marking intents paid.

## 5. Data Modeling Next Steps

- Add first-class tables/models for:
  - products, categories, inventory, users, roles
  - carts, order_items, reviews, wishlists, support messages
- Connect storefront/controllers to Eloquent models instead of inline arrays.

## 6. Front-End Next Steps

- Integrate Blade components or Livewire for:
  - product filters
  - cart interactions
  - checkout flow
  - customer support messaging

## 7. Admin Next Steps

- Add CRUD forms for products and categories.
- Add order detail page with payment timeline.
- Add user moderation tools and role management.

## 8. Linden Workflow Validation

1. Register LSL object in `lsl_objects` with active secret.
2. Configure script constants in `lsl/vendors/marketplace_vendor.lsl`.
3. Send signed purchase intent from in-world object.
4. Process Linden webhook with valid signature.
5. Confirm status endpoint returns `paid` and `ready` after webhook.

## 9. Recommended Testing Plan

- Feature tests for each API endpoint.
- Signature validation unit tests.
- Webhook duplicate/idempotency tests.
- Authorization tests for admin routes.

## 10. Implemented Next-Step Upgrades (DB-backed + auth/admin middleware)

The scaffold now includes the first recommended upgrades:

- Controllers now query real models instead of hardcoded arrays:
  - `Storefront\HomeController` uses `Product` records marked `featured`
  - `Storefront\ProductController` uses paginated `Product` data
  - `Storefront\AccountController` loads authenticated user's `orders`
  - Admin list controllers use paginated `Product`, `Order`, and `User` data
- Added auth/authorization protection:
  - `/account` now requires `auth` middleware
  - `/admin/*` now requires `auth` + custom `admin` middleware
  - `App\Http\Middleware\EnsureUserIsAdmin` checks `role=admin` and `status=active`
- Added marketplace core schema tables:
  - `products`
  - `orders`
  - user profile extensions (`public_uuid`, `role`, `status`)
- Added seeded demo users/products/orders for quick local testing.

### Seeded accounts

- Admin: `admin@example.com` / `password`
- Customer: `customer@example.com` / `password`
- Test: `test@example.com` / `password`

## 11. Sprint 1 Delivered

Sprint 1 security and auth foundations are now in place:

- Installed Laravel Breeze (Blade) authentication scaffold.
- Added replay protection table `lsl_request_nonces` and nonce uniqueness check in purchase-intent handling.
- Enforced canonical signature header presence for LSL purchase intents.
- Hardened webhook flow with intent existence check and strict amount/currency verification.
- Added feature tests for:
  - valid purchase-intent signature acceptance
  - replayed nonce rejection
  - webhook amount mismatch rejection
  - admin authorization (guest/customer/admin behavior)
