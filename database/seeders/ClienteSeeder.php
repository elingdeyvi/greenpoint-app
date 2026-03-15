<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

/**
 * Inserta 16 clientes según cotizaciones/clientes.html (logos c1.jpg … c16.jpg).
 * Idempotente: firstOrCreate por orden.
 */
class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 16; $i++) {
            Cliente::firstOrCreate(
                ['orden' => $i],
                [
                    'nombre' => "Cliente {$i}",
                    'logo' => "clientes/c{$i}.jpg",
                    'enlace' => null,
                    'activo' => true,
                ]
            );
        }
    }
}
