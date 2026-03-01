# Keffect Marketplace

Laravel marketplace scaffold with storefront, admin panels, and Linden Dollar (L$) + LSL payment integration endpoints.

## Current Status

This repository now contains a full Laravel 12 application base plus marketplace scaffolding:

- Storefront routes/controllers/views (`/`, `/products`, `/account`)
- Admin routes/controllers/views (`/admin`, `/admin/products`, `/admin/orders`, `/admin/users`)
- LSL payment endpoints:
  - `POST /api/lsl/purchase-intent`
  - `POST /api/payments/linden/webhook`
  - `GET /api/lsl/orders/{intentId}/status`
- Payment and LSL migrations:
  - `lsl_objects`
  - `payment_intents`
  - `payment_webhook_events`
  - `lsl_delivery_receipts`

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

## Routing Notes

- `routes/web.php` for storefront pages
- `routes/api.php` for LSL/webhook APIs
- `routes/admin.php` is registered in `bootstrap/app.php`

## Documentation Index

- [Architecture](docs/architecture.md)
- [Setup Guide](docs/setup.md)
- [Code Structure](docs/code-structure.md)
- [L$ + LSL Payment Flow](docs/payment-lsl.md)
- [Implementation Guide](docs/implementation-guide.md)
- [LSL API Contract](lsl/docs/lsl-api-contract.md)
