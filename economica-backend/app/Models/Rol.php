<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    // Le indicamos a Laravel que la tabla se llama 'roles'
    protected $table = 'rols';

    // Campos que permitiremos llenar mediante formularios/API
    protected $fillable = [
        'nombre',
        'descripcion'
    ];
}
