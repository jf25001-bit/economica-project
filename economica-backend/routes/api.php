<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SubCategoriaController;

Route::apiResource('roles', RolController::class);
Route::apiResource('ventas', VentaController::class);
Route::apiResource('usuarios', UserController::class);
Route::apiResource('compras', CompraController::class);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('proveedors', ProveedorController::class);
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('subcategorias', SubCategoriaController::class);

Route::get('/saludo', function () {
    return response()->json([
        'mensaje' => 'Hola desde Laravel'
    ]);
});
