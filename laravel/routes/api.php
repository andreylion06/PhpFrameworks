<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\TableReservationController;
use App\Http\Controllers\ProductController;

// Публічний логін
Route::post('/login', [AuthController::class, 'login']);

// Захищені маршрути — потрібен Bearer Token
Route::middleware('auth:api')->group(function () {

    // Інформація про авторизованого користувача
    Route::get('/me', [AuthController::class, 'me']);

    // Ресурсні API (повертають JSON)
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('table-reservations', TableReservationController::class);
    Route::apiResource('menu-items', MenuItemController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('order-items', OrderItemController::class);

    // Продукти — окремо оголошені (ручні)
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'getProducts'])->name('get_products');
        Route::get('/{id}', [ProductController::class, 'getProductItem'])->name('get_product_item');
        Route::post('/', [ProductController::class, 'createProduct'])->name('post_products');
        Route::put('/{id}', [ProductController::class, 'updateProduct'])->name('update_product');
        Route::delete('/{id}', [ProductController::class, 'deleteProduct'])->name('delete_product');
    });
});
