<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Password reset URL fix
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return env('FRONTEND_URL') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
        });

        // ─── Rate Limiters ────────────────────────────────────────

        // General API — 60 requests/min per IP
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many requests. Please slow down.',
                    ], 429);
                });
        });

        // Auth routes — 10 attempts/min per IP (login, register)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many attempts. Try again in a minute.',
                    ], 429);
                });
        });

        // Forgot password — 5 attempts per 15 min per IP
        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perMinutes(15, 5)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many password reset attempts. Try again later.',
                    ], 429);
                });
        });
    }
}