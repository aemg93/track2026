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
    | MODELS (ÚNICA FUENTE DE VERDAD)
    |--------------------------------------------------------------------------
    */

    // Route::get('/models', [PerformanceController::class, 'index']);
    // Route::post('/models', [PerformanceController::class, 'store']);
    // Route::get('/models/{id}', [PerformanceController::class, 'show']);
    // Route::put('/models/{id}', [PerformanceController::class, 'update']);
    // Route::delete('/models/{id}', [PerformanceController::class, 'destroy']);

      Route::prefix('models')->group(function () {

    Route::get('/', [PerformanceController::class, 'index']);
    Route::post('/', [PerformanceController::class, 'store']);
    Route::get('/{id}', [PerformanceController::class, 'show']);
    Route::put('/{id}', [PerformanceController::class, 'update']);
    Route::delete('/{id}', [PerformanceController::class, 'destroy']);

});

Route::prefix('performances')->group(function () {

    Route::get('/', [PerformanceController::class, 'index']);
    Route::post('/', [PerformanceController::class, 'store']);
    Route::get('/{id}', [PerformanceController::class, 'show']);
    Route::put('/{id}', [PerformanceController::class, 'update']);
    Route::delete('/{id}', [PerformanceController::class, 'destroy']);




});
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