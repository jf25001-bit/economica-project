<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proveedor extends Model
{
    use HasFactory;

    // Vinculamos el modelo con la tabla en español
    protected $table = 'proveedors';

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'direccion'
    ];
}
