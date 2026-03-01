<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\ProductManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/products', [ProductManagementController::class, 'index'])->name('products.index');
        Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    });
