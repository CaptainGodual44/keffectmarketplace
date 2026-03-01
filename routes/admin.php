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
        Route::get('/products/create', [ProductManagementController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductManagementController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductManagementController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductManagementController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductManagementController::class, 'destroy'])->name('products.destroy');

        Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderManagementController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('orders.status.update');

        Route::get('/products', [ProductManagementController::class, 'index'])->name('products.index');
        Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    });
