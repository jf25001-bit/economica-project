<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadMedidaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unidades = [
            'Pieza',
            'Kilogramo',
            'Gramo',
            'Litro',
            'Mililitro',
            'Caja',
            'Paquete',
            'Metro',
            'Lata',
            'Bolsa',
            'Libra',
            'Onza',
            'Galón',
        ];

        foreach ($unidades as $unidad) {
            UnidadMedida::firstOrCreate(['nombre' => $unidad]);
        }
    }
}