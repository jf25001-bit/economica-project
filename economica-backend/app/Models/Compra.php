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
        'fecha_llegada',
        'estado',
        'total'
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'compra_id');
    }
}