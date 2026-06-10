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
    public function compra()
    {
        return $this->belongsTo(
            Compra::class,
            'compra_id'
        );
    }

    public function producto()
    {
        return $this->belongsTo(
            Producto::class,
            'producto_id'
        );
    }
     public function lotes()
    {
        return $this->hasMany(
            Lote::class,
            'detalle_compra_id'
        );
    }
}
