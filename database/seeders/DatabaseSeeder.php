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
        $this->call([
            UnidadMedidaSeeder::class,
            ClienteSeeder::class,
            SucursalSeeder::class,
            ConfiguracionEmpresaSeeder::class,
            ProductSeeder::class,
        ]);
        // Productos: ejecutar solo el seeder de la sucursal que corresponda:
        // php artisan db:seed --class=ProductoMacuspanaSeeder   (sistema Macuspana)
        // php artisan db:seed --class=ProductoVillahermosaSeeder (sistema Villahermosa)
    }
}
