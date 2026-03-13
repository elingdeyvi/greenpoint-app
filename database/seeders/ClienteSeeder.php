<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        Cliente::firstOrCreate(
            ['es_mostrador' => true],
            ['nombre' => 'Cliente mostrador', 'activo' => true]
        );
    }
}
