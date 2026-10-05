<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PerfilController;
use App\Http\Controllers\Api\HijoController;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [PerfilController::class, 'show']);
        Route::get('/hijos', [HijoController::class, 'index']);
        Route::get('/hijos/{alumno}', [HijoController::class, 'show']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
