<?php

declare(strict_types=1);

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Storefront\AccountController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('storefront.home');
Route::get('/products', [ProductController::class, 'index'])->name('storefront.products.index');
Route::get('/products/{sku}', [ProductController::class, 'show'])->name('storefront.products.show');

Route::middleware('auth')->get('/dashboard', function () {
    return redirect()->route('storefront.account.dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::get('/account', [AccountController::class, 'dashboard'])->name('storefront.account.dashboard');

    Route::get('/cart', [CartController::class, 'index'])->name('storefront.cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('storefront.cart.add');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('storefront.cart.checkout');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
