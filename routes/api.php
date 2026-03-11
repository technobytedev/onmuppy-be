<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\VendorController;


Route::get('/', function () {
    return 'Hello Onmuppy API!';
});

// ─── Public routes (auth limiter: 10 req/min) ─────────────────────
Route::middleware('throttle:auth')->group(function () {
    Route::post('/login',    [LoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'register']);
});

// ── Public browsing ────────────────────────────────────────────
Route::get('/services',          [ServiceController::class, 'browse']);
Route::get('/services/{service}', [ServiceController::class, 'show']);
Route::get('/vendors', [VendorController::class, 'index']);


// ─── Password routes (stricter: 5 req/15min) ─────────────────────
Route::middleware('throttle:forgot-password')->group(function () {
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
    Route::post('/reset-password',  [ForgotPasswordController::class, 'resetPassword']);
});


// ─── Protected routes (general: 60 req/min) ───────────────────────
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::post('/logout',     [LoginController::class, 'logout']);
    Route::post('/logout-all', [LoginController::class, 'logoutAll']);


    // Vendor service management
    Route::get ('/my/vendor', [VendorController::class, 'show']);
    Route::post('/my/vendor', [VendorController::class, 'store']);

    // ── Service CRUD for vendors (with availability toggle) ─────────────────────────────
    Route::get   ('/my/services',                    [ServiceController::class, 'index']);
    Route::post  ('/my/services',                    [ServiceController::class, 'store']);
    Route::put   ('/my/services/{service}',          [ServiceController::class, 'update']);
    Route::delete('/my/services/{service}',          [ServiceController::class, 'destroy']);
    Route::patch ('/my/services/{service}/toggle',   [ServiceController::class, 'toggleAvailability']);
    
});
