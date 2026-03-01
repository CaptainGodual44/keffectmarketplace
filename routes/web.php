<?php

declare(strict_types=1);

use App\Http\Controllers\Storefront\AccountController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('storefront.home');
Route::get('/products', [ProductController::class, 'index'])->name('storefront.products.index');
Route::get('/products/{sku}', [ProductController::class, 'show'])->name('storefront.products.show');
Route::get('/account', [AccountController::class, 'dashboard'])->name('storefront.account.dashboard');
