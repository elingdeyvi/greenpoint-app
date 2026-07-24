<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Cliente;
use App\Models\Configuracion;
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
use App\Models\RedSocial;
use App\Models\Servicio;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    /**
     * Seed de contenido de ejemplo para el sitio público GreenPoint.
     */
    public function run(): void
    {
        $this->seedConfiguracion();
        $this->seedServicios();
        $this->seedClientes();
        $this->seedGaleria();
        $this->seedBanners();
        $this->seedContactos();
        $this->seedRedesSociales();
        $this->seedPaginaNosotros();
        $this->seedPaginaHistoria();
        $this->seedPaginaTecnologia();
        $this->seedPaginaAviso();
    }

    protected function seedConfiguracion(): void
    {
        $defaults = [
            'sitio_nombre' => 'GreenPoint',
            'empresa_descripcion' => 'Nuestro servicio tiene un profundo conocimiento del mercado interior y exterior de petróleo y gas de México.',
            'telefono_principal' => '(+52) 993 358 1890',
            'email_principal' => 'info@greenpoint.com.mx',
            'direccion_matriz' => 'Villahermosa, Tabasco, México.',
            'whatsapp' => '529933581890',
            'horario_lunes_viernes' => '09:00 AM - 06:00 PM',
            'horario_sabado' => '10:00 AM - 03:00 PM',
            'horario_domingo' => 'Cerrado',
            'home_servicios_titulo' => 'Internet Satelital',
            'home_servicios_subtitulo' => 'Expertos en comunicaciones',
            'home_cta_titulo' => 'Greenpoint: Servicios Satelitales',
            'home_cta_texto' => 'Escríbenos y con gusto te asesoraremos sobre la mejor solución de comunicación satelital para tu operación.',
        ];

        foreach ($defaults as $clave => $valor) {
            Configuracion::query()->updateOrCreate(
                ['clave' => $clave],
                ['valor' => $valor],
            );
        }
    }

    protected function seedServicios(): void
    {
        if (Servicio::query()->exists()) {
            return;
        }

        $servicios = [
            [
                'nombre' => 'Conexión Satelital',
                'descripcion' => 'Conectividad satelital de alta velocidad, segura y confiable para operaciones en tierra y costa afuera.',
                'imagen' => 'images/demo/servicios/gestion-ambiental.jpg',
                'orden' => 1,
                'activo' => true,
            ],
            [
                'nombre' => 'Soluciones',
                'descripcion' => 'Diseño e integración de soluciones de comunicación adaptadas al sector petrolero y energético.',
                'imagen' => 'images/demo/servicios/recoleccion.jpg',
                'orden' => 2,
                'activo' => true,
            ],
            [
                'nombre' => 'Hardware Satelital y Redes WAN',
                'descripcion' => 'Suministro, instalación y soporte de hardware satelital y redes WAN empresariales.',
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
                'titulo' => 'Líder en comunicaciones para el sector Petrolero',
                'imagen' => 'images/demo/banners/banner1.jpg',
                'enlace' => '/nosotros',
                'orden' => 1,
                'activo' => true,
            ],
            [
                'titulo' => 'Comunicaciones Marítimas ROBUSTAS',
                'imagen' => 'images/demo/banners/banner2.jpg',
                'enlace' => '/servicios',
                'orden' => 2,
                'activo' => true,
            ],
            [
                'titulo' => 'Servicios de Conexión SATELITAL',
                'imagen' => 'images/demo/banners/banner3.jpg',
                'enlace' => '/contacto',
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
        $contactos = [
            [
                'ubicacion' => 'Tabasco',
                'direccion' => 'Francisco Sarabia #126, Col. Gil y Saenz, CP 86080, Villahermosa, Tabasco.',
                'telefono' => '(993) 161 6064',
                'email' => 'villahermosa@greenpoint.com.mx',
                'mapa_url' => null,
                'orden' => 1,
            ],
            [
                'ubicacion' => 'Veracruz',
                'direccion' => 'Sandoval #174, Fracc. Reforma, CP 91919, Veracruz.',
                'telefono' => '(229) 000 0000',
                'email' => 'veracruz@greenpoint.com.mx',
                'mapa_url' => null,
                'orden' => 2,
            ],
            [
                'ubicacion' => 'Cd. del Carmen',
                'direccion' => 'Calle 53 #74, Col. Morelos, Ciudad del Carmen, Campeche.',
                'telefono' => '(938) 160 4654',
                'email' => 'cdcarmen@greenpoint.com.mx',
                'mapa_url' => null,
                'orden' => 3,
            ],
        ];

        foreach ($contactos as $contacto) {
            Contacto::query()->updateOrCreate(
                ['ubicacion' => $contacto['ubicacion']],
                $contacto,
            );
        }
    }

    protected function seedRedesSociales(): void
    {
        if (RedSocial::query()->exists()) {
            return;
        }

        $redes = [
            [
                'nombre' => 'Facebook',
                'url' => 'https://www.facebook.com/',
                'icono' => 'fa-brands fa-facebook-f',
                'orden' => 1,
            ],
            [
                'nombre' => 'Twitter',
                'url' => 'https://twitter.com/',
                'icono' => 'fa-brands fa-twitter',
                'orden' => 2,
            ],
            [
                'nombre' => 'YouTube',
                'url' => 'https://www.youtube.com/',
                'icono' => 'fa-brands fa-youtube',
                'orden' => 3,
            ],
            [
                'nombre' => 'LinkedIn',
                'url' => 'https://www.linkedin.com/',
                'icono' => 'fa-brands fa-linkedin-in',
                'orden' => 4,
            ],
        ];

        foreach ($redes as $red) {
            RedSocial::create($red);
        }
    }

    protected function seedPaginaNosotros(): void
    {
        if (PaginaNosotros::query()->exists()) {
            return;
        }

        $pagina = PaginaNosotros::create([
            'titulo' => 'Quiénes Somos',
            'subtitulo' => 'Greenpoint',
            'texto_descriptivo' => 'Greenpoint le ayuda a que toda esta tecnología se ponga a trabajar para usted. Contamos con la red satelital más moderna a nivel mundial. Nuestro Telepuerto HUB Satelital iDirect, interconectado con la red corporativa de Pemex, nos permite entregar micros y segmentos de red de Pemex previamente autorizados y supervisados por ellos.',
            'texto_adicional' => 'Ofrecemos conexión a Internet de alta velocidad, segura y confiable para operaciones en el sector petrolero y energético.',
            'url_video' => null,
            'imagen_destacada' => 'images/demo/nosotros/destacada.jpg',
            'meta_descripcion' => 'Conoce GreenPoint: expertos en comunicaciones satelitales.',
            'meta_keywords' => 'greenpoint, nosotros, satelital, comunicaciones',
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
            ['titulo' => 'Cobertura satelital', 'porcentaje' => 95],
            ['titulo' => 'Experiencia en sector energético', 'porcentaje' => 90],
            ['titulo' => 'Satisfacción de clientes', 'porcentaje' => 92],
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
            ['anio' => 2004, 'titulo' => 'Inicio de operaciones', 'descripcion' => 'GreenPoint inicia operaciones enfocadas en comunicaciones satelitales para el sureste de México.'],
            ['anio' => 2010, 'titulo' => 'Expansión regional', 'descripcion' => 'Apertura de presencia operativa en Veracruz y Cd. del Carmen.'],
            ['anio' => 2015, 'titulo' => 'Telepuerto iDirect', 'descripcion' => 'Consolidación del Telepuerto HUB Satelital iDirect interconectado con redes corporativas.'],
            ['anio' => 2019, 'titulo' => 'Crecimiento continuo', 'descripcion' => 'Ampliación de soluciones de hardware satelital y redes WAN para el sector energético.'],
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
            'contenido' => 'Contamos con el Telepuerto iDirect equipado con la mejor tecnología a nivel mundial para ofrecer comunicaciones robustas y confiables.',
            'imagen_destacada' => 'images/demo/tecnologia/destacada.jpg',
            'meta_descripcion' => 'Tecnología e infraestructura satelital de GreenPoint.',
            'meta_keywords' => 'tecnologia, idirect, satelital',
            'estado' => true,
        ]);

        $secciones = [
            [
                'titulo' => 'Telepuerto iDirect',
                'contenido' => 'Infraestructura satelital de clase mundial para conexiones estables en tierra y costa afuera.',
            ],
            [
                'titulo' => 'Redes WAN',
                'contenido' => 'Integración de hardware y redes WAN para operaciones críticas del sector energético.',
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
            'contenido' => 'GREENPOINT S.A. de C.V., y/o sus empresas asociadas, afiliadas y subsidiarias (“GREENPOINT”), con domicilio en Francisco Sarabia #126, Col. Gil y Saenz, Municipio Centro, Villahermosa, Tabasco, México, es responsable de recabar sus datos personales y de su adecuado tratamiento conforme a la Ley aplicable.',
            'orden' => 1,
        ]);

        $listas = [
            'Autoridades Fiscales: para el cumplimiento de obligaciones fiscales.',
            'Autoridades Gubernamentales, Administrativas y/o Judiciales: para procedimientos legales o auditorías.',
            'Instituciones Bancarias: para cobros o pagos electrónicos aplicables.',
            'Empleados, asesores, afiliadas y proveedores: para la prestación del servicio solicitado.',
            'Terceros con relación comercial: para envío de publicidad autorizada.',
        ];

        foreach ($listas as $index => $texto) {
            PaginaAvisoLista::create([
                'pagina_aviso_seccion_id' => $seccionGeneral->id,
                'texto' => $texto,
                'orden' => $index + 1,
            ]);
        }

        PaginaAvisoSeccion::create([
            'pagina_aviso_id' => $pagina->id,
            'titulo' => 'Protección de Datos Personales',
            'contenido' => 'En cumplimiento con la Ley Federal de Protección de Datos Personales en Posesión de los Particulares, GREENPOINT utiliza los datos para contacto, seguimiento de solicitudes, relación comercial y comunicaciones sobre servicios.',
            'orden' => 2,
        ]);
    }
}
