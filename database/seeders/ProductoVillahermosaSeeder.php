<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Pobla productos para Villahermosa (sistema local Villahermosa).
 * Los 3 compartidos (Polvo, Rezaga, Balustre) con IDs 1, 2, 3 (mismo identificador
 * que en Macuspana para importar pedidos). El resto de productos solo en Villahermosa.
 */
class ProductoVillahermosaSeeder extends Seeder
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

        $soloVillahermosa = [
            'Tortuguero', 'Granzón', 'Revestimiento',
            'Grava de 3/4', 'Grava de 1/2', 'Gravón de 6"', 'Finos', 'Piedra Braza', 'Roca Maya',
        ];

        foreach ($soloVillahermosa as $nombre) {
            Producto::firstOrCreate(
                ['nombre' => $nombre],
                [
                    'precio_unitario' => 0,
                    'stock_actual' => 0,
                    'unidad_medida' => 'm3',
                    'unidad_medida_id' => $unidadId,
                    'activo' => true,
                ]
            );
        }
    }
}
