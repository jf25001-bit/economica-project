<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RolSeeder;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            UserSeeder::class,
        ]);
    }
}