<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\BannerController;
use App\Http\Controllers\Api\Admin\BrandController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\AboutController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\InventoryController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\AccountController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserOrderController;
use App\Http\Controllers\Api\WishlistController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
//HomePage
Route::get('homescreen', [HomeController::class, 'index']);
Route::get('categories', [HomeController::class, 'getcategory']);
Route::get('brands', [HomeController::class, 'getbrands']);
Route::post('contact', [UserController::class, 'contact']);
Route::get('get/product/{slug}', [HomeController::class, 'product']);
// CART
Route::post('addToCart/{slug}', [CartController::class, 'addToCart']);
Route::delete('cart/removeItem', [CartController::class, 'remove']);
Route::get('carts', [CartController::class, 'index']);
Route::put('cart/{cart}/update', [CartController::class, 'update']);
Route::get('totalitems', [CartController::class, 'totalitemsincart']);
Route::get('search', [HomeController::class, 'search']);
Route::get('cart/{cart}/checkout', [CheckoutController::class, 'getcheckoutdetails']);

//wishlist
Route::get('getwishlist', [WishlistController::class, 'index']);
Route::post('addToWishlist/{slug}', [WishlistController::class, 'add']);
Route::post('removefromWishlist/{id}', [WishlistController::class, 'remove']);
Route::get('totalwishlistitems', [WishlistController::class, 'totalitems']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('Customers', [AccountController::class, 'index']);
    Route::get('orders/customer/{customerId}', [UserOrderController::class, 'orderByCustomerId']);


    Route::post('logout', [AuthController::class, 'logout']);

    Route::middleware('role:1')->group(function () {
        Route::get('/admin', function () {
            return response()->json(['message' => 'Welcome Admin']);
        });


        Route::apiResource('banner', BannerController::class);
        Route::apiResource('brand', BrandController::class);
        Route::apiResource('category', CategoryController::class);
        Route::apiResource('about', AboutController::class);
        Route::apiResource('product', ProductController::class);
        Route::get('inventory/add/{inventory}', [InventoryController::class, 'add']);
        Route::post('inventory/store', [InventoryController::class, 'store']);
        Route::get('inventory/index', [InventoryController::class, 'index']);
        Route::post('inventory/{inventory}/update', [InventoryController::class, 'update']);
        Route::post('inventory/{inventory}/delete', [InventoryController::class, 'destroy']);
        Route::get('inventory/{inventory}/get', [InventoryController::class, 'show']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('order/{order}', [OrderController::class, 'show']);
        Route::get('Customers', [AccountController::class, 'index']);
        Route::get('contactedUsers', [AccountController::class, 'contactedUsers']);
        Route::get(
            'notifications',
            [NotificationController::class, 'getallnotifications']
        );
        Route::post('send/notification', [NotificationController::class, 'sendnotification']);
    });

    Route::middleware('role:2')->group(function () {
        Route::get('/user/dashboard', function () {
            return response()->json(['message' => 'Welcome User']);
        });

        Route::post('cart/{cart}/checkout', [CheckoutController::class, 'checkout']);
    });
});
