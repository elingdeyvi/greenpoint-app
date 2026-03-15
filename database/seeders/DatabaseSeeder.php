<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Orden: roles/permisos primero; luego catálogos (Configuracion no depende de imágenes;
     * Servicio/Cliente/Banner/Galeria guardan rutas que requieren imágenes en storage).
     * Para tener imágenes reales, ejecutar antes: php artisan app:import-cotizaciones-images
     * (origen: COTIZACIONES_PATH en .env o ../cotizaciones). Las rutas en los seeders
     * pueden ser placeholders hasta ejecutar ese comando.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            // Catálogos desde cotizaciones (orden: sin dependencia de imágenes → con rutas de imágenes)
            ConfiguracionSeeder::class,
            RedSocialSeeder::class,
            ContactoSeeder::class,
            ServicioSeeder::class,
            ClienteSeeder::class,
            BannerSeeder::class,
            GaleriaSeeder::class,
            DemoContentSeeder::class,
        ]);
    }
}
