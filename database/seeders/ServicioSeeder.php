<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

/**
 * Inserta los servicios tal como aparecen en cotizaciones (index.html, sección OUR SERVICES).
 * Idempotente: usa updateOrCreate por nombre.
 */
class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = [
            [
                'nombre' => 'Internet Satelital',
                'descripcion' => 'Con la técnologia satelital de Greenpoint, usted puede contar con un servicio completo y en cualquier ubicación.',
                'imagen' => 'icons/icon-04.png',
                'orden' => 1,
            ],
            [
                'nombre' => 'Nube Satelital Dedicada',
                'descripcion' => 'Conexión permanente a internet de alta velocidad simétrica de subida y bajada, brindada por nuestra red.',
                'imagen' => 'icons/icon-02.png',
                'orden' => 2,
            ],
            [
                'nombre' => 'Red Privada IP',
                'descripcion' => 'La Telefonía IP que permite disfrutar de todas las características de nuestro sistema que está diseñado de manera inteligente.',
                'imagen' => 'icons/icon-03.png',
                'orden' => 3,
            ],
            [
                'nombre' => 'Internet fijo y movil',
                'descripcion' => 'Instalación en pozos y plataformas. Servicios satelitales dedicados. SCPC punto a punto Satelital. Enlaces microondas, etc.',
                'imagen' => 'icons/icon-01.png',
                'orden' => 4,
            ],
        ];

        foreach ($servicios as $data) {
            Servicio::updateOrCreate(
                ['nombre' => $data['nombre']],
                [
                    'descripcion' => $data['descripcion'],
                    'imagen' => $data['imagen'],
                    'orden' => $data['orden'],
                    'activo' => true,
                ]
            );
        }
    }
}
