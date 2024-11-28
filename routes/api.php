<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\productImageController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\TelegramController;


Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');
    
Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');
        
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('logout');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Users routes
Route::apiResource('users', UserController::class);

// Markets routes
Route::apiResource('markets', MarketController::class);
Route::post('markets/rate/{id}', [MarketController::class, 'rateMarket'])->name('markets.rate');
// GET /api/markets/top-rated?limit=5
Route::get('markets/top-rated', [MarketController::class, 'topRatedmarkets'])->name('markets.topRated');

// Products routes
Route::apiResource('products', ProductController::class);
Route::post('products/rate/{id}', [ProductController::class, 'rateProduct'])->name('products.rate');
// GET /api/products/top-rated?limit=5
Route::get('products/top-rated', [ProductController::class, 'topRatedProducts'])->name('products.topRated');

// Images routes

Route::apiResource('images', productImageController::class);

// Carts routes
Route::get('/carts', [CartController::class, 'index'])->name('carts.index');
Route::post('/carts', [CartController::class, 'store'])->name('carts.store');
Route::post('/carts/{cart}/add-item', [CartController::class, 'addItem'])->name('carts.addItem');
Route::delete('/carts/{cart}/remove-item/{item}', [CartController::class, 'removeItem'])->name('carts.removeItem');
Route::delete('/carts/{cart}', [CartController::class, 'destroy'])->name('carts.destroy');

// Cart items routes
Route::apiResource('cart-items', CartItemController::class);

// Orders routes
Route::apiResource('orders', OrderController::class);

// Categories routes
Route::apiResource('categories', CategoryController::class);

// Subcategories routes
Route::apiResource('subcategories', SubcategoryController::class);

// Favorites routes
Route::apiResource('favorites', FavoriteController::class);

Route::post('/send-verification-code', [TelegramController::class, 'sendVerificationCode']);

Route::get('/get-updates', [PasswordResetLinkController::class, 'getChatId']);
