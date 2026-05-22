<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    // Forzamos el nombre de la tabla en español
    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];
}
