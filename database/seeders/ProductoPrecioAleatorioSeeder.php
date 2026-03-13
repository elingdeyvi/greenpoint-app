<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

/**
 * Asigna un precio_unitario aleatorio a todos los productos existentes.
 * Útil para desarrollo/pruebas. Ejecutar después de los seeders de productos:
 *   php artisan db:seed --class=ProductoPrecioAleatorioSeeder
 */
class ProductoPrecioAleatorioSeeder extends Seeder
{
    /** Precio mínimo por unidad (ej. m³) en pesos. */
    private const PRECIO_MIN = 150;

    /** Precio máximo por unidad en pesos. */
    private const PRECIO_MAX = 2500;

    /** Número de decimales para el precio. */
    private const DECIMALES = 2;

    public function run(): void
    {
        $productos = Producto::all();

        $factor = 10 ** self::DECIMALES;
        $min = (int) round(self::PRECIO_MIN * $factor);
        $max = (int) round(self::PRECIO_MAX * $factor);

        foreach ($productos as $producto) {
            $precio = random_int($min, $max) / $factor;
            $producto->update(['precio_unitario' => $precio]);
        }

        $this->command?->info('Precios aleatorios asignados a ' . $productos->count() . ' producto(s).');
    }
}
