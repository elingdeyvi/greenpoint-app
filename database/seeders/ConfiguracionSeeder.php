<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;

/**
 * Inserta claves de configuración del footer y sitio de cotizaciones.
 * Idempotente: updateOrCreate por clave.
 */
class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['clave' => 'telefono_general', 'valor' => '(+52) 993 358 1890'],
            ['clave' => 'email_general', 'valor' => 'info@greenpoint.mx'],
            ['clave' => 'direccion_matriz', 'valor' => 'Villahermosa, Tabasco, México.'],
            ['clave' => 'whatsapp_url', 'valor' => 'https://api.whatsapp.com/send?phone=529933581890'],
            ['clave' => 'meta_keywords_default', 'valor' => 'Proveedor de internet satelital, broadband, internet services mexico'],
            ['clave' => 'meta_description_default', 'valor' => 'GreenPoint - Proveedor de internet satelital, broadband, internet services mexico'],
            ['clave' => 'footer_texto_empresa', 'valor' => 'Nuestro servicio tiene un profundo conocimiento del mercado interior y exterior de petróleo y gas de México.'],
        ];

        foreach ($items as $data) {
            Configuracion::updateOrCreate(
                ['clave' => $data['clave']],
                ['valor' => $data['valor']]
            );
        }
    }
}
