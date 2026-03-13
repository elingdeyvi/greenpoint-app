<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Cliente;
use App\Models\Contacto;
use App\Models\Galeria;
use App\Models\PaginaAviso;
use App\Models\PaginaAvisoLista;
use App\Models\PaginaAvisoSeccion;
use App\Models\PaginaHistoria;
use App\Models\PaginaHistoriaEvento;
use App\Models\PaginaHistoriaImagen;
use App\Models\PaginaNosotros;
use App\Models\PaginaNosotrosImagen;
use App\Models\PaginaNosotrosProgreso;
use App\Models\PaginaTecnologia;
use App\Models\PaginaTecnologiaSeccion;
use App\Models\Servicio;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    /**
     * Seed de contenido de ejemplo para el sitio público GreenPoint.
     * Comentario: Usa rutas de imagen placeholder; se pueden reemplazar por assets reales al desplegar.
     */
    public function run(): void
    {
        $this->seedServicios();
        $this->seedClientes();
        $this->seedGaleria();
        $this->seedBanners();
        $this->seedContactos();
        $this->seedPaginaNosotros();
        $this->seedPaginaHistoria();
        $this->seedPaginaTecnologia();
        $this->seedPaginaAviso();
    }

    protected function seedServicios(): void
    {
        if (Servicio::query()->exists()) {
            return;
        }

        $servicios = [
            [
                'nombre' => 'Recolección de residuos',
                'descripcion' => 'Servicio integral de recolección y manejo de residuos industriales y urbanos.',
                'imagen' => 'images/demo/servicios/recoleccion.jpg',
                'orden' => 1,
                'activo' => true,
            ],
            [
                'nombre' => 'Gestión ambiental',
                'descripcion' => 'Asesoría en cumplimiento normativo y planes de gestión ambiental.',
                'imagen' => 'images/demo/servicios/gestion-ambiental.jpg',
                'orden' => 2,
                'activo' => true,
            ],
            [
                'nombre' => 'Transporte especializado',
                'descripcion' => 'Transporte seguro de materiales y residuos con flota especializada.',
                'imagen' => 'images/demo/servicios/transporte.jpg',
                'orden' => 3,
                'activo' => true,
            ],
        ];

        foreach ($servicios as $servicio) {
            Servicio::create($servicio);
        }
    }

    protected function seedClientes(): void
    {
        if (Cliente::query()->exists()) {
            return;
        }

        for ($i = 1; $i <= 16; $i++) {
            Cliente::create([
                'nombre' => "Cliente {$i}",
                'logo' => "images/demo/clientes/cliente{$i}.png",
                'enlace' => null,
                'orden' => $i,
                'activo' => true,
            ]);
        }
    }

    protected function seedGaleria(): void
    {
        if (Galeria::query()->exists()) {
            return;
        }

        for ($i = 1; $i <= 8; $i++) {
            Galeria::create([
                'titulo' => "Proyecto {$i}",
                'descripcion' => 'Registro fotográfico de proyectos y operaciones en campo.',
                'imagen' => "images/demo/galeria/galeria{$i}.jpg",
                'orden' => $i,
                'activo' => true,
            ]);
        }
    }

    protected function seedBanners(): void
    {
        if (Banner::query()->exists()) {
            return;
        }

        $banners = [
            [
                'titulo' => 'Compromiso con el medio ambiente',
                'imagen' => 'images/demo/banners/banner1.jpg',
                'enlace' => '/nosotros',
                'orden' => 1,
                'activo' => true,
            ],
            [
                'titulo' => 'Soluciones integrales en residuos',
                'imagen' => 'images/demo/banners/banner2.jpg',
                'enlace' => '/servicios',
                'orden' => 2,
                'activo' => true,
            ],
            [
                'titulo' => 'Confianza de nuestros clientes',
                'imagen' => 'images/demo/banners/banner3.jpg',
                'enlace' => '/clientes',
                'orden' => 3,
                'activo' => true,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }

    protected function seedContactos(): void
    {
        if (Contacto::query()->exists()) {
            return;
        }

        $contactos = [
            [
                'ubicacion' => 'Tabasco',
                'direccion' => 'Parque Industrial, Villahermosa, Tabasco.',
                'telefono' => '993 000 0000',
                'email' => 'tabasco@greenpoint.com',
                'mapa_url' => null,
                'orden' => 1,
            ],
            [
                'ubicacion' => 'Veracruz',
                'direccion' => 'Zona Industrial, Coatzacoalcos, Veracruz.',
                'telefono' => '921 000 0000',
                'email' => 'veracruz@greenpoint.com',
                'mapa_url' => null,
                'orden' => 2,
            ],
            [
                'ubicacion' => 'Carmen',
                'direccion' => 'Área Industrial, Ciudad del Carmen, Campeche.',
                'telefono' => '938 000 0000',
                'email' => 'carmen@greenpoint.com',
                'mapa_url' => null,
                'orden' => 3,
            ],
        ];

        foreach ($contactos as $contacto) {
            Contacto::create($contacto);
        }
    }

    protected function seedPaginaNosotros(): void
    {
        if (PaginaNosotros::query()->exists()) {
            return;
        }

        $pagina = PaginaNosotros::create([
            'titulo' => 'Quiénes somos',
            'subtitulo' => 'GreenPoint, soluciones ambientales integrales',
            'texto_descriptivo' => 'Somos una empresa especializada en la gestión integral de residuos y soluciones ambientales para la industria.',
            'texto_adicional' => 'Trabajamos con estándares de calidad y seguridad, alineados a la normatividad vigente.',
            'url_video' => null,
            'imagen_destacada' => 'images/demo/nosotros/destacada.jpg',
            'meta_descripcion' => 'Conoce la historia, misión y valores de GreenPoint.',
            'meta_keywords' => 'greenpoint, nosotros, empresa, gestión de residuos',
            'estado' => true,
        ]);

        $imagenes = [
            'images/demo/nosotros/galeria1.jpg',
            'images/demo/nosotros/galeria2.jpg',
            'images/demo/nosotros/galeria3.jpg',
        ];

        foreach ($imagenes as $index => $ruta) {
            PaginaNosotrosImagen::create([
                'pagina_nosotros_id' => $pagina->id,
                'ruta_imagen' => $ruta,
                'orden' => $index + 1,
            ]);
        }

        $barras = [
            ['titulo' => 'Compromiso ambiental', 'porcentaje' => 95],
            ['titulo' => 'Cobertura en el sureste', 'porcentaje' => 85],
            ['titulo' => 'Satisfacción de clientes', 'porcentaje' => 90],
        ];

        foreach ($barras as $index => $barra) {
            PaginaNosotrosProgreso::create([
                'pagina_nosotros_id' => $pagina->id,
                'titulo' => $barra['titulo'],
                'porcentaje' => $barra['porcentaje'],
                'descripcion' => null,
                'orden' => $index + 1,
            ]);
        }
    }

    protected function seedPaginaHistoria(): void
    {
        if (PaginaHistoria::query()->exists()) {
            return;
        }

        $pagina = PaginaHistoria::create([
            'titulo' => 'Nuestra Historia',
            'meta_descripcion' => 'Línea de tiempo de la evolución de GreenPoint.',
            'meta_keywords' => 'historia, greenpoint, trayectoria',
            'estado' => true,
        ]);

        $eventos = [
            ['anio' => 2010, 'titulo' => 'Fundación', 'descripcion' => 'Iniciamos operaciones en Tabasco.'],
            ['anio' => 2015, 'titulo' => 'Expansión', 'descripcion' => 'Apertura de operaciones en Veracruz.'],
            ['anio' => 2020, 'titulo' => 'Consolidación', 'descripcion' => 'Integración de nuevos servicios ambientales.'],
        ];

        foreach ($eventos as $index => $evento) {
            PaginaHistoriaEvento::create([
                'pagina_historia_id' => $pagina->id,
                'anio' => $evento['anio'],
                'titulo' => $evento['titulo'],
                'descripcion' => $evento['descripcion'],
                'orden' => $index + 1,
            ]);
        }

        $imagenes = [
            'images/demo/historia/historia1.jpg',
            'images/demo/historia/historia2.jpg',
        ];

        foreach ($imagenes as $index => $ruta) {
            PaginaHistoriaImagen::create([
                'pagina_historia_id' => $pagina->id,
                'ruta_imagen' => $ruta,
                'orden' => $index + 1,
            ]);
        }
    }

    protected function seedPaginaTecnologia(): void
    {
        if (PaginaTecnologia::query()->exists()) {
            return;
        }

        $pagina = PaginaTecnologia::create([
            'titulo' => 'Tecnología',
            'contenido' => 'En GreenPoint utilizamos tecnología de vanguardia para la gestión y trazabilidad de residuos.',
            'imagen_destacada' => 'images/demo/tecnologia/destacada.jpg',
            'meta_descripcion' => 'Tecnología y procesos utilizados por GreenPoint.',
            'meta_keywords' => 'tecnologia, monitoreo, trazabilidad',
            'estado' => true,
        ]);

        $secciones = [
            [
                'titulo' => 'Monitoreo en tiempo real',
                'contenido' => 'Sistemas de monitoreo que permiten seguimiento en tiempo real de las operaciones.',
            ],
            [
                'titulo' => 'Optimización de rutas',
                'contenido' => 'Herramientas de planificación para reducir tiempos y emisiones.',
            ],
        ];

        foreach ($secciones as $index => $seccion) {
            PaginaTecnologiaSeccion::create([
                'pagina_tecnologia_id' => $pagina->id,
                'titulo' => $seccion['titulo'],
                'contenido' => $seccion['contenido'],
                'orden' => $index + 1,
            ]);
        }
    }

    protected function seedPaginaAviso(): void
    {
        if (PaginaAviso::query()->exists()) {
            return;
        }

        $pagina = PaginaAviso::create([
            'titulo' => 'Aviso de Privacidad',
            'meta_descripcion' => 'Aviso de privacidad de GreenPoint.',
            'meta_keywords' => 'aviso de privacidad, datos personales',
            'estado' => true,
        ]);

        $seccionGeneral = PaginaAvisoSeccion::create([
            'pagina_aviso_id' => $pagina->id,
            'titulo' => 'Tratamiento de datos personales',
            'contenido' => 'GreenPoint se compromete a proteger la información personal de sus clientes y usuarios.',
            'orden' => 1,
        ]);

        $listas = [
            'Finalidades del tratamiento de datos.',
            'Medios para ejercer los derechos ARCO.',
            'Medidas de seguridad implementadas.',
        ];

        foreach ($listas as $index => $texto) {
            PaginaAvisoLista::create([
                'pagina_aviso_seccion_id' => $seccionGeneral->id,
                'texto' => $texto,
                'orden' => $index + 1,
            ]);
        }
    }
}

