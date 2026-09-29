<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    use HasFactory;

    protected $table = 'detalle_compras';

    protected $fillable = [
        'compra_id',
        'producto_id',
        'proveedor_id', 
        'cantidad',
        'unidades_por_paquete',
        'precio_compra',
        'subtotal'
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

    
    public function proveedor()
    {
        return $this->belongsTo(
            Proveedor::class,
            'proveedor_id'
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