<?php
use Illuminate\Support\Facades\Route;

// Import Controllers auth
use App\Http\Controllers\AdminController;

// Import Controllers User (Frontend)
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\StockOpnameController;
use App\Http\Controllers\Admin\StockProcurementController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Customer\ReviewController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Customer\CheckOutController;
use App\Http\Controllers\Customer\FavoriteController;
use App\Http\Controllers\Admin\StaticContentController;
use App\Http\Controllers\Customer\UserAddressController;
use App\Http\Controllers\Customer\MembershipController as CustomerMembershipController;
use App\Http\Controllers\Admin\MembershipController as AdminMembershipController;
use App\Http\Controllers\Customer\PurchaseHistoryController;

// Import Controllers Admin (Backend)
use App\Http\Controllers\Customer\TransactionController;
use App\Http\Controllers\Customer\UserProfileController;
use App\Http\Controllers\Customer\NotificationController;
use App\Http\Controllers\Customer\ShoppingCardController;
use App\Http\Controllers\Customer\CustomerProductController;
use App\Http\Controllers\Supplier\ProcurementController as SupplierProcurementController;
use App\Http\Controllers\Admin\AdminProductController; // ← CRUD Produk Admin
// Redirect root path berdasarkan role
Route::get('/', function() {
    if (auth()->check() && auth()->user()->role === 'admin') {
        if (auth()->user()->admin_role === 'finance') {
            return redirect('/admin/order-status');
        }
        if (auth()->user()->admin_role === 'gudang') {
            return redirect('/admin/warehouse');
        }
        return redirect('/admin/product-management');
    }
    if (auth()->check() && auth()->user()->role === 'supplier') {
        return redirect()->route('supplier.procurements.index');
    }
    return redirect()->route('customer.home');
});

// ==================== AUTH GROUP ====================
Route::group(['prefix' => '', 'as' => 'auth.', 'middleware' => 'guest'], function () {
    // Halaman Login (GET)
    Route::get('/login', [UserController::class, 'login'])->name('login');
    // Proses Login (POST)
    Route::post('/login', [UserController::class, 'doLogin'])->name('doLogin');
    // Register (GET)
    Route::get('/register', [UserController::class, 'register'])->name('register');
    Route::post('/register', [UserController::class, 'doRegister'])->name('doRegister');
    // Forgot Password
    Route::get('/forgot', [UserController::class, 'forgot'])->name('forgot');
    Route::post('/forgot', [UserController::class, 'doForgot'])->name('doForgot');
});

// Logout route
Route::get('/logout', [UserController::class, 'logout'])->name('auth.logout');

// ==================== CUSTOMER GROUP (PUBLIC) ====================
Route::group(['prefix' => '', 'as' => 'customer.'], function () {
    // ========== HOME & PRODUCTS (Public, tapi redirect admin) ==========
    Route::get('/homePage', [CustomerProductController::class, 'index'])->name('home')
        ->middleware('block.admin');
    Route::get('/products/{id}', [CustomerProductController::class, 'show'])->name('product.detail');
    Route::get('/search/live', [CustomerProductController::class, 'liveSearch'])->name('search.live');
});

