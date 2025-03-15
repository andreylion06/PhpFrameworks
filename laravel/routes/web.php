<?php

use App\Http\Controllers\TestController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::get('test', [TestController::class, 'test']);

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'getProducts'])->name('get_products');
    Route::get('/{id}', [ProductController::class, 'getProductItem'])->name('get_product_item');
    Route::post('/', [ProductController::class, 'createProduct'])->name('post_products');
    Route::put('/{id}', [ProductController::class, 'updateProduct'])->name('update_product');
    Route::delete('/{id}', [ProductController::class, 'deleteProduct'])->name('delete_product');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
