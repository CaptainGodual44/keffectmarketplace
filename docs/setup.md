# Setup Guide

## Prerequisites

- PHP 8.3+
- Composer 2+
- Node.js 20+
- MySQL 8+ or PostgreSQL 15+
- Redis 7+

## Environment Variables (planned)

- `APP_ENV`, `APP_KEY`, `APP_URL`
- `DB_*`
- `REDIS_*`
- `QUEUE_CONNECTION=redis`
- `LSL_SHARED_SECRET`
- `LSL_WEBHOOK_SECRET`
- `LINDEN_PAYMENT_PROVIDER_URL`

## Planned Install Steps

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

## Planned Runtime Steps

```bash
php artisan serve
php artisan queue:work
php artisan schedule:work
```

## Validation Checklist

- Application boots locally
- Database migrations complete
- Queue worker starts
- LSL signed request endpoint returns expected validation messages
- Webhook endpoint rejects malformed signature and accepts valid payload
