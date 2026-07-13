<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes for Cart
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index']);
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add']);
    Route::patch('/cart/{cartKey}', [App\Http\Controllers\CartController::class, 'update']);
    Route::delete('/cart/{cartKey}', [App\Http\Controllers\CartController::class, 'remove']);
    Route::delete('/cart', [App\Http\Controllers\CartController::class, 'clear']);
});

// API Routes for Wishlist
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/wishlist/{product}', [App\Http\Controllers\WishlistController::class, 'toggle']);
    Route::get('/wishlist', [App\Http\Controllers\WishlistController::class, 'index']);
});

// API Routes for Reviews
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store']);
    Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy']);
});

// API Routes for Notifications
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index']);
    Route::get('/notifications/fetch', [App\Http\Controllers\NotificationController::class, 'fetch']);
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllRead']);
});

// API Routes for Products (public)
Route::get('/products', [App\Http\Controllers\ProductController::class, 'index']);
Route::get('/products/{product}', [App\Http\Controllers\ProductController::class, 'show']);

// API Routes for Promotions (public)
Route::get('/promotions', [App\Http\Controllers\PromotionController::class, 'publicIndex']);
