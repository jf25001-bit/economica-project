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
        'categoria_id',
        'sub_categoria_id',
        'proveedor_id',
        'imagen'
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Relación: El producto pertenece a una Subcategoría.
     */
    public function subcategoria()
    {
        return $this->belongsTo(SubCategoria::class, 'sub_categoria_id');
    }

    /**
     * Relación: El producto es surtido por un Proveedor.
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }
}
