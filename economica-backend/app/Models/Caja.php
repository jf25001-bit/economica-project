<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'cajas';

    protected $fillable = [
    'user_id',
    'monto_apertura',
    'monto_cierre',
    'total_ventas',
    'estado',
    'fecha_apertura',
    'fecha_cierre',
    'observacion',
    'cerrado_por_id',
];

public function usuario()
{
    return $this->belongsTo(\App\Models\User::class, 'user_id');
}

public function cerradoPor()
{
    return $this->belongsTo(\App\Models\User::class, 'cerrado_por_id');
}
}