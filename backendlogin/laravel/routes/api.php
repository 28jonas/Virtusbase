<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/refresh', function (Request $request) {
    Log::info('🔁 /api/auth/refresh route reached on login backend');
    Log::info('🍪 Cookies: ' . json_encode($request->cookies->all()));
    return app(AuthController::class)->refresh($request);
});

// Protected routes
Route::middleware(['jwt.auth'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::get('/me', [AuthController::class,'me']);
});