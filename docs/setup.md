# Setup Guide

## Prerequisites

- PHP 8.2+
- Composer 2+
- Node.js 20+
- MySQL 8+ / PostgreSQL 15+ / SQLite

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

## Run Locally

```bash
php artisan serve
php artisan queue:work
```

## Important Configuration

Set these in `.env` for Linden integration:

- `LSL_SHARED_SECRET`
- `LSL_WEBHOOK_SECRET`
- `LINDEN_PAYMENT_PROVIDER_URL`

Also map webhook secret in `config/services.php`:

```php
'linden' => [
    'webhook_secret' => env('LSL_WEBHOOK_SECRET'),
],
```

## Verification

```bash
php artisan route:list
php artisan migrate:status
php artisan test
```
