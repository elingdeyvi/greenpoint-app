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
        // Seed inicial del sistema GreenPoint: roles, permisos, catálogos desde cotizaciones y contenido demo.
        $this->call([
            RolesAndPermissionsSeeder::class,
            ServicioSeeder::class,
            ClienteSeeder::class,
            ContactoSeeder::class,
            BannerSeeder::class,
            GaleriaSeeder::class,
            RedSocialSeeder::class,
            ConfiguracionSeeder::class,
            DemoContentSeeder::class,
        ]);
    }
}
