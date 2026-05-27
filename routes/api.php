<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PerformanceController;
use App\Http\Controllers\Api\EarningController;
use App\Http\Controllers\Api\BonusController;
use App\Http\Controllers\Api\PenaltyController;

/*
|--------------------------------------------------------------------------
| AUTH PUBLIC
|--------------------------------------------------------------------------
| TOKEN-BASED SANCTUM AUTH
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (SANCTUM TOKEN GUARD)
|--------------------------------------------------------------------------
| Requiere:
| Authorization: Bearer {token}
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | USER
    |--------------------------------------------------------------------------
    */
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | PERFORMANCES
    |--------------------------------------------------------------------------
    */
    Route::get('/performances', [PerformanceController::class, 'index']);
    Route::get('/performances/{id}', [PerformanceController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | EARNINGS
    |--------------------------------------------------------------------------
    */
    Route::get('/earnings', [EarningController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | BONUSES
    |--------------------------------------------------------------------------
    */
    Route::get('/bonuses', [BonusController::class, 'index']);
    Route::post('/bonuses', [BonusController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | PENALTIES
    |--------------------------------------------------------------------------
    */
    Route::get('/penalties', [PenaltyController::class, 'index']);
    Route::post('/penalties', [PenaltyController::class, 'store']);
});