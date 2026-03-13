<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use Illuminate\Database\Seeder;

class SucursalSeeder extends Seeder
{
    /**
     * Sucursales por defecto para el Sistema de Ventas e Inventario (2 sucursales).
     */
    public function run(): void
    {
        $sucursales = [
            ['nombre' => 'Villahermosa', 'tipo_sucursal' => Sucursal::TIPO_VENTA, 'activo' => true],
            ['nombre' => 'Macuspana', 'tipo_sucursal' => Sucursal::TIPO_VENTA_ALMACEN, 'activo' => true],
        ];

        foreach ($sucursales as $item) {
            Sucursal::updateOrCreate(
                ['nombre' => $item['nombre']],
                ['tipo_sucursal' => $item['tipo_sucursal'], 'activo' => $item['activo']]
            );
        }
    }
}
