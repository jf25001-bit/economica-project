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
use App\Http\Controllers\UnidadMedidaController;

Route::get('/reportes/general', [ReporteController::class, 'reporteGeneral']);

Route::get('/saludo', function () {
    return response()->json(['mensaje' => 'Hola desde Laravel']);
});

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    Route::get('/reportes/tarjetas', [ReporteController::class, 'datosTarjetas']);
    Route::get('/reportes/resumen', [ReporteController::class, 'resumenJson']);
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
Route::apiResource('unidades-medida', UnidadMedidaController::class);
