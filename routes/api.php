<?php

use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\StatsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// Auth
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// Publik: katalog & checkout
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:20,1');

Route::middleware('auth:sanctum')->group(function () {
    // Customer
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    // Admin
    Route::middleware('admin')->group(function () {
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);

        Route::prefix('admin')->group(function () {
            Route::get('/orders', [AdminOrderController::class, 'index']);
            Route::get('/orders/{order}', [AdminOrderController::class, 'show']);
            Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
            Route::get('/stats', StatsController::class);
        });
    });
});
