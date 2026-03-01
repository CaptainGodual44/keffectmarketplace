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

## Admin login troubleshooting

If admin login fails with "These credentials do not match our records":

```bash
php artisan db:seed --class=Database\\Seeders\\DatabaseSeeder --force
```

Or reset admin credentials directly:

```bash
php artisan marketplace:reset-admin admin@example.com password
```

Default seeded admin credentials are:
- email: `admin@example.com`
- password: `password`
