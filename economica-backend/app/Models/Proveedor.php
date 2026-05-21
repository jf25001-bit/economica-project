<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Compra;


class Proveedor extends Model
{
   protected $table = 'proveedores';

    protected $fillable = [
        'nombre_proveedor',
        'telefono',
        'direccion'
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_proveedor');
    }
}


