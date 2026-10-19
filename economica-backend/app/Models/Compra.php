<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';

    protected $fillable = [
        'fecha_compra',
        'total',
        'anulado',
        'anulado_at'
    ];

    protected $casts = [
        'anulado'    => 'boolean',
        'anulado_at' => 'datetime',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'compra_id');
    }
}