<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DetalleCompraController;
use App\Http\Controllers\DetalleVentaController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SubCategoriaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

Route::apiResource('proveedores', ProveedorController::class);
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('subcategorias', SubCategoriaController::class);
Route::apiResource('roles', RolController::class);
Route::apiResource('ventas', VentaController::class);
Route::apiResource('usuarios', UserController::class);
Route::apiResource('compras', CompraController::class);
Route::apiResource('detallecompras', DetalleCompraController::class);
Route::apiResource('detalleventas', DetalleVentaController::class);
Route::apiResource('lotes', LoteController::class);

Route::get('/productos/resumen', [ProductoController::class, 'resumen']);
Route::apiResource('productos', ProductoController::class);

Route::get('me', [AuthController::class, 'me']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('/saludo', function () {
    return response()->json([
        'mensaje' => 'Hola desde Laravel',
    ]);
});
