<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ForgotPasswordController;


Route::get('/', function () {
    return 'Hello Onmuppy API!';
});

// ─── Public routes (auth limiter: 10 req/min) ─────────────────────
Route::middleware('throttle:auth')->group(function () {
    Route::post('/login',    [LoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'register']);
});

// ─── Password routes (stricter: 5 req/15min) ──────────────────────
Route::middleware('throttle:forgot-password')->group(function () {
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
    Route::post('/reset-password',  [ForgotPasswordController::class, 'resetPassword']);
});

// ─── Protected routes (general: 60 req/min) ───────────────────────
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::post('/logout',     [LoginController::class, 'logout']);
    Route::post('/logout-all', [LoginController::class, 'logoutAll']);

    
});