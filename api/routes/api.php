<?php

declare(strict_types=1);

use App\Presentation\Controllers\AuthController;
use App\Presentation\Controllers\CategoryController;
use App\Presentation\Controllers\HealthController;
use App\Presentation\Controllers\MediaController;
use App\Presentation\Controllers\ProductController;
use App\Presentation\Controllers\ReportController;
use App\Presentation\Controllers\SaleController;
use App\Presentation\Middleware\JwtAuthMiddleware;
use App\Presentation\Middleware\RequireAdminMiddleware;
use Illuminate\Support\Facades\Route;

// Health check endpoint (anonymous, direct on :8000/health per spec E-14)
Route::get('/health', [HealthController::class, 'health']);

// Media endpoint (anonymous, served by api on /media/{key} per spec E-15)
Route::get('/media/{key}', [MediaController::class, 'show']);

// Public auth routes (with brute-force protection rate limiting: 20 req/min)
Route::post('/api/auth/login', [AuthController::class, 'login'])->middleware('throttle:20,1');

// Protected routes (any authenticated user)
Route::middleware([JwtAuthMiddleware::class])->group(function () {
    Route::get('/api/categories', [CategoryController::class, 'index']);
    Route::get('/api/products', [ProductController::class, 'index']);
    Route::get('/api/products/{id}', [ProductController::class, 'show']);

    Route::post('/api/sales', [SaleController::class, 'store']);
    Route::get('/api/sales', [SaleController::class, 'index']);
    Route::get('/api/sales/{id}', [SaleController::class, 'show']);

    Route::get('/api/reports/sales', [ReportController::class, 'sales']);

    // Admin-only routes
    Route::middleware([RequireAdminMiddleware::class])->group(function () {
        Route::post('/api/auth/register', [AuthController::class, 'register']);
        Route::post('/api/products', [ProductController::class, 'store']);
        Route::put('/api/products/{id}', [ProductController::class, 'update']);
        Route::delete('/api/products/{id}', [ProductController::class, 'destroy']);
        Route::post('/api/products/{id}/image', [ProductController::class, 'uploadImage']);
    });
});
