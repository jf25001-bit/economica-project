<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'fecha_venta',
        'cliente',
        'total'
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }
}