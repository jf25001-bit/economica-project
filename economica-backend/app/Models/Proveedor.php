<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Compra;
use App\Models\Producto;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre_proveedor',
        'telefono',
        'direccion'
    ];


    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_proveedor')
                    ->withTimestamps();
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_proveedor');
    }
}