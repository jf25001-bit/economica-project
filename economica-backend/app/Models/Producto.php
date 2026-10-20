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
        'margen_porcentaje',
        'precio_automatico',
        'stock',
        'stock_minimo',
        'sub_categoria_id',
        'unidad_medida_id',
    ];

    protected $casts = [
        'precio_automatico' => 'boolean',
        'anulado' => 'boolean',
    ];

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'producto_proveedor')
                    ->withTimestamps();
    }

    public function subcategoria()
    {
        return $this->belongsTo(SubCategoria::class, 'sub_categoria_id');
    }

    public function imagenes()
    {
        return $this->hasMany(Imagen::class, 'producto_id');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    public function lotes()
    {
        return $this->hasMany(Lote::class, 'producto_id');
    }

    public function calcularCostoUnitario(): ?float
    {
        $tablaLotes = (new Lote)->getTable();
        $tablaDetalles = (new DetalleCompra)->getTable();

        $filas = Lote::query()
            ->join($tablaDetalles, "$tablaDetalles.id", '=', "$tablaLotes.detalle_compra_id")
            ->where("$tablaLotes.producto_id", $this->id)
            ->where("$tablaLotes.cantidad_actual", '>', 0)
            ->get([
                "$tablaLotes.cantidad_actual",
                "$tablaDetalles.precio_compra",
                "$tablaDetalles.unidades_por_paquete",
            ]);

        $unidades = 0;
        $valorTotal = 0.0;

        foreach ($filas as $fila) {
            $porPaquete = max(1, (int) $fila->unidades_por_paquete);
            $costoUnidad = ((float) $fila->precio_compra) / $porPaquete;
            $cantidad = (float) $fila->cantidad_actual;

            $unidades += $cantidad;
            $valorTotal += $costoUnidad * $cantidad;
        }

        if ($unidades > 0) {
            return $valorTotal / $unidades;
        }

        $ultimo = DetalleCompra::where('producto_id', $this->id)
            ->whereHas('compra', fn ($q) => $q->where('anulado', false))
            ->latest('id')
            ->first();

        if ($ultimo) {
            return ((float) $ultimo->precio_compra) / max(1, (int) $ultimo->unidades_por_paquete);
        }

        return null;
    }

    public function recalcularPrecios(): void
    {
        $costo = $this->calcularCostoUnitario();

        if ($costo === null) {
            return;
        }

        $this->precio_compra = round($costo, 4);

        if ($this->precio_automatico && $this->margen_porcentaje !== null) {
            $this->precio_venta = self::redondearPrecio(
                $costo * (1 + ((float) $this->margen_porcentaje) / 100)
            );
        }

        $this->save();
    }

    public static function redondearPrecio(float $precio): float
    {
        $centavos = round($precio * 100, 4);

        return (ceil($centavos / 5) * 5) / 100;
    }
}