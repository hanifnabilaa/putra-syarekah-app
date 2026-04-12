<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BillController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProfileController;
use Illuminate\Support\Facades\Route;

// ─── Auth (tidak perlu token) ─────────────────────────────────────────────────
Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
    });

    // ─── Protected (butuh token Sanctum) ─────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('logout',  [AuthController::class, 'logout']);
            Route::get('me',       [AuthController::class, 'me']);
        });

        // Katalog (publik untuk semua authenticated)
        Route::get('products',       [ProductController::class, 'index']);
        Route::get('products/{product}', [ProductController::class, 'show']);

        // Pesanan & Tagihan — hanya untuk role daerah
        Route::middleware('role.daerah')->group(function () {
            Route::apiResource('orders', OrderController::class);
            Route::apiResource('bills',  BillController::class)->only(['index', 'show']);
            Route::get('profile',  [ProfileController::class, 'show']);
            Route::put('profile',  [ProfileController::class, 'update']);
        });
    });
});
