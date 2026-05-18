<?php

use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\PromotionController;
use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', [ProductController::class, 'home'])->name('home');

// Export products and statistics (admin)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/export/products', [ProductController::class, 'exportProducts'])->name('export.products');
    Route::get('/export/statistics', [ProductController::class, 'exportStatistics'])->name('export.statistics');
});

// Public promotions catalog
Route::get('/khuyen-mai', [PromotionController::class, 'publicIndex'])->name('promotions.index');

// Shopping Cart (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{productId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{productId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
});

// Checkout (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

// Products catalog resource
Route::resource('products', ProductController::class);
