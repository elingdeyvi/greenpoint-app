<?php

namespace Database\Seeders;

use App\Models\ConfiguracionEmpresa;
use App\Models\Sucursal;
use Illuminate\Database\Seeder;

class ConfiguracionEmpresaSeeder extends Seeder
{
    public function run(): void
    {
        $primeraSucursal = Sucursal::where('activo', true)->orderBy('id')->first();

        $empresa = ConfiguracionEmpresa::firstOrCreate(
            ['nombre_empresa' => 'Entrada'],
            [
                'nombre_empresa' => 'Entrada',
                'nombre_corto' => 'ENT',
                'nombre_largo' => 'Entrada',
                'rfc' => '',
                'descripcion' => '',
                'telefono' => '',
                'email' => '',
                'direccion' => '',
                'codigo_postal' => '',
                'ciudad' => '',
                'estado' => '',
                'pais' => 'México',
                'sitio_web' => '',
                'color_primario' => '#1976D2',
                'color_secundario' => '#424242',
                'terminos_condiciones' => '',
                'politica_privacidad' => '',
                'activo' => true,
            ]
        );

        if ($primeraSucursal) {
            $empresa->update(['sucursal_id' => $primeraSucursal->id]);
        }
    }
}
