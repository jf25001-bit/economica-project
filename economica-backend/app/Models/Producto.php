<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'codigo_barras',
        'nombre',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'sub_categoria_id',
        'proveedor_id'
    ];

    public function subcategoria()
    {
        return $this->belongsTo(SubCategoria::class, 'sub_categoria_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function imagenes()
    {
        return $this->hasMany(Imagen::class, 'producto_id');
    }
}