<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;

Route::get('/saludo', function () {
    return response()->json([
        'mensaje' => 'Hola desde Laravel'
    ]);
});