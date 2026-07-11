<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;

use App\Http\Controllers\Api\PerformanceController;
use App\Http\Controllers\Api\PlatformController;
use App\Http\Controllers\Api\ShiftController;

use App\Http\Controllers\Api\EarningController;
use App\Http\Controllers\Api\BonusController;
use App\Http\Controllers\Api\PenaltyController;
use App\Http\Controllers\Api\DeductionController;


Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {


    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);


    Route::get('/dashboard', [DashboardController::class, 'index']);


    Route::get('/platforms', [PlatformController::class, 'index']);


    Route::prefix('performances')->group(function () {

        Route::get('/', [PerformanceController::class, 'index']);

        Route::post('/', [PerformanceController::class, 'store']);

        Route::get('/leaderboard', [PerformanceController::class, 'leaderboard']);

        Route::get('/{id}', [PerformanceController::class, 'show']);

        Route::put('/{id}', [PerformanceController::class, 'update']);

        Route::delete('/{id}', [PerformanceController::class, 'destroy']);

        Route::get('/{id}/analytics', [
            PerformanceController::class,
            'analytics'
        ]);

        Route::get('/{id}/platforms', [
            PerformanceController::class,
            'platforms'
        ]);

    });


    Route::prefix('shifts')->group(function () {

        Route::get('/active', [ShiftController::class, 'active']);

        Route::post('/{performance}/start', [
            ShiftController::class,
            'start'
        ]);

        Route::post('/{shift}/pause', [
            ShiftController::class,
            'pause'
        ]);

        Route::post('/{shift}/resume', [
            ShiftController::class,
            'resume'
        ]);

        Route::post('/{shift}/finish', [
            ShiftController::class,
            'finish'
        ]);

    });


    Route::get('/earnings', [EarningController::class, 'index']);
    Route::post('/earnings', [EarningController::class, 'store']);


    Route::get('/bonuses', [BonusController::class, 'index']);
    Route::post('/bonuses', [BonusController::class, 'store']);

    Route::get('/penalties', [PenaltyController::class, 'index']);
    Route::post('/penalties', [PenaltyController::class, 'store']);

    Route::get('/deductions', [DeductionController::class, 'index']);
    Route::post('/deductions', [DeductionController::class, 'store']);

});