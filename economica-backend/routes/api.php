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
<<<<<<< HEAD
use App\Http\Controllers\Auth\AuthController;
=======
use App\Http\Controllers\LoteController;
>>>>>>> origin/main
use App\Http\Controllers\ImagenController;
use App\Http\Controllers\ReporteController;

<<<<<<< HEAD
// Esta ruta es para que el botón de tus PDFs funcione de forma directa sin tokens
Route::get('/reportes/general', [ReporteController::class, 'reporteGeneral']);

Route::get('/saludo', function () {
    return response()->json(['mensaje' => 'Hola desde Laravel']);
});


/*
// 2. SISTEMA DE AUTENTICACIÓN Y REPORTES DINÁMICOS

| Mantenemos el prefijo obligatorio 'auth' que tu sistema de Login y 
| las tarjetas del Reportes.vue ya tienen configurados en el Frontend.
*/
Route::prefix('auth')->group(function () {
    // Endpoints públicos de acceso
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Endpoints que requieren Token (Aquí unificamos para evitar el 404 de las tarjetas)
    Route::get('/reportes/tarjetas', [ReporteController::class, 'datosTarjetas']);
    Route::get('/reportes/resumen', [ReporteController::class, 'resumenJson']);
    
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});


/*
|--------------------------------------------------------------------------
| 3. MÓDULOS DE LA BASE DE DATOS (API Resources)
|--------------------------------------------------------------------------
| Los dejamos libres para que tu panel de "Productos.vue" y los demás 
| puedan leer y escribir en la base de datos sin restricciones de tokens por ahora.
*/
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
Route::apiResource('imagenes', ImagenController::class);
=======
Route::apiResource('roles', RolController::class);
Route::apiResource('usuarios', UserController::class);
Route::apiResource('ventas', VentaController::class);
Route::apiResource('compras', CompraController::class);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('proveedores', ProveedorController::class);
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('subcategorias', SubCategoriaController::class);
Route::apiResource('detallecompras', DetalleCompraController::class);
Route::apiResource('detalleventas', DetalleVentaController::class);
Route::apiResource('lotes', LoteController::class);
Route::apiResource('imagenes', ImagenController::class);

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});
>>>>>>> origin/main
