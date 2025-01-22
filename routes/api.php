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
Route::post('users/update/{id}', [UserController::class, "updateuser"]);
Route::middleware(['checkAdmin'])->group(function () {
    Route::apiResource('users', UserController::class)->except(['show']);
    Route::get('/users/vendors', [UserController::class, "getVendors"]);
});
Route::apiResource('users', UserController::class)->only(['show']);


// Markets routes
Route::middleware('checkVendor')->group(function () {
    Route::apiResource('markets', MarketController::class)->except(['index','show','store']);
    Route::get('/market/titles', [MarketController::class, 'marketsTitles']);
    Route::post('markets/update/{id}', [MarketController::class,"updateMarket"]);
});
Route::apiResource('markets',MarketController::class)->only('store')->middleware('checkAdmin');
Route::apiResource('markets', MarketController::class)->only(['index','show']);
Route::post('markets/rate/{id}', [MarketController::class, 'rateMarket'])->name('markets.rate');
route::get('/market/toprate', [MarketController::class, 'MarketTopRate']);
Route::get('/markets/{id}/categories', [MarketController::class, 'categories']);

// Products routes
Route::middleware('checkVendor')->group(function () {
    Route::apiResource('products', ProductController::class)->except(['index','show']);
});
Route::apiResource('products', ProductController::class)->only(['index','show']);

Route::post('products/rate/{id}', [ProductController::class, 'rateProduct'])->name('products.rate');
route::get('product/toprate', [ProductController::class, 'productTopRate']);

// Images routes
Route::apiResource('images', productImageController::class)->middleware('checkVendor');

// Carts routes
Route::apiResource('carts', CartController::class);
Route::post('/carts/{cart}/add-item', [CartController::class, 'addItem'])->name('carts.addItem');
Route::delete('/carts/{cart}/remove-item/{item}', [CartController::class, 'removeItem'])->name('carts.removeItem');

// Cart items routes
Route::apiResource('cart-items', CartItemController::class);

// Orders routes
Route::get('/orders/user/{customerId}', [OrderController::class, 'getCustomerOrders']);
Route::put('/orders/{orderId}/status', [OrderController::class, 'updateStatus'])->middleware('checkDeliveryMan');
Route::apiResource('orders', OrderController::class);

// Categories routes
Route::apiResource('categories', CategoryController::class)->except(['store','destroy']);
Route::apiResource('categories', CategoryController::class)->only(['store','destroy'])->middleware('checkAdmin');
Route::get('categories/{id}/products', [CategoryController::class, 'products']);
Route::post('categories/update/{id}', [CategoryController::class, 'updateCategory'])->middleware('checkAdmin');
route::get('/category/titles', [CategoryController::class, 'categoriesTitles'])->middleware('checkVendor');
Route::get('/categories/{id}/markets', [CategoryController::class, 'markets']);

// Subcategories routes
Route::apiResource('subcategories', SubcategoryController::class)->except(['store','destroy','update']);
Route::apiResource('subcategories', SubcategoryController::class)->only(['store','destroy','update'])->middleware('checkAdmin');
route::get('/subcategory/titles', [SubcategoryController::class, 'subcategoriesTitles'])->middleware('checkVendor');

// Favorites routes
// ->middleware(CheckAdmin::class)
Route::get('users/{userId}/favorites', [FavoriteController::class, 'userFavorite']);
Route::get('/favorites/check/{userId}/{productId}', [FavoriteController::class, 'isProductInFavorites']);
Route::apiResource('favorites', FavoriteController::class);

Route::post('/send-verification-code', [TelegramController::class, 'sendVerificationCode']);

Route::get('/get-updates', [PasswordResetLinkController::class, 'getChatId']);

// new routes 

// Route::get('/categories/{id}/markets', [CategoryController::class, 'markets'])->middleware('checkAdmin');
// GET /api/categories/1/markets?search=laptop&page=2&per_page=5
// GET /api/categories/1/markets
 