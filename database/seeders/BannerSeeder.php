<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

/**
 * Inserta los 3 banners del carrusel de cotizaciones/index.html.
 * Tabla banners: titulo, imagen, enlace (sin descripcion). Idempotente por titulo.
 */
class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'titulo' => 'Lider en comunicaciones para el sector Petrolero',
                'imagen' => 'banners/banner-01.jpg',
                'enlace' => '/nosotros',
                'orden' => 1,
                'activo' => true,
            ],
            [
                'titulo' => 'Comunicaciones Maritimas ROBUSTAS',
                'imagen' => 'banners/banner-02.jpg',
                'enlace' => '/nosotros',
                'orden' => 2,
                'activo' => true,
            ],
            [
                'titulo' => 'Servicios de Conexion SATELITAL',
                'imagen' => 'banners/banner-03.jpg',
                'enlace' => '/nosotros',
                'orden' => 3,
                'activo' => true,
            ],
        ];

        foreach ($banners as $data) {
            Banner::updateOrCreate(
                ['titulo' => $data['titulo']],
                $data
            );
        }
    }
}