// ==================== CUSTOMER GROUP (AUTH REQUIRED) ====================
Route::group(['prefix' => '', 'as' => 'customer.', 'middleware' => 'customer'], function () {
    // ========== SHOPPING CART (Keranjang Belanja) ==========
    Route::get('/cart', [ShoppingCardController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [ShoppingCardController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [ShoppingCardController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [ShoppingCardController::class, 'delete'])->name('cart.delete');
    Route::post('/cart/checkout', [ShoppingCardController::class, 'checkout'])->name('cart.checkout');

    // ========== CHECKOUT ==========
    Route::get('/checkout', [CheckOutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/store', [CheckOutController::class, 'store'])->name('checkout.store');

    // ========== PAYMENT/TRANSACTION ==========
    Route::get('/payment', [\App\Http\Controllers\Customer\PaymentController::class, 'index'])->name('payment.index');
    Route::post('/payment/store', [\App\Http\Controllers\Customer\PaymentController::class, 'store'])->name('payment.store');
    Route::get('/transaction/success/{orderId}', [\App\Http\Controllers\Customer\PaymentController::class, 'success'])->name('transaction.success');

    // ========== FAVORITES & NOTIFICATIONS ==========
    Route::get('/favorites', [\App\Http\Controllers\Customer\ProductFavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/store', [\App\Http\Controllers\Customer\ProductFavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{id}', [\App\Http\Controllers\Customer\ProductFavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::get('/favorites/{id}/add-to-cart', [\App\Http\Controllers\Customer\ProductFavoriteController::class, 'addToCart'])->name('favorites.addToCart');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');

    // ========== MEMBERSHIP ==========
    Route::get('/membership', [CustomerMembershipController::class, 'index'])->name('membership.index');
    Route::post('/membership/register', [CustomerMembershipController::class, 'register'])->name('membership.register');
    Route::get('/membership/history', [CustomerMembershipController::class, 'history'])->name('membership.history');
    Route::get('/membership/vouchers', [CustomerMembershipController::class, 'myVouchers'])->name('membership.vouchers');
    Route::post('/membership/redeem/{reward}', [CustomerMembershipController::class, 'redeem'])->name('membership.redeem');

    // ========== REVIEWS (Old - Product Detail Reviews) ==========
    Route::get('/products/{id}/reviews', [ReviewController::class, 'index'])->name('products.reviews.index');
    Route::post('/product-reviews', [ReviewController::class, 'store'])->name('product.reviews.store');
    Route::put('/product-reviews/{id}', [ReviewController::class, 'update'])->name('product.reviews.update');
    Route::delete('/product-reviews/{id}', [ReviewController::class, 'destroy'])->name('product.reviews.destroy');

    // ========== ORDER REVIEWS (Purchase History Reviews) ==========
    Route::post('/order-reviews', [\App\Http\Controllers\Customer\OrderReviewController::class, 'store'])->name('order.reviews.store');
    Route::get('/order-reviews/{orderItemId}', [\App\Http\Controllers\Customer\OrderReviewController::class, 'show'])->name('order.reviews.show');

    // ========== USER PROFILE ==========
    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/edit', [UserProfileController::class, 'update'])->name('profile.update');
    Route::post('/orders/{order}/confirm-arrived', [PurchaseHistoryController::class, 'confirmArrived'])->name('orders.confirmArrived');

    // User Addresses (AJAX)
    Route::post('/profile/addresses', [UserAddressController::class, 'store'])->name('profile.addresses.store');
    Route::post('/profile/addresses/{id}/update', [UserAddressController::class, 'update'])->name('profile.addresses.update');
    Route::post('/profile/addresses/{id}/set-primary', [UserAddressController::class, 'setPrimary'])->name('profile.addresses.setPrimary');
    Route::delete('/profile/addresses/{id}', [UserAddressController::class, 'destroy'])->name('profile.addresses.destroy');

    // ========== RETURNS (Customer) ==========
    Route::get('/returns', [\App\Http\Controllers\Customer\ReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/create/{orderItemId}', [\App\Http\Controllers\Customer\ReturnController::class, 'create'])->name('returns.create');
    Route::post('/returns', [\App\Http\Controllers\Customer\ReturnController::class, 'store'])->name('returns.store');
    Route::patch('/returns/{id}/cancel', [\App\Http\Controllers\Customer\ReturnController::class, 'cancel'])->name('returns.cancel');
    Route::post('/returns/{id}/courier-proof', [\App\Http\Controllers\Customer\ReturnController::class, 'submitCourierProof'])->name('returns.courierProof');

    // ========== LIVE CHAT (Customer) ==========
    Route::get('/chat', [\App\Http\Controllers\Customer\ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/get-or-create', [\App\Http\Controllers\Customer\ChatController::class, 'getOrCreateChat'])->name('chat.getOrCreate');
    Route::post('/chat/product/{productId}/ask', [\App\Http\Controllers\Customer\ChatController::class, 'askProduct'])->name('chat.askProduct');
    Route::post('/chat/send', [\App\Http\Controllers\Customer\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/{chatId}/messages', [\App\Http\Controllers\Customer\ChatController::class, 'getNewMessages'])->name('chat.messages');
    Route::post('/chat/{chatId}/close', [\App\Http\Controllers\Customer\ChatController::class, 'closeChat'])->name('chat.close');
});

// ==================== ADMIN GROUP ====================
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function () {
    // Daftar Produk (list/table view)
    Route::get('/daftar-produk', [AdminProductController::class, 'index'])
        ->name('daftar-produk');
    // Product Management Page (grid + modal edit/delete)
    Route::get('/product-management', [AdminProductController::class, 'manage'])
        ->name('product.management.index');
    // CREATE & STORE product (Form + Save in one route)
    Route::match(['GET', 'POST'], '/products/create', [AdminProductController::class, 'createOrStore'])
        ->name('products.create');
    // UPDATE product (modal)
    Route::post('/products/{id}/update', [AdminProductController::class, 'update'])
        ->name('products.update');
    // DELETE product (modal)
    Route::post('/products/{id}/delete', [AdminProductController::class, 'destroy'])
        ->name('products.delete');
    // Supply Chain: Supplier, Pengadaan Stok, Gudang, Stock Opname
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    Route::get('/stock-procurements', [StockProcurementController::class, 'index'])->name('stock-procurements.index');
    Route::get('/stock-procurements/create', [StockProcurementController::class, 'create'])->name('stock-procurements.create');
    Route::post('/stock-procurements', [StockProcurementController::class, 'store'])->name('stock-procurements.store');
    Route::get('/stock-procurements/{stockProcurement}', [StockProcurementController::class, 'show'])->name('stock-procurements.show');
    Route::post('/stock-procurements/{stockProcurement}/confirm-arrived', [StockProcurementController::class, 'confirmArrived'])->name('stock-procurements.confirm-arrived');
    Route::post('/stock-procurements/{stockProcurement}/apply-stock', [StockProcurementController::class, 'applyStock'])->name('stock-procurements.apply-stock');

    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('/stock-opname', [StockOpnameController::class, 'index'])->name('stock-opname.index');
    Route::post('/stock-opname/adjust', [StockOpnameController::class, 'adjust'])->name('stock-opname.adjust');
    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    // Membership
    Route::get('/memberships', [AdminMembershipController::class, 'index'])->name('memberships.index');
    Route::post('/memberships/{id}/status', [AdminMembershipController::class, 'updateMemberStatus'])->name('memberships.status');
    Route::post('/memberships/{id}/points', [AdminMembershipController::class, 'adjustPoints'])->name('memberships.points');
    Route::get('/membership-rewards', [AdminMembershipController::class, 'rewards'])->name('membership-rewards.index');
    Route::get('/membership-rewards/create', [AdminMembershipController::class, 'createReward'])->name('membership-rewards.create');
    Route::post('/membership-rewards', [AdminMembershipController::class, 'storeReward'])->name('membership-rewards.store');
    Route::get('/membership-rewards/{id}/edit', [AdminMembershipController::class, 'editReward'])->name('membership-rewards.edit');
    Route::put('/membership-rewards/{id}', [AdminMembershipController::class, 'updateReward'])->name('membership-rewards.update');
    Route::delete('/membership-rewards/{id}', [AdminMembershipController::class, 'destroyReward'])->name('membership-rewards.destroy');
    Route::get('/membership-broadcast', [AdminMembershipController::class, 'broadcastForm'])->name('membership-broadcast.index');
    Route::post('/membership-broadcast', [AdminMembershipController::class, 'broadcast'])->name('membership-broadcast.store');
    // Product Reviews (admin view)
    Route::get('/product-reviews', [App\Http\Controllers\Admin\ProductReviewController::class, 'index'])->name('reviews.index');
    Route::get('/product-reviews/{id}', [App\Http\Controllers\Admin\ProductReviewController::class, 'show'])->name('reviews.show');
    Route::post('/product-reviews/{id}/moderate', [App\Http\Controllers\Admin\ProductReviewController::class, 'moderate'])->name('reviews.moderate');
    // Live Chat Admin
    Route::get('/live-chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/live-chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/live-chat/{chatId}/messages', [ChatController::class, 'getNewMessages'])->name('chat.messages');
    Route::get('/live-chat/list', [ChatController::class, 'getChatList'])->name('chat.list');
    // Returns (Admin)
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::post('/returns/{id}/update-status', [ReturnController::class, 'updateStatus'])->name('returns.updateStatus');
    Route::delete('/returns/{id}', [ReturnController::class, 'destroy'])->name('returns.destroy');
    // Order Status
    Route::get('/order-status', [OrderStatusController::class, 'index'])->name('orders.status');
    Route::post('/order-status/{id}/update', [OrderStatusController::class, 'updateStatus'])->name('orders.status.update');
    Route::post('/order-status/{id}/payment', [OrderStatusController::class, 'updatePaymentStatus'])->name('orders.payment.update');
    // Sales Report
    Route::get('/sales-report', [ReportController::class, 'index'])->name('reports.sales');
    Route::get('/sales-report/export', [ReportController::class, 'export'])->name('reports.export');
    // Notifications
    Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/toggle-read', [App\Http\Controllers\Admin\NotificationController::class, 'toggleRead'])->name('notifications.toggleRead');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/clear-read', [App\Http\Controllers\Admin\NotificationController::class, 'clearRead'])->name('notifications.clearRead');
    Route::delete('/notifications/{id}', [App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');
    // STATIC CONTENT
    Route::get('/konten', [App\Http\Controllers\Admin\StaticContentController::class, 'index'])
        ->name('konten.index');
    // Slideshow CRUD
    Route::post('/konten/slides/create', [App\Http\Controllers\Admin\StaticContentController::class, 'storeSlide'])
        ->name('konten.slides.create');
    Route::post('/konten/slides/{id}/update', [App\Http\Controllers\Admin\StaticContentController::class, 'updateSlide'])
        ->name('konten.slides.update');
    Route::post('/konten/slides/{id}/delete', [App\Http\Controllers\Admin\StaticContentController::class, 'deleteSlide'])
        ->name('konten.slides.delete');
    // Old endpoint (compatibility only)
    Route::post('/konten/slideshow', [App\Http\Controllers\Admin\StaticContentController::class, 'updateSlideshow']);
});

// ==================== SUPPLIER GROUP ====================
Route::group(['prefix' => 'supplier', 'as' => 'supplier.', 'middleware' => 'supplier'], function () {
    Route::get('/procurements', [SupplierProcurementController::class, 'index'])->name('procurements.index');
    Route::get('/procurements/{procurement}', [SupplierProcurementController::class, 'show'])->name('procurements.show');
    Route::post('/procurements/{procurement}/status', [SupplierProcurementController::class, 'updateStatus'])->name('procurements.status');
});
