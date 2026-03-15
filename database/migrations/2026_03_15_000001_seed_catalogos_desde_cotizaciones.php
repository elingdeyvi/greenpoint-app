<?php

use Database\Seeders\BannerSeeder;
use Database\Seeders\ClienteSeeder;
use Database\Seeders\ConfiguracionSeeder;
use Database\Seeders\ContactoSeeder;
use Database\Seeders\GaleriaSeeder;
use Database\Seeders\RedSocialSeeder;
use Database\Seeders\ServicioSeeder;
use Illuminate\Database\Migrations\Migration;

/**
 * Inserta datos iniciales de catálogos desde cotizaciones (mismo contenido que los seeders 1.1–7.1).
 * Útil en entornos donde no se ejecutan seeders; ejecutar después de las migraciones que crean
 * las tablas servicios, clientes, contactos, banners, galeria, redes_sociales, configuracion.
 *
 * Las rutas de imágenes (banners/, clientes/, galeria/, icons/) asumen que las imágenes se copiarán
 * después con: php artisan app:import-cotizaciones-images
 */
return new class extends Migration
{
    public function up(): void
    {
        $seeders = [
            ConfiguracionSeeder::class,
            RedSocialSeeder::class,
            ContactoSeeder::class,
            ServicioSeeder::class,
            ClienteSeeder::class,
            BannerSeeder::class,
            GaleriaSeeder::class,
        ];

        foreach ($seeders as $seederClass) {
            (new $seederClass())->run();
        }
    }

    public function down(): void
    {
        // No se revierten datos para evitar borrar contenido creado por el usuario.
    }
};
