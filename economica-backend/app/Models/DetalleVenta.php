<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalle_ventas';

    protected $fillable = [
        'cantidad',
        'precio_unitario',
        'subtotal',
        'venta_id',
        'producto_id'
    ];

    // Relación: El detalle pertenece a una venta madre
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    // Relación: El detalle corresponde a un producto específico
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
