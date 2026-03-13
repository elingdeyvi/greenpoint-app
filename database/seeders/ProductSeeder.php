<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

/**
 * Pobla la tabla productos con el catálogo base de materiales.
 * Usa updateOrCreate por nombre para evitar duplicados (campo nombre en BD).
 */
class ProductSeeder extends Seeder
{
    /**
     * Nombres de productos a crear o actualizar (campo nombre en la tabla productos).
     */
    private const PRODUCT_NAMES = [
        'Polvo',
        'Rezaga',
        'Balastre',
        'Tortuguero',
        'Granzón',
        'Revestimiento',
        'Grava de 3/4',
        'Grava de 1/2',
        'Gravón de 6"',
        'Finos',
        'Piedra Braza',
        'Roca Maya',
    ];

    public function run(): void
    {
        $unidad_medida_id = UnidadMedida::first()?->id;

        foreach (self::PRODUCT_NAMES as $product_name) {
            Producto::updateOrCreate(
                ['nombre' => $product_name],
                [
                    'descripcion' => null,
                    'precio_unitario' => 0,
                    'stock_actual' => 0,
                    'unidad_medida' => 'm3',
                    'unidad_medida_id' => $unidad_medida_id,
                    'activo' => true,
                ]
            );
        }
    }
}
