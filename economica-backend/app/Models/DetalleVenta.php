<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $fillable = [
    'cantidad',
    'precio_venta',
    'venta_id',
    'producto_id'
];
}
