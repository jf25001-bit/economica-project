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
        // Crear rol Administrador
        $adminRol = Rol::updateOrCreate(
            ['nombre' => 'Administrador'],
            [
                'descripcion' => 'Usuario con acceso total al sistema'
            ]
        );

        // Crear rol Cajero
        $cajeroRol = Rol::updateOrCreate(
            ['nombre' => 'Cajero'],
            [
                'descripcion' => 'Usuario encargado de ventas y caja'
            ]
        );

        // Crear usuario administrador
        User::updateOrCreate(
            ['name' => 'Carlos'],
            [
                'apellido' => 'Mendoza',
                'email'    => 'carlos.mendoza@admin.com',
                'telefono' => '78901234',
                'password' => Hash::make('clave1234'),
                'rol_id'   => $adminRol->id,
                'activo'   => true
            ]
        );

        // Crear usuario cajero
        User::updateOrCreate(
            ['name' => 'Maria'],
            [
                'apellido' => 'Lopez',
                'email'    => 'maria.lopez@caja.com',
                'telefono' => '71234567',
                'password' => Hash::make('clave1234'),
                'rol_id'   => $cajeroRol->id,
                'activo'   => true
            ]
        );
    }
}