<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SubCategoriaController;
use App\Http\Controllers\DetalleCompraController;
use App\Http\Controllers\DetalleVentaController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\ReporteController;

Route::get('/saludo', function () {
    return response()->json(['mensaje' => 'Hola desde Laravel']);
});

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    Route::apiResource('roles', RolController::class);
    Route::apiResource('usuarios', UserController::class);
    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('subcategorias', SubCategoriaController::class);
    Route::apiResource('productos', ProductoController::class);
    Route::apiResource('proveedores', ProveedorController::class);
    Route::apiResource('compras', CompraController::class);
    Route::apiResource('ventas', VentaController::class);
    Route::apiResource('detallecompras', DetalleCompraController::class);
    Route::apiResource('detalleventas', DetalleVentaController::class);
    Route::apiResource('lotes', LoteController::class);
    Route::apiResource('imagenes', ImagenController::class);
});

// ESTO TIENE QUE ESTAR AQUÍ, TOTALMENTE AFUERA Y LIBRE
Route::prefix('reportes')->group(function () {
    Route::get('/general', [ReporteController::class, 'reporteGeneral']);
    Route::get('/tarjetas', [ReporteController::class, 'datosTarjetas']);
    Route::get('/resumen', [ReporteController::class, 'resumenJson']);
    Route::get('/inventario', [ReporteController::class, 'inventario']);
    Route::get('/ventas', [ReporteController::class, 'ventasFinancieras']);
    Route::get('/balance', [ReporteController::class, 'balanceFlujo']);
});