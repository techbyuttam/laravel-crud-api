<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'getAll']);
    Route::get('/{id}', [UserController::class, 'getById']);
    Route::post('/', [UserController::class, 'add']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/truncate', [UserController::class, 'truncateAll']);
    Route::delete('/{id}', [UserController::class, 'delete']);
});

use App\Http\Controllers\LogController;

Route::prefix('logs')->group(function () {
    Route::delete('/clear', [LogController::class, 'clearAllLogs']);
    Route::delete('/clear/{date}', [LogController::class, 'clearLogsByDate']);
    Route::get('/{date}', [LogController::class, 'getLogsByDate']);
});

use App\Http\Controllers\SystemController;

Route::get('/system/optimize-clear', [SystemController::class, 'optimizeClear']);
