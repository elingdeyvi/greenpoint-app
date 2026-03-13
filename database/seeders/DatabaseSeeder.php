<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed inicial del sistema GreenPoint: roles, permisos, usuario administrador y contenido demo.
        $this->call([
            RolesAndPermissionsSeeder::class,
            DemoContentSeeder::class,
        ]);
    }
}
