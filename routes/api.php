<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PerfilController;
use App\Http\Controllers\Api\HijoController;
use App\Http\Controllers\Api\CuotaController;
use App\Http\Controllers\Api\PasswordController;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [PerfilController::class, 'show']);
        Route::get('/hijos', [HijoController::class, 'index']);
        Route::get('/hijos/{alumno}', [HijoController::class, 'show']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Parte 3: cuotas, facturas y pagos (solo padres)
        Route::get('/hijos/{alumno}/facturas', [CuotaController::class, 'facturas']);
        Route::get('/hijos/{alumno}/comprobantes', [CuotaController::class, 'comprobantes']);
        Route::get('/hijos/{alumno}/deuda', [CuotaController::class, 'deuda']);
        Route::get('/facturas/{factura}', [CuotaController::class, 'mostrar']);
        Route::get('/facturas/{factura}/pdf', [CuotaController::class, 'pdfFactura']);
        Route::post('/facturas/{factura}/comprobantes', [CuotaController::class, 'subirComprobante']);
        Route::post('/facturas/{factura}/pagar', [CuotaController::class, 'pagar']);
        Route::get('/pagos/{pago}/pdf', [CuotaController::class, 'pdfPago']);
        Route::get('/comprobantes/{comprobante}/archivo', [CuotaController::class, 'archivo']);
        Route::post('/cambiar-password', [PasswordController::class, 'cambiar']);
    });
});
