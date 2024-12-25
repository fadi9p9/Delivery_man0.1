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
use App\Http\Middleware\CheckAdmin;

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
Route::post('users/update/{id}', [UserController::class, "updateuser"]);

// Markets routes
Route::apiResource('markets', MarketController::class);
Route::post('markets/update/{id}', [MarketController::class,"updateMarket"]);
Route::post('markets/rate/{id}', [MarketController::class, 'rateMarket'])->name('markets.rate');
// GET /api/market/toprate?limit=5
route::get('/market/toprate', [MarketController::class, 'MarketTopRate']);

// Products routes
Route::apiResource('products', ProductController::class);
Route::post('products/rate/{id}', [ProductController::class, 'rateProduct'])->name('products.rate');
// GET /api/product/toprate?limit=5
route::get('product/toprate', [ProductController::class, 'productTopRate']);
// Images routes

Route::apiResource('images', productImageController::class);
// Carts routes
Route::apiResource('carts', CartController::class);
Route::post('/carts/{cart}/add-item', [CartController::class, 'addItem'])->name('carts.addItem');
Route::delete('/carts/{cart}/remove-item/{item}', [CartController::class, 'removeItem'])->name('carts.removeItem');

// Cart items routes
Route::apiResource('cart-items', CartItemController::class);

// Orders routes
Route::apiResource('orders', OrderController::class);
// Order status routes !!!!!!!!!!!!!!!!!!!!!!!!!
Route::put('/orders/{orderId}/status', [OrderController::class, 'updateStatus']);

// Categories routes
Route::apiResource('categories', CategoryController::class);
Route::post('categories/update/{id}', [CategoryController::class, 'updateCategory']);

// Subcategories routes
Route::apiResource('subcategories', SubcategoryController::class);

// Favorites routes
Route::get('users/{userId}/favorites', [FavoriteController::class, 'userFavorite'])->middleware(CheckAdmin::class);
Route::apiResource('favorites', FavoriteController::class);

Route::post('/send-verification-code', [TelegramController::class, 'sendVerificationCode']);

Route::get('/get-updates', [PasswordResetLinkController::class, 'getChatId']);

// new routes 

Route::get('/categories/{id}/markets', [CategoryController::class, 'markets']);
// Route::get('/categories/{id}/markets', [CategoryController::class, 'markets'])->middleware('checkAdmin');
// GET /api/categories/1/markets?search=laptop&page=2&per_page=5
// GET /api/categories/1/markets
 
Route::get('/markets/{id}/categories', [MarketController::class, 'categories']);
// GET /api/markets/1/categories?search=electronics&page=1&per_page=10
// GET /api/markets/1/categories