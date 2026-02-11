<?php

use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\UploadController as AdminUploadController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ContactInfoController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::get('products', [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);
Route::get('products/{product}/reviews', [ReviewController::class, 'index']);
Route::get('contact-info', [ContactInfoController::class, 'show']);
Route::post('contact', [ContactController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('cart', [CartController::class, 'show']);
    Route::post('cart/items', [CartController::class, 'store']);
    Route::patch('cart/items/{cartItem}', [CartController::class, 'update']);
    Route::delete('cart/items/{cartItem}', [CartController::class, 'destroy']);

    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);

    Route::post('products/{product}/reviews', [ReviewController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);

    Route::get('admin/orders', [AdminOrderController::class, 'index']);
    Route::patch('admin/orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
    Route::post('admin/uploads', [AdminUploadController::class, 'store']);
    Route::put('admin/contact-info', [ContactInfoController::class, 'update']);
});
