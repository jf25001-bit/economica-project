<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategoria extends Model
{
    use HasFactory;

    
    protected $table = 'sub_categorias';

    protected $fillable = [
        'nombre',
        'categoria_id'
    ];

   
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}
