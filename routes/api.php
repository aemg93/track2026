<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PerformanceController;
use App\Http\Controllers\Api\EarningController;
use App\Http\Controllers\Api\BonusController;
use App\Http\Controllers\Api\PenaltyController;
use App\Http\Controllers\Api\DeductionController;

/*
|--------------------------------------------------------------------------
| AUTH PUBLIC
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (SANCTUM)
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
    | MODELS (ALIAS REAL)
    |--------------------------------------------------------------------------
    */
    Route::get('/models', [PerformanceController::class, 'index']);
    Route::get('/models/{id}', [PerformanceController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | RAW PERFORMANCES (ADMIN)
    |--------------------------------------------------------------------------
    */
    Route::get('/performances', [PerformanceController::class, 'index']);
    Route::get('/performances/{id}', [PerformanceController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | FINANCIAL MODULES
    |--------------------------------------------------------------------------
    */
    Route::get('/earnings', [EarningController::class, 'index']);

    Route::get('/bonuses', [BonusController::class, 'index']);
    Route::post('/bonuses', [BonusController::class, 'store']);

    Route::get('/penalties', [PenaltyController::class, 'index']);
    Route::post('/penalties', [PenaltyController::class, 'store']);

    Route::get('/deductions', [DeductionController::class, 'index']);
    Route::post('/deductions', [DeductionController::class, 'store']);
});