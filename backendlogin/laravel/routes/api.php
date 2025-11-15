<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TwoFactorDisplayController;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Log;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/refresh', function (Request $request) {
    // Log::info('🔁 /api/auth/refresh route reached on login backend');
    // Log::info('🍪 Cookies: ' . json_encode($request->cookies->all()));
    return app(AuthController::class)->refresh($request);
});
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/verify-2fa', [AuthController::class, 'verifyTwoFactor']);
Route::post('/toggle-2fa', [AuthController::class, 'toggleTwoFactor'])->middleware('auth:api');


Route::post('/disable-2fa', [AuthController::class, 'disable2FA']);
Route::post('/verify-recovery-code', [AuthController::class, 'verifyRecoveryCode']);

// Protected routes
Route::middleware(['jwt.auth'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/meInfo', [AuthController::class, 'meInfo']);
    Route::post('/setup-authenticator-2fa', [AuthController::class, 'setupAuthenticator2FA']);
    Route::post('/verify-authenticator-2fa', [AuthController::class, 'verifyAuthenticator2FA']);
    
    // Route::get('/auth/users', [UserController::class, 'index'])->middleware('user.permission:users,read');
    // Route::get('/auth/users/{id}', [UserController::class, 'show'])->middleware('user.permission:users,read');
    // Route::post('/auth/create', [UserController::class, 'create'])->middleware('user.permission:users,create');
    // Route::put('/auth/users/{id}', [UserController::class, 'update'])->middleware('user.permission:users,update');
    // Route::delete('/auth/users/{id}', [UserController::class, 'destroy'])->middleware('user.permission:users,delete');
    // Route::get('/roles', [RoleController::class, 'index'])->middleware('user.permission:users,read');



    //Route::post('/auth/roles', [RoleController::class, 'store']);
});