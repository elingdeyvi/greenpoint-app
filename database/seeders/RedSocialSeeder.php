<?php

namespace Database\Seeders;

use App\Models\RedSocial;
use Illuminate\Database\Seeder;

/**
 * Inserta redes sociales desde cotizaciones (footer/header): WhatsApp y opcionales.
 * Idempotente: updateOrCreate por nombre.
 */
class RedSocialSeeder extends Seeder
{
    public function run(): void
    {
        $redes = [
            [
                'nombre' => 'WhatsApp',
                'url' => 'https://api.whatsapp.com/send?phone=529933581890',
                'icono' => 'fab fa-whatsapp',
                'orden' => 1,
            ],
        ];

        foreach ($redes as $data) {
            RedSocial::updateOrCreate(
                ['nombre' => $data['nombre']],
                $data
            );
        }
    }
}
