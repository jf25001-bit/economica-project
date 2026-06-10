<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'cliente',
        'total',
        'user_id'
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    // Relación: Una venta pertenece a un usuario/empleado
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación: Una venta tiene muchos detalles/productos desglosados
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }
}
