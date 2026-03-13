<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadMedidaSeeder extends Seeder
{
    public function run(): void
    {
        UnidadMedida::firstOrCreate(
            ['codigo' => 1],
            ['nombre' => 'm3', 'activo' => true]
        );
    }
}
