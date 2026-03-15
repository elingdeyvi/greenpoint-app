<?php

namespace Database\Seeders;

use App\Models\Galeria;
use Illuminate\Database\Seeder;

/**
 * Inserta los 8 ítems de cotizaciones/galeria.html (data-sub-html y clases).
 * Idempotente: updateOrCreate por titulo.
 */
class GaleriaSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['titulo' => 'Fast Internet #01', 'descripcion' => 'Streaming', 'imagen' => 'galeria/g1.jpg', 'orden' => 1],
            ['titulo' => 'Internet Speed #02', 'descripcion' => 'Online TV', 'imagen' => 'galeria/g2.jpg', 'orden' => 2],
            ['titulo' => 'Best TV Programs #03', 'descripcion' => 'Broadband', 'imagen' => 'galeria/g3.jpg', 'orden' => 3],
            ['titulo' => 'Movies to Watch #04', 'descripcion' => 'Streaming', 'imagen' => 'galeria/g4.jpg', 'orden' => 4],
            ['titulo' => 'Fast Internet #05', 'descripcion' => 'Online Gaming', 'imagen' => 'galeria/g5.jpg', 'orden' => 5],
            ['titulo' => 'Provide Wi-Fi #06', 'descripcion' => 'Broadband', 'imagen' => 'galeria/g6.jpg', 'orden' => 6],
            ['titulo' => 'Mobile Internet #07', 'descripcion' => 'Streaming', 'imagen' => 'galeria/g7.jpg', 'orden' => 7],
            ['titulo' => 'Provide Wi-Fi #08', 'descripcion' => 'Online Gaming', 'imagen' => 'galeria/g8.jpg', 'orden' => 8],
        ];

        foreach ($items as $data) {
            Galeria::updateOrCreate(
                ['titulo' => $data['titulo']],
                array_merge($data, ['activo' => true])
            );
        }
    }
}
