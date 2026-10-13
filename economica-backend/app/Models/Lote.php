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

    protected $casts = [
        'vencido_descontado_at' => 'datetime',
    ];

    // withTrashed: el historial sigue mostrando productos que ya fueron eliminados
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id')->withTrashed();
    }

    public function detalleCompra()
    {
        return $this->belongsTo(DetalleCompra::class, 'detalle_compra_id');
    }

    
    public static function hoy(): string
    {
        return now('America/El_Salvador')->toDateString();
    }

    
    public function estaVencido(): bool
    {
        $fecha = substr((string) $this->fecha_expiracion, 0, 10);

        return $fecha !== '' && $fecha < self::hoy();
    }
}