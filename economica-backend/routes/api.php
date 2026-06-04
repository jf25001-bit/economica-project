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
use App\Http\Controllers\DetalleCompraController;
use App\Http\Controllers\DetalleVentaController;
use App\Http\Controllers\Auth\AuthController;

use App\Http\Controllers\ImagenController;

Route::apiResource('roles', RolController::class);
Route::apiResource('ventas', VentaController::class);
Route::apiResource('usuarios', UserController::class);
Route::apiResource('compras', CompraController::class);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('proveedors', ProveedorController::class);
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('subcategorias', SubCategoriaController::class);




// Ruta de prueba
Route::get('/saludo', function () {
    return response()->json([
        'mensaje' => 'Hola desde Laravel'
    ]);
});

// Rutas API Resource
Route::apiResource('compras', CompraController::class);
Route::apiResource('proveedores', ProveedorController::class);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('ventas', VentaController::class);
Route::apiResource('detallecompras', DetalleCompraController::class);
Route::apiResource('detalleventas', DetalleVentaController::class);

Route::apiResource('imagenes', ImagenController::class);












Route::prefix('auth')->group(function(){
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function(){
        Route::get('me',[AuthController::class, 'me']);
        Route::post('logout',[AuthController::class, 'logout']);
        Route::post('refresh',[AuthController::class, 'refresh']);
    });
});
