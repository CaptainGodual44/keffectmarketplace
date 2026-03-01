<?php

declare(strict_types=1);

use App\Http\Controllers\Api\LindenWebhookController;
use App\Http\Controllers\Api\LslOrderStatusController;
use App\Http\Controllers\Api\LslPurchaseIntentController;
use Illuminate\Support\Facades\Route;

Route::post('/lsl/purchase-intent', LslPurchaseIntentController::class);
Route::post('/payments/linden/webhook', LindenWebhookController::class);
Route::get('/lsl/orders/{intentId}/status', LslOrderStatusController::class);
