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
        $admin = Rol::where('nombre', 'Administrador')->first();
        $cajero = Rol::where('nombre', 'Cajero')->first();

        User::updateOrCreate(
            ['name' => 'administrador'],
            [
                'password' => Hash::make('clave1234'),
                'rol_id' => $admin?->id,
                'activo' => true
            ]
        );

        User::updateOrCreate(
            ['name' => 'cajero'],
            [
                'password' => Hash::make('clave1234'),
                'rol_id' => $cajero?->id,
                'activo' => true
            ]
        );
    }
}