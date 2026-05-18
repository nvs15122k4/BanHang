<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\InventoryController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

// Users management
Route::get('/users', [UserController::class, 'users'])->name('users');
Route::post('/users/create', [UserController::class, 'createUser'])->name('users.create');
Route::put('/users/{user}/role', [UserController::class, 'updateUserRole'])->name('users.role');
Route::put('/users/{user}/status', [UserController::class, 'toggleUserStatus'])->name('users.status');

// Categories management
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

// Products management
Route::get('/products', [ProductController::class, 'products'])->name('products');
Route::put('/products/{product}/status', [ProductController::class, 'updateProductStatus'])->name('products.status');
Route::put('/products/{product}/stock', [ProductController::class, 'updateProductStock'])->name('products.stock');

// Statistics
Route::get('/statistics', [DashboardController::class, 'statistics'])->name('statistics');

// Orders management
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
Route::put('/orders/{order}/payment', [OrderController::class, 'updatePaymentStatus'])->name('orders.payment');

// Cancellation approval
Route::post('/orders/{order}/approve-cancel', [OrderController::class, 'approveCancel'])->name('orders.approveCancel');
Route::post('/orders/{order}/reject-cancel', [OrderController::class, 'rejectCancel'])->name('orders.rejectCancel');
Route::put('/orders/{order}/refund', [OrderController::class, 'updateRefund'])->name('orders.updateRefund');

// Promotions management
Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
Route::get('/promotions/create', [PromotionController::class, 'create'])->name('promotions.create');
Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
Route::get('/promotions/{promotion}/edit', [PromotionController::class, 'edit'])->name('promotions.edit');
Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');
Route::patch('/promotions/{promotion}/toggle', [PromotionController::class, 'toggle'])->name('promotions.toggle');

// Inventory management
Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/inventory/logs', [InventoryController::class, 'logs'])->name('inventory.logs');
Route::post('/inventory/import', [InventoryController::class, 'import'])->name('inventory.import');
Route::post('/inventory/export', [InventoryController::class, 'export'])->name('inventory.export');
Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
