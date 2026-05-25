<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    use HasFactory;

    // Indicamos explícitamente el nombre de la tabla
    protected $table = 'detalle_compras';

    // Habilitamos los campos para que Laravel permita la inserción masiva desde el Controlador
    protected $fillable = [
        'cantidad',
        'precio_compra',
        'subtotal',
        'compra_id',
        'producto_id'
    ];

    // RELACIÓN: El renglón del detalle pertenece a una compra madre
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    // RELACIÓN: El detalle corresponde a un producto específico del catálogo
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
