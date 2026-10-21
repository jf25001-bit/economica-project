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
use App\Http\Controllers\CajaController;

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

    Route::prefix('caja')->group(function () {
        Route::get('estado', [CajaController::class, 'estadoActual']);
        Route::post('abrir', [CajaController::class, 'abrir']);
        Route::post('cerrar', [CajaController::class, 'cerrar']);
        Route::get('activas', [CajaController::class, 'cajasActivas']);
        Route::get('historial', [CajaController::class, 'historial']);
        Route::post('forzar-cierre/{id}', [CajaController::class, 'forzarCierre']);
    });

    Route::prefix('reportes')->group(function () {
        Route::get('general', [ReporteController::class, 'reporteGeneral']);
    });

    Route::apiResource('ventas', VentaController::class);
    Route::patch('compras/{id}/anular', [CompraController::class, 'anular']);
    Route::apiResource('compras', CompraController::class)->only(['index', 'store', 'show']);
    Route::apiResource('usuarios', UserController::class);
    Route::apiResource('roles', RolController::class);
    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('subcategorias', SubCategoriaController::class);
    Route::patch('productos/{id}/stock-minimo', [ProductoController::class, 'actualizarStockMinimo']);
    Route::patch('productos/{id}/reactivar', [ProductoController::class, 'reactivar']);
    Route::apiResource('productos', ProductoController::class);
    Route::apiResource('proveedores', ProveedorController::class);
    Route::apiResource('detallecompras', DetalleCompraController::class);
    Route::apiResource('detalleventas', DetalleVentaController::class);
    Route::patch('lotes/{id}/reducir', [LoteController::class, 'reducir']);
    Route::apiResource('lotes', LoteController::class);
    Route::apiResource('imagenes', ImagenController::class);
    Route::apiResource('unidades-medida', UnidadMedidaController::class);
});