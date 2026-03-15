<?php

namespace Database\Seeders;

use App\Models\Contacto;
use Illuminate\Database\Seeder;

/**
 * Inserta los 3 contactos de cotizaciones (Tabasco, Veracruz, Cd. del Carmen).
 * Idempotente: updateOrCreate por ubicacion.
 */
class ContactoSeeder extends Seeder
{
    public function run(): void
    {
        $contactos = [
            [
                'ubicacion' => 'Tabasco',
                'direccion' => 'Francisco Sarabia # 126, Col. Gil y Saenz, Tabasco. CP. 86080',
                'telefono' => '(+52) (993) 161 6064',
                'email' => 'villahermosa@greenpoint.mx',
                'mapa_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3794.853128487109!2d-92.93587504926919!3d17.985568889949867!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85edd8294181d1c7%3A0x2dcc0df6165753a1!2sFrancisco%20Sarabia%20126%2C%20Gil%20y%20Saenz%2C%2086080%20Villahermosa%2C%20Tab.!5e0!3m2!1ses-419!2smx!4v1657138972272!5m2!1ses-419!2smx',
                'orden' => 1,
            ],
            [
                'ubicacion' => 'Veracruz',
                'direccion' => 'Sandoval # 174, Fracc. Reforma, Veracruz. CP. 91919',
                'telefono' => '(+52) (229) 932 6060',
                'email' => 'veracruz@greenpoint.mx',
                'mapa_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3768.5267450228316!2d-96.13034614925766!3d19.172182353859956!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85c34136fd546f85%3A0xe7e6d38ced7b4b25!2sGonzalo%20de%20Sandoval%20174%2C%20poligono%201%2C%20Reforma%2C%2091919%20Veracruz%2C%20Ver.!5e0!3m2!1ses-419!2smx!4v1657140738812!5m2!1ses-419!2smx',
                'orden' => 2,
            ],
            [
                'ubicacion' => 'Cd. del Carmen',
                'direccion' => 'Calle 53 # 74, Col. Morelos, Cd. del Carmen, Campeche.',
                'telefono' => '(+52) (999) 122 3651 / (+52) (938) 160 4654',
                'email' => 'cdcarmen@greenpoint.mx',
                'mapa_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3780.1763891315436!2d-91.82917044926279!3d18.656079069822226!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85f10781ff8261c3%3A0xb7d4145b2b9aec07!2sC.%2053%2074%2C%20Morelos%2C%2024115%20Cd%20del%20Carmen%2C%20Camp.!5e0!3m2!1ses-419!2smx!4v1657141404646!5m2!1ses-419!2smx',
                'orden' => 3,
            ],
        ];

        foreach ($contactos as $data) {
            Contacto::updateOrCreate(
                ['ubicacion' => $data['ubicacion']],
                $data
            );
        }
    }
}
