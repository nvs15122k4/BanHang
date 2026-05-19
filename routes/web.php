<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;



// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Additional auth routes for tests compatibility
Route::get('/forgot-password', function() {
    return view('auth.forgot-password');
})->name('password.request');

// Forgot password (stub - chưa có logic)
Route::post('/forgot-password', function() {
    return back()->with('status', 'Chức năng đang phát triển.');
})->name('password.email');

// Reset password (stub - chưa có logic)
Route::post('/reset-password', function() {
    return back()->with('status', 'Chức năng đang phát triển.');
})->name('password.store');


Route::get('/verify-email', function() {
    return view('auth.verify-email');
})->name('verification.notice');

Route::post('/email/verification-notification', function(Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/verify-email/{id}/{hash}', function() {
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/confirm-password', function() {
    return view('auth.confirm-password');
})->name('password.confirm');

// Home route (public)
Route::get('/', [ProductController::class, 'home'])->name('home');

// Public Promotions page
Route::get('/khuyen-mai', [PromotionController::class, 'publicIndex'])->name('promotions.index');

// Public product routes - guest có thể xem
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Export routes
    Route::get('/export/products', [ProductController::class, 'exportProducts'])->name('export.products');
    Route::get('/export/statistics', [ProductController::class, 'exportStatistics'])->name('export.statistics');

    // Admin only routes - phải khai báo TRƯỚC route {product} để tránh conflict
    Route::middleware(['admin'])->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });
});

// Public product show - khai báo SAU create để không bị conflict
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Dashboard route for backward compatibility with tests
Route::get('/dashboard', function() {
    return redirect('/');
})->name('dashboard');

// Static Pages
Route::get('/about', function() { return view('pages.about'); })->name('pages.about');
Route::get('/blog', function() { return view('pages.blog'); })->name('pages.blog');
Route::get('/contact', function() { return view('pages.contact'); })->name('pages.contact');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Address routes
    Route::post('/profile/addresses', [ProfileController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::put('/profile/addresses/{address}', [ProfileController::class, 'updateAddress'])->name('profile.addresses.update');
    Route::delete('/profile/addresses/{address}', [ProfileController::class, 'destroyAddress'])->name('profile.addresses.destroy');
    Route::put('/profile/addresses/{address}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.addresses.default');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users management
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::post('/users/create', [App\Http\Controllers\AdminController::class, 'createUser'])->name('users.create');
    Route::put('/users/{user}/role', [App\Http\Controllers\AdminController::class, 'updateUserRole'])->name('users.role');
    Route::put('/users/{user}/status', [App\Http\Controllers\AdminController::class, 'toggleUserStatus'])->name('users.status');
    
    // Categories management
    Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [App\Http\Controllers\CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // Products management
    Route::get('/products', [App\Http\Controllers\AdminController::class, 'products'])->name('products');
    Route::put('/products/{product}/status', [App\Http\Controllers\AdminController::class, 'updateProductStatus'])->name('products.status');
    Route::put('/products/{product}/stock', [App\Http\Controllers\AdminController::class, 'updateProductStock'])->name('products.stock');
    
    // Statistics
    Route::get('/statistics', [App\Http\Controllers\AdminController::class, 'statistics'])->name('statistics');
    
    // Orders management
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [App\Http\Controllers\OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [App\Http\Controllers\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::put('/orders/{order}/payment', [App\Http\Controllers\OrderController::class, 'updatePaymentStatus'])->name('orders.payment');
    
    // Cancellation approval
    Route::post('/orders/{order}/approve-cancel', [App\Http\Controllers\OrderController::class, 'approveCancel'])->name('orders.approveCancel');
    Route::post('/orders/{order}/reject-cancel', [App\Http\Controllers\OrderController::class, 'rejectCancel'])->name('orders.rejectCancel');
    Route::put('/orders/{order}/refund', [App\Http\Controllers\OrderController::class, 'updateRefund'])->name('orders.updateRefund');
    
    // Promotions management
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/create', [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');
    Route::get('/promotions/{promotion}/edit', [PromotionController::class, 'edit'])->name('promotions.edit');
    Route::put('/promotions/{promotion}', [PromotionController::class, 'update'])->name('promotions.update');
    Route::delete('/promotions/{promotion}', [PromotionController::class, 'destroy'])->name('promotions.destroy');
    Route::patch('/promotions/{promotion}/toggle', [PromotionController::class, 'toggle'])->name('promotions.toggle');

    // Inventory management
    Route::get('/inventory', [App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/logs', [App\Http\Controllers\InventoryController::class, 'logs'])->name('inventory.logs');
    Route::post('/inventory/import', [App\Http\Controllers\InventoryController::class, 'import'])->name('inventory.import');
    Route::post('/inventory/export', [App\Http\Controllers\InventoryController::class, 'export'])->name('inventory.export');
    Route::post('/inventory/adjust', [App\Http\Controllers\InventoryController::class, 'adjust'])->name('inventory.adjust');
    
});

// Wishlist routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

// Cart routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{cartKey}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartKey}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
});

// Checkout routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
});

// Notification routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/fetch', [App\Http\Controllers\NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.readAll');
});

// My Orders routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'myOrders'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\OrderController::class, 'myOrderShow'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/update-status', [App\Http\Controllers\OrderController::class, 'updateStatusByUser'])->name('orders.updateStatus');
    
    // Refund Info
    Route::get('/orders/{order}/refund', [App\Http\Controllers\OrderController::class, 'refundInfo'])->name('orders.refund');
    Route::post('/orders/{order}/refund', [App\Http\Controllers\OrderController::class, 'submitRefundInfo'])->name('orders.submitRefund');
});

// Reviews routes (auth required)
Route::middleware('auth')->group(function () {
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Payment Webhook (Simulation)
Route::post('/payment/webhook', [App\Http\Controllers\PaymentController::class, 'webhook'])->name('payment.webhook');



