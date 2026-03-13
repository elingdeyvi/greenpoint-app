<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Pobla solo los 3 productos de Macuspana (sistema local Macuspana).
 * Polvo, Rezaga y Balustre con IDs fijos 1, 2, 3 para que coincidan
 * con los del sistema Villahermosa al importar pedidos por QR.
 */
class ProductoMacuspanaSeeder extends Seeder
{
    public function run(): void
    {
        $unidadId = UnidadMedida::where('codigo', 1)->value('id') ?? UnidadMedida::first()?->id ?? null;
        $now = now();

        $compartidos = [
            ['id' => 1, 'nombre' => 'Polvo'],
            ['id' => 2, 'nombre' => 'Rezaga'],
            ['id' => 3, 'nombre' => 'Balustre'],
        ];

        foreach ($compartidos as $p) {
            DB::table('productos')->updateOrInsert(
                ['id' => $p['id']],
                [
                    'nombre' => $p['nombre'],
                    'descripcion' => null,
                    'precio_unitario' => 0,
                    'stock_actual' => 0,
                    'unidad_medida' => 'm3',
                    'unidad_medida_id' => $unidadId,
                    'activo' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
