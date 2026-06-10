<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
      protected $table = 'lotes';

    protected $fillable = [
        'detalle_compra_id',
        'codigo_lote',
        'fecha_expiracion',
        'cantidad_inicial',
        'cantidad_actual',
        'producto_id'
    ];

    public function producto()
    {
        return $this->belongsTo(
            Producto::class,
            'producto_id'
        );
    }

    public function detalleCompra()
    {
        return $this->belongsTo(
            DetalleCompra::class,
            'detalle_compra_id'
        );
    }
}
