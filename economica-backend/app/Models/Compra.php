<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $table = 'compras';
    protected $fillable = ['total', 'proveedor_id', 'user_id'];

    public function proveedor() {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function usuario() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles() {
        return $this->hasMany(DetalleCompra::class, 'compra_id');
    }
}

