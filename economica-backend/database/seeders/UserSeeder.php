<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRol = Rol::updateOrCreate(
            ['nombre' => 'Administrador'],
            ['descripcion' => 'Usuario con acceso total al sistema']
        );

        $cajeroRol = Rol::updateOrCreate(
            ['nombre' => 'Cajero'],
            ['descripcion' => 'Usuario encargado de ventas y caja']
        );

        User::updateOrCreate(
            ['email' => 'pedro.admin@sistema.com'],
            [
                'name' => 'Pedro',
                'apellido' => 'Gómez',
                'telefono' => '70000001',
                'password' => Hash::make('clave1234'),
                'rol_id' => $adminRol->id,
                'activo' => true
            ]
        );

        User::updateOrCreate(
            ['email' => 'maria.cajero@sistema.com'],
            [
                'name' => 'María',
                'apellido' => 'López',
                'telefono' => '70000002',
                'password' => Hash::make('clave1234'),
                'rol_id' => $cajeroRol->id,
                'activo' => true
            ]
        );
    }
}