<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Mobile App
|--------------------------------------------------------------------------
*/

// Auth
Route::post('/auth/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/auth/register', [App\Http\Controllers\Api\AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/auth/user', [App\Http\Controllers\Api\AuthController::class, 'user']);

    // Profile
    Route::get('/profile', [App\Http\Controllers\Api\ProfileController::class, 'show']);
    Route::put('/profile', [App\Http\Controllers\Api\ProfileController::class, 'update']);
    Route::put('/profile/password', [App\Http\Controllers\Api\ProfileController::class, 'updatePassword']);

    // Avatar upload
    Route::post('/profile/avatar', [App\Http\Controllers\Api\ProfileController::class, 'uploadAvatar']);

    // Addresses
    Route::get('/profile/addresses', [App\Http\Controllers\Api\ProfileController::class, 'addresses']);
    Route::post('/profile/addresses', [App\Http\Controllers\Api\ProfileController::class, 'storeAddress']);
    Route::put('/profile/addresses/{address}', [App\Http\Controllers\Api\ProfileController::class, 'updateAddress']);
    Route::delete('/profile/addresses/{address}', [App\Http\Controllers\Api\ProfileController::class, 'destroyAddress']);
    Route::put('/profile/addresses/{address}/default', [App\Http\Controllers\Api\ProfileController::class, 'setDefaultAddress']);

    // Cart
    Route::get('/cart', [App\Http\Controllers\Api\CartController::class, 'index']);
    Route::post('/cart/add', [App\Http\Controllers\Api\CartController::class, 'add']);
    Route::patch('/cart/{cartKey}', [App\Http\Controllers\Api\CartController::class, 'update']);
    Route::delete('/cart/{cartKey}', [App\Http\Controllers\Api\CartController::class, 'remove']);
    Route::delete('/cart', [App\Http\Controllers\Api\CartController::class, 'clear']);

    // Checkout
    Route::post('/checkout', [App\Http\Controllers\Api\CheckoutController::class, 'store']);

    // Orders
    Route::get('/orders', [App\Http\Controllers\Api\OrderController::class, 'index']);
    Route::get('/orders/{order}', [App\Http\Controllers\Api\OrderController::class, 'show']);
    Route::post('/orders/{order}/cancel', [App\Http\Controllers\Api\OrderController::class, 'cancel']);
    Route::post('/orders/{order}/update-status', [App\Http\Controllers\Api\OrderController::class, 'updateStatusByUser']);

    // Wishlist
    Route::post('/wishlist/{product}', [App\Http\Controllers\Api\WishlistController::class, 'toggle']);
    Route::get('/wishlist', [App\Http\Controllers\Api\WishlistController::class, 'index']);

    // Reviews
    Route::post('/reviews', [App\Http\Controllers\Api\ReviewController::class, 'store']);
    Route::delete('/reviews/{review}', [App\Http\Controllers\Api\ReviewController::class, 'destroy']);

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::get('/notifications/fetch', [App\Http\Controllers\Api\NotificationController::class, 'fetch']);
    Route::post('/notifications/{id}/read', [App\Http\Controllers\Api\NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [App\Http\Controllers\Api\NotificationController::class, 'markAllRead']);

    // Admin
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [App\Http\Controllers\Api\AdminController::class, 'dashboard']);
        Route::get('/admin/orders', [App\Http\Controllers\Api\AdminController::class, 'orders']);
        Route::put('/admin/orders/{order}/status', [App\Http\Controllers\Api\AdminController::class, 'updateOrderStatus']);
    });
});

// Public
Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
Route::get('/products/{product}', [App\Http\Controllers\Api\ProductController::class, 'show']);
Route::get('/categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);
Route::get('/promotions', [App\Http\Controllers\PromotionController::class, 'publicIndex']);
