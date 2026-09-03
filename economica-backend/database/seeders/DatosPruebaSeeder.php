<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\SubCategoria;
use App\Models\Proveedor;
use App\Models\UnidadMedida;
use App\Models\Producto;

class DatosPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Insertar Unidades de Medida
        $unidades = [
            'Pieza', 'Kilogramo', 'Gramo', 'Litro', 'Mililitro', 
            'Caja', 'Paquete', 'Metro', 'Lata', 'Bolsa', 'Libra', 'Onza', 'Galón'
        ];

        foreach ($unidades as $unidad) {
            UnidadMedida::firstOrCreate(['nombre' => $unidad]);
        }

        // 2. Insertar Proveedores
        $proveedor1 = Proveedor::firstOrCreate(
            ['nombre_proveedor' => 'Distribuidora Global S.A.'],
            ['telefono' => '555-1234', 'direccion' => 'Av. Principal #123']
        );

        $proveedor2 = Proveedor::firstOrCreate(
            ['nombre_proveedor' => 'Comercializadora El Mayorista'],
            ['telefono' => '555-5678', 'direccion' => 'Zona Industrial Calle 4']
        );

        // 3. Insertar Categorías y Subcategorías
        $categoriaBebidas = Categoria::firstOrCreate(
            ['nombre' => 'Bebidas'],
            ['descripcion' => 'Bebidas con y sin gas, jugos y aguas']
        );

        $subCatGaseosas = SubCategoria::firstOrCreate([
            'nombre' => 'Gaseosas',
            'categoria_id' => $categoriaBebidas->id
        ]);

        $categoriaAbarrotes = Categoria::firstOrCreate(
            ['nombre' => 'Abarrotes'],
            ['descripcion' => 'Productos de consumo diario para el hogar']
        );

        $subCatEnlatados = SubCategoria::firstOrCreate([
            'nombre' => 'Enlatados',
            'categoria_id' => $categoriaAbarrotes->id
        ]);

        // 4. Obtener referencias de unidades de medida creadas
        $unidadMedidaPieza = UnidadMedida::where('nombre', 'Pieza')->first();
        $unidadMedidaLata = UnidadMedida::where('nombre', 'Lata')->first();

        // 5. Insertar Productos y asociarlos con Proveedores (tabla pivote)
        $producto1 = Producto::firstOrCreate(
            ['codigo_barras' => '75010001001'],
            [
                'nombre' => 'Refresco de Cola 600ml',
                'precio_venta' => 18.00,
                'stock' => 5,
                'stock_minimo' => 10,
                'sub_categoria_id' => $subCatGaseosas->id,
                'unidad_medida_id' => $unidadMedidaPieza ? $unidadMedidaPieza->id : 1,
            ]
        );
        // Sincronizar relación muchos a muchos con proveedores
        $producto1->proveedores()->sync([$proveedor1->id]);

        $producto2 = Producto::firstOrCreate(
            ['codigo_barras' => '75010001002'],
            [
                'nombre' => 'Atún en Agua 140g',
                'precio_venta' => 22.50,
                'stock' => 50,
                'stock_minimo' => 5,
                'sub_categoria_id' => $subCatEnlatados->id,
                'unidad_medida_id' => $unidadMedidaLata ? $unidadMedidaLata->id : 1,
            ]
        );
        // Un producto puede tener varios proveedores usando sync
        $producto2->proveedores()->sync([$proveedor1->id, $proveedor2->id]);
    }
}