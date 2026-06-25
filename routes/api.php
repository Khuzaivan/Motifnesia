<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiProductController;
use App\Http\Controllers\Api\ApiUserController;
use App\Http\Controllers\Api\ApiShoppingCardController;
use App\Http\Controllers\Api\ApiProductFavoriteController;
use App\Http\Controllers\Api\ApiUserAddressController;

/*
|--------------------------------------------------------------------------
| API Routes untuk Flutter
|--------------------------------------------------------------------------
|
| Base URL: http://127.0.0.1:8000/api/
|
*/

// ========== PUBLIC API ROUTES ==========

// Register & Login
Route::post('/register', [ApiUserController::class, 'register']);
Route::post('/login', [ApiUserController::class, 'login']);

// Catalog Products
Route::get('/products', [ApiProductController::class, 'index']);
Route::get('/products/{id}', [ApiProductController::class, 'show']);
Route::get('/products-categories', [ApiProductController::class, 'categories']);
Route::post('/products/search', [ApiProductController::class, 'search']);

// Test endpoint
Route::get('/test', function() {
    return response()->json([
        'success' => true,
        'message' => 'API Motifnesia is running!',
        'timestamp' => now()
    ]);
});

// ========== PROTECTED API ROUTES (Laravel Sanctum) ==========
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [ApiUserController::class, 'logout']);

    // Profile
    Route::get('/profile', [ApiUserController::class, 'profile']);
    Route::put('/profile', [ApiUserController::class, 'updateProfile']);
    Route::patch('/profile', [ApiUserController::class, 'updateProfile']);

    // Cart
    Route::get('/cart', [ApiShoppingCardController::class, 'index']);
    Route::get('/cart/count', [ApiShoppingCardController::class, 'count']);
    Route::post('/cart', [ApiShoppingCardController::class, 'store']);
    Route::put('/cart/{id}', [ApiShoppingCardController::class, 'update']);
    Route::patch('/cart/{id}', [ApiShoppingCardController::class, 'update']);
    Route::delete('/cart/clear', [ApiShoppingCardController::class, 'clear']);
    Route::delete('/cart/{id}', [ApiShoppingCardController::class, 'destroy']);

    // Favorites
    Route::get('/favorites', [ApiProductFavoriteController::class, 'index']);
    Route::get('/favorites/count', [ApiProductFavoriteController::class, 'count']);
    Route::get('/favorites/check', [ApiProductFavoriteController::class, 'check']);
    Route::post('/favorites', [ApiProductFavoriteController::class, 'store']);
    Route::delete('/favorites/{id}', [ApiProductFavoriteController::class, 'destroy']);
    Route::post('/favorites/{id}/add-to-cart', [ApiProductFavoriteController::class, 'addToCart']);

    // Addresses
    Route::get('/addresses', [ApiUserAddressController::class, 'index']);
    Route::get('/addresses/primary', [ApiUserAddressController::class, 'getPrimary']);
    Route::get('/addresses/{id}', [ApiUserAddressController::class, 'show']);
    Route::post('/addresses', [ApiUserAddressController::class, 'store']);
    Route::put('/addresses/{id}', [ApiUserAddressController::class, 'update']);
    Route::patch('/addresses/{id}', [ApiUserAddressController::class, 'update']);
    Route::post('/addresses/{id}/set-primary', [ApiUserAddressController::class, 'setPrimary']);
    Route::delete('/addresses/{id}', [ApiUserAddressController::class, 'destroy']);

    // Admin-Only Routes
    Route::middleware('admin')->group(function () {
        // User management
        Route::get('/users', [ApiUserController::class, 'index']);
        Route::get('/users/{id}', [ApiUserController::class, 'show']);
        Route::delete('/users/{id}', [ApiUserController::class, 'destroy']);

        // Product management (Write operations)
        Route::post('/products', [ApiProductController::class, 'store']);
        Route::put('/products/{id}', [ApiProductController::class, 'update']);
        Route::patch('/products/{id}', [ApiProductController::class, 'update']);
        Route::delete('/products/{id}', [ApiProductController::class, 'destroy']);
    });
});
