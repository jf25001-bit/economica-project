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
        'fecha_cierre'
    ];
}