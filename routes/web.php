<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WishlistController;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])
    ->middleware('throttle:6,1')
    ->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.store');

Route::get('/verify-email', [AuthController::class, 'showVerificationNotice'])->name('verification.notice');
Route::post('/email/verification-notification', [AuthController::class, 'sendVerificationEmail'])
    ->middleware('throttle:6,1')
    ->name('verification.send');
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::get('/confirm-password', function () {
    return view('auth.confirm-password');
})->name('password.confirm');

// Home route (public)
Route::get('/', [ProductController::class, 'home'])->name('home');

// Public XML sitemap for search engines
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Public Promotions page
Route::get('/khuyen-mai', [PromotionController::class, 'publicIndex'])->name('promotions.index');

// Public product routes - guest có thể xem
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/danh-muc/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/san-pham/{product:slug}', [ProductController::class, 'show'])->name('products.show');

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

// Legacy product URL remains available as a single permanent redirect.
Route::get('/products/{product}', [ProductController::class, 'redirectLegacy'])->name('products.legacy.show');

// Dashboard route for backward compatibility with tests
Route::get('/dashboard', function () {
    return redirect('/');
})->name('dashboard');

// Static Pages
Route::get('/about', function () {
    return view('pages.about');
})->name('pages.about');
Route::get('/blog', function () {
    return view('pages.blog');
})->name('pages.blog');
Route::get('/blog/{slug}', function (string $slug) {
    $posts = [
        'cach-chon-size-quan-ao-khi-mua-online' => 'blog.size-online',
        'cach-phoi-ao-thun-don-gian-hang-ngay' => 'blog.phoi-ao-thun',
        'cach-bao-quan-trang-phuc-ben-mau' => 'blog.bao-quan-trang-phuc',
    ];

    abort_unless(array_key_exists($slug, $posts), 404);

    return view($posts[$slug]);
})->name('blog.show');
Route::get('/contact', function () {
    return view('pages.contact');
})->name('pages.contact');
Route::view('/chinh-sach/giao-hang', 'pages.policies.shipping')->name('policies.shipping');
Route::view('/chinh-sach/doi-tra', 'pages.policies.returns')->name('policies.returns');
Route::view('/chinh-sach/thanh-toan', 'pages.policies.payment')->name('policies.payment');
Route::view('/chinh-sach/bao-mat', 'pages.policies.privacy')->name('policies.privacy');
Route::view('/chinh-sach/dieu-khoan', 'pages.policies.terms')->name('policies.terms');
Route::view('/ho-tro/cau-hoi-thuong-gap', 'pages.support.faq')->name('support.faq');
Route::view('/ho-tro/huong-dan-mua-hang', 'pages.support.purchase-guide')->name('support.purchase-guide');
Route::view('/huong-dan/chon-size', 'pages.guides.size')->name('guides.size');

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
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/export/statistics', [ProductController::class, 'exportStatistics'])->name('export.statistics');

    // Users management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::put('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.role');
    Route::put('/users/{user}/status', [AdminController::class, 'toggleUserStatus'])->name('users.status');

    // Categories management
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::patch('/categories/{category}/seen', [CategoryController::class, 'markSeen'])->name('categories.seen');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Products management
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::put('/products/{product}/status', [AdminController::class, 'updateProductStatus'])->name('products.status');
    Route::put('/products/{product}/stock', [AdminController::class, 'updateProductStock'])->name('products.stock');
    Route::patch('/products/{productId}/restore', [AdminController::class, 'restoreProduct'])->name('products.restore');

    // Statistics
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');

    // Orders management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [AdminOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [AdminOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::put('/orders/{order}/payment', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.payment');

    // Cancellation approval
    Route::post('/orders/{order}/approve-cancel', [AdminOrderController::class, 'approveCancel'])->name('orders.approveCancel');
    Route::post('/orders/{order}/reject-cancel', [AdminOrderController::class, 'rejectCancel'])->name('orders.rejectCancel');
    Route::get('/orders/{order}/refund', function (Order $order) {
        return redirect()->route('admin.orders.show', $order);
    })->name('orders.refund.redirect');
    Route::put('/orders/{order}/refund', [AdminOrderController::class, 'updateRefund'])->name('orders.updateRefund');

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

});

// Wishlist routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

// Cart routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{cartKey}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cartKey}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
});

// Checkout routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

// Notification routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
});

// My Orders routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'myOrders'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'myOrderShow'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatusByUser'])->name('orders.updateStatus');

    // Refund Info
    Route::get('/orders/{order}/refund', [OrderController::class, 'refundInfo'])->name('orders.refund');
    Route::post('/orders/{order}/refund', [OrderController::class, 'submitRefundInfo'])->name('orders.submitRefund');
});

// Reviews routes (auth required)
Route::middleware('auth')->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Payment Webhook (Simulation)
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
