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
            'home_cta_titulo' => 'Greenpoint, internet satelital',
            'home_cta_texto' => 'Greenpoint cuenta hoy en día con la tecnología para mantenerlo siempre conectado, ya sea a internet o incluso a su red corporativa, y con esto aprovechar las ventajas que tienes al estarlo. Poder realizar llamadas telefónicas, videoconferencias, transferencias de cualquier tipo de datos, acceso a internet y lo más importante, desde cualquier sitio, sin importar si estás en pozo petrolero de Tabasco o en una mina en Chihuahua, o bien en un barco en el Golfo de México.',
            'anos_experiencia' => '18',
            'home_video_url' => 'https://www.youtube.com/watch?v=yd1JhZzoS6A',
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
        $servicios = [
            [
                'nombre' => 'Conexion Satelital',
                'subtitulo' => null,
                'titulo_seccion' => 'Greenpoint cuenta con el telepuerto iDirect',
                'descripcion' => "Equipado con la mejor tecnología a nivel mundial. Permite entregar a sus clientes una conexión robusta, compartida o servicios dedicados con administración del QoS, para asignar prioridades a las aplicaciones críticas como son voz, video o información en tiempo real.\n\n[features]\nAnchos de banda desde 64 Kbps hasta 100 Mbps\nServicio dedicado o en tasa compartida\nCIR (committed information rate)\nIP Públicas Homologadas\nConexión al backbone de internet o WAN empresarial\nQoS ajustable a las necesidades de cada cliente\n[/features]\n\n[badges]\nVSAT FIJA MANUAL\nVSAT SEMI-FIJA AUTOMATICA\nVSAT AUTOESTABILIZADA\nVSAT BAJO PERFIL EN MOVIMIENTO\nSERVICIOS SATELITALES IP\nSERVICIOS DE ACCESO A INTERNET\nRED PRIVADA SATELITAL\nTRANSPORTE DE VOZ, DATOS Y VIDEO\nCELLULAR BACKHAUL\nDISTRIBUCION DE CONTENIDO\n[/badges]",
                'imagen' => 'images/demo/servicios/tec2.jpg',
                'imagen_secundaria' => 'images/demo/servicios/tec3.jpg',
                'plantilla' => 'detalle',
                'orden' => 1,
                'activo' => true,
            ],
            [
                'nombre' => 'Soluciones',
                'subtitulo' => 'Soluciones para el sector petrolero y maritimo.',
                'titulo_seccion' => 'Soluciones a medida',
                'descripcion' => "[features]\nInstalación en pozos y plataformas\nServicios satelitales dedicados\nBackhaul para redes de hardware\nSCPC Punto a Punto Satelital\nEnlaces microondas PtP, PtM y Mesh\nRenta y Venta de equipos de hadware\nConsultoría y Asesoria en networking\nProyectos garantizados con llave en mano\nServicios de Telefonía IP, IP-PBX\nIntegración de redes locales\nSoluciones de Videoconferencias IP\n[/features]",
                'imagen' => 'images/demo/servicios/s1.jpg',
                'imagen_secundaria' => 'images/demo/servicios/s2.jpg',
                'plantilla' => 'about',
                'orden' => 2,
                'activo' => true,
            ],
            [
                'nombre' => 'Hardware Satelital y Redes Wan',
                'subtitulo' => null,
                'titulo_seccion' => 'Hardware y Equipos',
                'descripcion' => "Greenpoint brinda el asesoramiento, suministro, instalaciones, mantenimiento, garantías y todo lo relacionado al hardware utilizado en los servicios que se prestan, por lo que nuestros clientes no tienen que dedicar tiempo para estos asuntos.\n\n[kit]: : : : Kit satelital: Modem, buc, Inb, canister y base : : : :[/kit]\n\n[features]\nEquipamiento para redes alámbricas e inalámbricas\nMantenimiento de equipos satelitales\nSuministro de refacciones y herramientas especiales\nTelepuerto iDirect para redes empresariales\nDiseño y arquitectura de redes satelitales tipo malla y/o estrella\n[/features]",
                'imagen' => 'images/demo/servicios/s3.jpg',
                'imagen_secundaria' => null,
                'plantilla' => 'split',
                'orden' => 3,
                'activo' => true,
            ],
        ];

        foreach ($servicios as $servicio) {
            Servicio::query()->updateOrCreate(
                ['orden' => $servicio['orden']],
                $servicio,
            );
        }

        /* Desactivar catálogo anterior (4.º ítem y nombres viejos fuera de prod) */
        Servicio::query()->where('orden', '>=', 4)->update(['activo' => false]);
    }

    protected function seedClientes(): void
    {
        for ($i = 1; $i <= 16; $i++) {
            Cliente::query()->updateOrCreate(
                ['orden' => $i],
                [
                    'nombre' => "Cliente {$i}",
                    'logo' => "images/demo/clientes/c{$i}.jpg",
                    'enlace' => null,
                    'activo' => true,
                ],
            );
        }

        Cliente::query()->where('orden', '>', 16)->update(['activo' => false]);
    }

    protected function seedGaleria(): void
    {
        // cgi-bin galeria.html — galeria/g1.jpg … g8.jpg
        $items = [
            ['titulo' => 'Fast Internet #01', 'descripcion' => 'Streaming', 'imagen' => 'images/demo/galeria/g1.jpg', 'orden' => 1],
            ['titulo' => 'Internet Speed #02', 'descripcion' => 'Online TV', 'imagen' => 'images/demo/galeria/g2.jpg', 'orden' => 2],
            ['titulo' => 'Best TV Programs #03', 'descripcion' => 'Broadband', 'imagen' => 'images/demo/galeria/g3.jpg', 'orden' => 3],
            ['titulo' => 'Movies to Watch #04', 'descripcion' => 'Streaming', 'imagen' => 'images/demo/galeria/g4.jpg', 'orden' => 4],
            ['titulo' => 'Fast Internet #05', 'descripcion' => 'Online Gaming', 'imagen' => 'images/demo/galeria/g5.jpg', 'orden' => 5],
            ['titulo' => 'Provide Wi-Fi #06', 'descripcion' => 'Broadband', 'imagen' => 'images/demo/galeria/g6.jpg', 'orden' => 6],
            ['titulo' => 'Mobile Internet #07', 'descripcion' => 'Streaming', 'imagen' => 'images/demo/galeria/g7.jpg', 'orden' => 7],
            ['titulo' => 'Provide Wi-Fi #08', 'descripcion' => 'Online Gaming', 'imagen' => 'images/demo/galeria/g8.jpg', 'orden' => 8],
        ];

        foreach ($items as $item) {
            Galeria::query()->updateOrCreate(
                ['orden' => $item['orden']],
                [
                    'titulo' => $item['titulo'],
                    'descripcion' => $item['descripcion'],
                    'imagen' => $item['imagen'],
                    'activo' => true,
                ],
            );
        }

        Galeria::query()->where('orden', '>', 8)->update(['activo' => false]);
    }

    protected function seedBanners(): void
    {
        $banners = [
            [
                'titulo' => 'Líder en comunicaciones|para el sector Petrolero',
                'descripcion' => 'Ofrecemos la conexión a Internet de alta velocidad, segura y confiable que lo ayuda a ofrecer un servicio constante.',
                'imagen' => 'images/demo/banners/banner1.jpg',
                'enlace' => '/nosotros',
                'orden' => 1,
                'activo' => true,
            ],
            [
                'titulo' => 'Comunicaciones|Marítimas ROBUSTAS',
                'descripcion' => 'Contamos con el Telepuerto iDirect equipado con la mejor tecnología a nivel mundial.',
                'imagen' => 'images/demo/banners/banner2.jpg',
                'enlace' => '/servicios',
                'orden' => 2,
                'activo' => true,
            ],
            [
                'titulo' => 'Servicios de|Conexion SATELITAL',
                'descripcion' => 'Nuestro servicio tiene un profundo conocimiento del mercado interior y exterior de petróleo y gas de México.',
                'imagen' => 'images/demo/banners/banner3.jpg',
                'enlace' => '/contacto',
                'orden' => 3,
                'activo' => true,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::query()->updateOrCreate(
                ['orden' => $banner['orden']],
                $banner,
            );
        }
    }

    protected function seedContactos(): void
    {
        $contactos = [
            [
                'ubicacion' => 'Tabasco',
                'subtitulo' => 'Villahermosa',
                'direccion' => 'Francisco Sarabia # 126, Col. Gil y Saenz, Tabasco. CP. 86080',
                'telefono' => '(+52) (993) 161 6064',
                'email' => 'villahermosa@greenpoint.mx',
                'mapa_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3794.853128487109!2d-92.93587504926919!3d17.985568889949867!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85edd8294181d1c7%3A0x2dcc0df6165753a1!2sFrancisco%20Sarabia%20126%2C%20Gil%20y%20Saenz%2C%2086080%20Villahermosa%2C%20Tab.!5e0!3m2!1ses-419!2smx!4v1657138972272!5m2!1ses-419!2smx',
                'orden' => 1,
            ],
            [
                'ubicacion' => 'Veracruz',
                'subtitulo' => 'Veracruz',
                'direccion' => 'Sandoval # 174, Fracc. Reforma, Veracruz. CP. 91919',
                'telefono' => '(+52) (229) 932 6060',
                'email' => 'veracruz@greenpoint.mx',
                'mapa_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3768.5267450228316!2d-96.13034614925766!3d19.172182353859956!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85c34136fd546f85%3A0xe7e6d38ced7b4b25!2sGonzalo%20de%20Sandoval%20174%2C%20poligono%201%2C%20Reforma%2C%2091919%20Veracruz%2C%20Ver.!5e0!3m2!1ses-419!2smx!4v1657140738812!5m2!1ses-419!2smx',
                'orden' => 2,
            ],
            [
                'ubicacion' => 'Cd. del Carmen',
                'subtitulo' => 'Ciudad del Carmen',
                'direccion' => 'Calle 53 # 74, Col. Morelos, Cd. del Carmen, Campeche.',
                'telefono' => "(+52) (999) 122 3651\n(+52) (938) 160 4654",
                'email' => 'cdcarmen@greenpoint.mx',
                'mapa_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3780.1763891315436!2d-91.82917044926279!3d18.656079069822226!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85f10781ff8261c3%3A0xb7d4145b2b9aec07!2sC.%2053%2074%2C%20Morelos%2C%2024115%20Cd%20del%20Carmen%2C%20Camp.!5e0!3m2!1ses-419!2smx!4v1657141404646!5m2!1ses-419!2smx',
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
        $pagina = PaginaNosotros::query()->firstOrNew([]);
        $pagina->fill([
            'titulo' => 'Quienes Somos',
            'subtitulo' => 'Greenpoint',
            'texto_descriptivo' => 'Greenpoint le ayuda que toda esta tecnología se ponga a trabajar para usted. Ya que contamos con la red satelital mas moderna a nivel mundial. Nuestro Telepuerto HUB Satelital iDirect, interconectado con la red corporativa de Pemex, lo que nos permite entregar micros y segmentos de red de Pemex previamente autorizados y supervisados por ellos.',
            'texto_adicional' => null,
            'url_video' => 'https://www.youtube.com/watch?v=yd1JhZzoS6A',
            'imagen_destacada' => 'images/demo/nosotros/nos10.jpg',
            'meta_descripcion' => 'Conoce GreenPoint: expertos en comunicaciones satelitales.',
            'meta_keywords' => 'greenpoint, nosotros, satelital, comunicaciones',
            'estado' => true,
        ]);
        $pagina->save();

        $imagenes = [
            'images/demo/nosotros/nos11.jpg',
            'images/demo/home/nos6.jpg',
            'images/demo/nosotros/con4.jpg',
        ];

        foreach ($imagenes as $index => $ruta) {
            PaginaNosotrosImagen::query()->updateOrCreate(
                [
                    'pagina_nosotros_id' => $pagina->id,
                    'orden' => $index + 1,
                ],
                ['ruta_imagen' => $ruta],
            );
        }

        $barras = [
            ['titulo' => 'Satisfaccion de clientes', 'porcentaje' => 100],
            ['titulo' => 'Soporte experimentado', 'porcentaje' => 100],
            ['titulo' => 'Garantia de servicios', 'porcentaje' => 100],
            ['titulo' => 'Garantia greenpoint', 'porcentaje' => 100],
        ];

        foreach ($barras as $index => $barra) {
            PaginaNosotrosProgreso::query()->updateOrCreate(
                [
                    'pagina_nosotros_id' => $pagina->id,
                    'orden' => $index + 1,
                ],
                [
                    'titulo' => $barra['titulo'],
                    'porcentaje' => $barra['porcentaje'],
                    'descripcion' => null,
                ],
            );
        }
    }

    protected function seedPaginaHistoria(): void
    {
        $pagina = PaginaHistoria::query()->firstOrNew([]);
        $pagina->fill([
            'titulo' => 'Historia',
            'meta_descripcion' => 'Línea de tiempo de la evolución de GreenPoint.',
            'meta_keywords' => 'historia, greenpoint, trayectoria',
            'estado' => true,
            'cv_etiqueta' => 'Servicios Greenpoint',
        ]);

        // Curriculum PDF (cgi-bin: cv.pdf)
        if (! $pagina->cv_pdf || ! \Illuminate\Support\Facades\Storage::disk('public')->exists($pagina->cv_pdf)) {
            $cvPath = 'historia/cv/servicios-greenpoint.pdf';
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('historia/cv');

            if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($cvPath)) {
                $demoCv = public_path('images/demo/cv/servicios-greenpoint.pdf');
                if (is_file($demoCv)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->put($cvPath, file_get_contents($demoCv));
                } else {
                    // Placeholder mínimo; reemplazable desde Admin → Páginas → Historia
                    \Illuminate\Support\Facades\Storage::disk('public')->put($cvPath, "%PDF-1.4\n1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj\n2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj\n3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources<< /Font<< /F1 5 0 R >> >> >>endobj\n4 0 obj<< /Length 80 >>stream\nBT /F1 18 Tf 72 720 Td (Servicios Greenpoint) Tj ET\nendstream\nendobj\n5 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj\nxref\n0 6\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000274 00000 n \n0000000404 00000 n \ntrailer<< /Size 6 /Root 1 0 R >>\nstartxref\n481\n%%EOF\n");
                }
            }

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cvPath)) {
                $pagina->cv_pdf = $cvPath;
            }
        }

        $pagina->save();

        $eventos = [
            ['anio' => 2004, 'titulo' => '2004', 'descripcion' => 'Inicio de operaciones en Veracruz, Veracruz. Se instalan los primeros Servicios de Internet de banda ancha en pozos petroleros.'],
            ['anio' => 2005, 'titulo' => '2005', 'descripcion' => 'Instalamos nuestra primer antena satelital en plataformas petroleras, frente a las costas de Campeche y Tabasco.'],
            ['anio' => 2006, 'titulo' => '2006', 'descripcion' => 'Se instalan 30 VSAT´s en pozos petroleros de tierra para la empresa QMAX de fluidos de perforación. En las Ciudades de Villahermosa, Cd. Del Carmen, Veracruz, Poza Rica y Reynosa. Para servicios de Internet Satelital.'],
            ['anio' => 2006, 'titulo' => '2006b', 'descripcion' => 'Se logran contratos con la empresa Weatherford para atender distintas líneas de negocio como son Precision Drilling, fluidos, Internacional Logging; conectando más de 50 pozos y plataformas de todo el país.'],
            ['anio' => 2007, 'titulo' => '2007', 'descripcion' => 'Groenpunt, S.A. de C.V. (Greenpoint) se constituye como sociedad mercantil.'],
            ['anio' => 2007, 'titulo' => '2007b', 'descripcion' => 'Greenpoint obtiene el permiso de Servicios de Valor Agregado de la COFETEL, para dar servicios de Internet.'],
            ['anio' => 2008, 'titulo' => '2008', 'descripcion' => 'Greenpoint logra contratos con la empresa Baker Hughes, para conectividad satelital en proyectos de la línea INTEC, para transmission de datos en tiempo real.'],
            ['anio' => 2009, 'titulo' => '2009', 'descripcion' => 'Se abre sucursal en Villahermosa, Tabasco y se inicia el proyecto del telepuerto satelital en esa Ciudad.'],
            ['anio' => 2010, 'titulo' => '2010', 'descripcion' => 'Inicia operaciones nuestro propio telepuerto iDirect, el único en su tipo en todo el sureste.'],
            ['anio' => 2011, 'titulo' => '2011', 'descripcion' => 'La intranet corporativa de PEMEX, es conectada vía microondas con nuestro telepuerto, y extendida a través de nuestra red satelital a locaciones remotas y campamentos de exploración. Alcanzamos más de 250 pozos petroleros terrestres conectados a Internet vía satélite con distintas empresas petroleras.'],
            ['anio' => 2012, 'titulo' => '2012', 'descripcion' => 'Se logran contratos para comunicaciones en ubicaciones remotas de la empresa COMESA.'],
            ['anio' => 2013, 'titulo' => '2013', 'descripcion' => 'Inicia operaciones nuestra red inalambrica en Tabasco. Logramos nuestros primeros contratos para Servicios de comunicación en movimiento, Servicios maritimos en barcos de la empresa COSL, Oceanografia y Cotemar.'],
            ['anio' => 2014, 'titulo' => '2014', 'descripcion' => 'Se logra extender la intranet de la empresa Mexoil, a través de nuestras redes satelitales y microondas, en distintas plataformas offshore y pozos de tierra.'],
            ['anio' => 2015, 'titulo' => '2015', 'descripcion' => 'Se inician Servicios de Petronet, la red hibrida de comunicaciones satelitales e inalámbricas que completa los 15,000 Km2, dando cobertura en 3 estados y más de 200 Km. a lo largo de la costa del Golfo de México entre Ciudad del Carmen y Paraíso.'],
            ['anio' => 2016, 'titulo' => '2016', 'descripcion' => 'Greenpoint obtiene la CONCESION UNICA de telecomunicaciones por parte del IFETEL para instalar, operar y explotar redes satelitales y microondas para los servicios de internet y redes privadas.'],
            ['anio' => 2018, 'titulo' => '2018', 'descripcion' => 'Greenpoint lanza su APP Movil de monitoreo donde se puede observar las trayectorias y estatus de las antenas Maritimas instaladas en los barcos asi como el estatus y ubicaciones de las antenas satelitales fijas en pozos y plataformas.'],
            ['anio' => 2019, 'titulo' => '2019', 'descripcion' => 'Se levanta nueva red y se comienza a trabajar con la nueva tecnologia DVB-S2X'],
        ];

        PaginaHistoriaEvento::query()->where('pagina_historia_id', $pagina->id)->delete();

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
            'images/demo/historia/h1.jpg',
            'images/demo/historia/h2.jpg',
        ];

        foreach ($imagenes as $index => $ruta) {
            PaginaHistoriaImagen::query()->updateOrCreate(
                [
                    'pagina_historia_id' => $pagina->id,
                    'orden' => $index + 1,
                ],
                ['ruta_imagen' => $ruta],
            );
        }
    }

    protected function seedPaginaTecnologia(): void
    {
        $pagina = PaginaTecnologia::query()->firstOrNew([]);
        $pagina->fill([
            'titulo' => 'Tecnologia',
            'contenido' => null,
            'imagen_destacada' => 'images/demo/tecnologia/tec1.jpg',
            'meta_descripcion' => 'Tecnología e infraestructura satelital de GreenPoint.',
            'meta_keywords' => 'tecnologia, idirect, satelital',
            'estado' => true,
        ]);
        $pagina->save();

        // cgi-bin: no hay secciones de texto; todo viene en tec1.jpg
        PaginaTecnologiaSeccion::query()
            ->where('pagina_tecnologia_id', $pagina->id)
            ->delete();
    }

    protected function seedPaginaAviso(): void
    {
        $pagina = PaginaAviso::query()->firstOrNew([]);
        $pagina->fill([
            'titulo' => 'Aviso de privacidad',
            'meta_descripcion' => 'Aviso de privacidad de GreenPoint.',
            'meta_keywords' => 'aviso de privacidad, datos personales',
            'estado' => true,
        ]);
        $pagina->save();

        PaginaAvisoSeccion::query()
            ->where('pagina_aviso_id', $pagina->id)
            ->get()
            ->each(function ($seccion) {
                $seccion->listas()->delete();
                $seccion->delete();
            });

        $secciones = [
            [
                'titulo' => '',
                'contenido' => "GREENPOINT S.A. de C.V., y/o sus empresas asociadas, afiliadas y subsidiarias (“GREENPOINT”), con domicilio en Francisco Sarabia # 126, Col. Gil y Saenz, Municipio Centro, Villahermosa, Tabasco, México, es responsable de recabar sus datos personales, incluyendo: datos personales de identificación: (i) nombre completo; (ii) domicilio; (iii) teléfono; (iv) correo electrónico; y/o (v) datos financieros, ya sea en las páginas web de GREENPOINT o a través de cualquier otro medio autorizado por GREENPOINT, le comunicamos por este medio, que GREENPOINT es el único responsable del tratamiento, uso, almacenamiento y/o divulgación, tratamiento y adecuada protección de sus datos personales conforme a lo establecido en la Ley de la materia y el presente Aviso de Privacidad.\nLos sitios, programas y/o servicios que ofrezca GREENPOINT tienen la finalidad de garantizar, recopilar, mantener y proteger la privacidad de la información personal sobre los suscriptores, visitantes, clientes, proveedores, usuarios y cualquier otra persona interesada en los servicios de GREENPOINT (los “Usuarios”), de conformidad con este Aviso de Privacidad, así como con las leyes, reglamentos y demás normatividad aplicable. Este Aviso de Privacidad aplica a información personal recopilada a través de los recursos y comunicaciones de GREENPOINT en línea o en forma impresa como contratos, formatos, circulares, avisos, incluyendo el Sitio, correo electrónico y otras herramientas en línea. Este Aviso de Privacidad no aplica a información personal recopilada por los recursos de terceros en línea con los cuales pueden tener un vínculo los sitios de Internet de GREENPOINT, en lo que este último no controle el contenido o las prácticas de privacidad de dichos recursos. GREENPOINT sólo recopila información personalmente, identificable sobre los visitantes del Sitio, programas y/o servicios otorgados por GREENPOINT, si los visitantes así lo eligen.",
                'listas' => [],
            ],
            [
                'titulo' => '',
                'contenido' => 'Protección de Datos Personales.- En cumplimiento con lo establecido en la Ley Federal de Protección de Datos Personales en Posesión de los Particulares, publicada en el Diario Oficial de la Federación el 5 de Julio de 2010, su Reglamento y/o Lineamientos aplicables (en lo sucesivo la “Ley”), y con la finalidad de garantizar la privacidad de nuestros Usuarios en compartir sus datos personales, le informamos lo siguiente: Los datos personales de nuestros Usuarios, serán utilizados para los siguientes fines: Para ser contactado vía correo electrónico, por escrito, por teléfono, por mensaje de texto o cualquier otro medio, ya sea físico o electrónico del que se disponga y enviar información a fin de dar seguimiento a sus peticiones, sugerencias, reclamaciones y/o cualquier otra solicitud o para realizar acciones de actualizaciones técnicas de los productos o servicios que hubiese adquirido, para crear una relación comercial, dar seguimiento y mantener comunicación en general.',
                'listas' => [],
            ],
            [
                'titulo' => '',
                'contenido' => "Para identificar, ubicar, comunicar, contactar, enviar información y/o mercancía a los Usuarios.- De igual forma, la información proporcionada por los Usuarios podrá ser también utilizada para diferentes fines de Recursos Humanos (manejo de desempeño, decisiones de sucesión o acciones de desarrollo, entre otras), Mercadotécnicos, Publicitarios o de Prospección Comercial, los cuales servirán para entender mejor sus necesidades y la manera en que podemos mejorar nuestros servicios, así como para personalizar ciertas comunicaciones con los Usuarios sobre servicios y promociones que los mismos pueden encontrar interesantes.\n\nEn caso de que los datos personales del Usuario no hayan sido obtenidos de manera directa por el titular de dichos datos, este último tendrá un plazo máximo de 5 (cinco) días hábiles para que pueda manifestar su negativa para el tratamiento de sus datos personales con respecto al párrafo anterior, teniendo a salvo los medios para ejercer sus derechos ARCO, los cuales se mencionan en el presente Aviso.",
                'listas' => [],
            ],
            [
                'titulo' => '',
                'contenido' => 'Transferencia de los datos personales.- Los datos personales recabados por GREENPOINT, podrán ser cedidos, vendidos, compartidos o transferidos a un tercero con quien GREENPOINT tenga alguna relación comercial, incluyendo de manera enunciativa más no limitativa, en los siguientes casos en que sea requerida:',
                'listas' => [
                    '1.- Autoridades Fiscales: Para el cumplimiento de las obligaciones fiscales;',
                    '2.- Autoridades Gubernamentales, Administrativas y/o Judiciales: Para la realización de procedimientos legales, auditorías o juicios correspondientes;',
                    '3.- Instituciones Bancarias: Para realizar en caso de ser aplicable, el cobro de nuestros servicios a través de su tarjeta o realizar pagos mediante transacciones electrónicas;',
                    '4.- Empleados, Asesores, Afiliadas, Proveedores: Para la realización de las actividades diarias de GREENPOINT para llevar a cabo el servicio solicitado por el Usuario;',
                    '5.- Terceros: Para el envío de publicidad de los Terceros con quien GREENPOINT tenga alguna relación comercial.',
                ],
            ],
            [
                'titulo' => '',
                'contenido' => 'En caso de que el Usuario no acepte que sus datos personales sean transmitan a un Tercero, deberá enviar un correo electrónico indicando que se opone a dicha transferencia tal y como se detalla en el siguiente punto.',
                'listas' => [],
            ],
            [
                'titulo' => 'Medios para ejercer los derechos ARCO.',
                'contenido' => "El Usuario, en relación con sus datos personales, podrá ejercer sus derechos de accesar a sus datos personales que GREENPOINT posee, rectificar sus datos personales cuando sean inexactos o incompletos, cancelar sus datos personales cuando considere que son excesivos o innecesarios para las finalidades del tratamiento o haya finalizado la relación contractual o de servicio u oponerse a proporcionar sus datos personales para los fines aquí mencionados, así como revocar su consentimiento para el tratamiento de sus datos personales, a fin de que GREENPOINT deje de hacer uso de los mismos. Lo anterior lo podrá realizar el Usuario a través de una solicitud por escrito dirigida a GREENPOINT, la cual podrá presentarse a través de las siguientes opciones: a) Mediante correo electrónico: contacto@greenpoint.mx, en la cual el Usuario tendrá que acreditar su identidad mediante el envío de: a.1) Fotocopia de cualquier identificación oficial (credencial de elector, pasaporte, cartilla militar o cédula profesional); o En caso de actuar en representación de un tercero, deberá presentar copia de la escritura pública del poder o carta poder simple otorgada al representante legal del Usuario, según corresponda, así como su identificación correspondiente; b) Sea(n) entregado(s) directamente en el domicilio de GREENPOINT, Francisco Sarabia # 126, Col. Gil y Saenz, Municipio Centro, Villahermosa, Tabasco, México, de Lunes a Viernes de 9:00 a 18:00 horas, con la acreditación correspondiente mencionada en el inciso a) anterior.\n\nAsimismo, GREENPOINT tiene un plazo máximo de 20 (veinte) días naturales, contados a partir de la fecha en que recibió la solicitud de acceso, rectificación, cancelación u oposición, para comunicarle al Usuario a través del mismo medio en que recibió la solicitud, si se acepta o no la misma, y en caso de que así sea, GREENPOINT tiene un plazo de 15 (quince) días naturales siguientes a la fecha en que se comunicó la respuesta al Usuario, para realizar las modificaciones correspondientes o entregar copia simple de la información solicitada. Los plazos antes mencionados podrán ser ampliados una sola vez por un periodo igual, siempre y cuando así GREENPOINT lo justifique y conforme a lo establecido en el artículo 32 de la Ley.",
                'listas' => [],
            ],
            [
                'titulo' => 'De igual forma, GREENPOINT podrá negar el acceso, rectificación, cancelación u oposición a los datos personales del Usuario, cuando:',
                'contenido' => '',
                'listas' => [
                    '1) El Usuario no sea el titular de los datos personales, o el representante legal no esté debidamente acreditado para ello;',
                    '2) Cuando en su base de datos, no se encuentren los datos personales del solicitante;',
                    '3) Cuando se lesionen los derechos de un tercero;',
                    '4) Cuando exista un impedimento legal, o la resolución de una autoridad competente, que restrinja el acceso a los datos personales del Usuario, o no permita la rectificación, cancelación u oposición de los mismos,',
                    '5) Cuando la rectificación, cancelación u oposición haya sido previamente realizada. En todos los casos anteriores, GREENPOINT informará al Usuario o al representante legal, según sea el caso, el motivo de su decisión por el mismo medio por el que se llevó a cabo la solicitud, acompañando, en su caso, las pruebas que resulten pertinentes.',
                ],
            ],
            [
                'titulo' => '',
                'contenido' => "Mecanismos y Procedimientos para la Revocación del Consentimiento.- Para el caso de que el Usuario quisiera revocar su consentimiento para el tratamiento de sus datos personales, deberá seguir los pasos establecidos anteriormente, referentes a los medios para ejercer sus derechos ARCO y el tiempo de respuesta por parte de GREENPOINT, será conforme a lo establecido en el párrafo anterior.\n\nMedidas de seguridad.- GREENPOINT garantiza en este acto que los datos personales del Usuario se almacenarán en las bases de datos de GREENPOINT, las cuales permanecerán durante el periodo necesario para cumplir con la finalidad específica para los cuales fueron recabados y de acuerdo a lo establecido en la Ley, siento el Usuario el único responsable de la veracidad de los datos que proporciona. GREENPOINT utiliza precauciones de tecnología, reglas y otros procedimientos de seguridad para proteger sus datos personales contra accesos no autorizados, mal uso, divulgación, pérdida o destrucción. Para asegurar la confidencialidad de sus datos, GREENPOINT también utiliza la protección estándar de la industria con firewalls y contraseñas. Sin embargo, es responsabilidad del Usuario asegurarse que la computadora que está utilizando esté asegurada y protegida adecuadamente contra software malicioso, como virus trojanos, virus de cómputo y programas dañinos. El Usuario está enterado de que sin medidas de seguridad adecuadas (por ejemplo, configuración segura del browser web, software antivirus actualizado, software firewall personal, no usar software de fuentes dudosas) existe un riesgo de que los datos y contraseñas que el Usuario utiliza para proteger el acceso a sus datos, podrían ser descubiertos por terceros no autorizados.\n\nNota para Usuarios de Sitios de Internet de Negocios o Profesionales.- Si el Usuario tiene una relación de negocios o profesional con GREENPOINT, podemos utilizar la información que el Usuario proporciona en nuestros sitios, incluyendo el Sitio y sitios específicamente dedicados a usuarios de negocios y profesionales, para satisfacer sus solicitudes y desarrollar nuestra relación de negocios con el Usuario y las entidades que este último representa. También podemos compartir dicha información con terceros que actúan en nuestra representación.\n\nOpciones del Usuario.- El Usuario tiene varias opciones referentes al uso de nuestros sitios de Internet, incluyendo el Sitio. El Usuario deberá proporcionar información personalmente identificable para entrar a las formas o campos de datos de nuestros sitios, incluyendo el Sitio y así poder utilizar los servicios personalizados disponibles. Si el Usuario proporciona sus datos personales, tiene derecho a ver y a corregir sus datos en cualquier momento accesando a la aplicación que corresponda. Determinados sitios pueden solicitar el permiso del Usuario para ciertos usos de su información y el Usuario puede aceptar o rechazar esos usos. Si el Usuario decide solicitar servicios o comunicaciones particulares, como un boletín o e-newsletter, podrá anular su suscripción en cualquier momento siguiendo las instrucciones incluidas en cada comunicación. Si el Usuario decide anular su suscripción de un servicio o comunicación, trabajaremos para eliminar su información oportunamente, aunque es posible que solicitemos información adicional al Usuario antes de poder procesar su petición.\n\nDatos de menores de edad o incapaces.- En caso de que pudieran recabarse datos de menores de edad o incapaces en función de la información que hubiera sido proporcionada respecto a su edad o año de nacimiento por el propio usuario, “GREENPOINT” hará sus mejores esfuerzos para no utilizar estos datos para fines inadecuados para la edad del menor o en relación con el incapaz. En todo caso, “GREENPOINT” garantiza que gestionará las solicitudes derivadas del ejercicio de los derechos de acceso, cancelación, rectificación y oposición de los datos de los menores o incapaces por los padres o tutores.\n\nAutoridad encargada de la Protección de sus Datos.- Si considera que su derecho a la protección de sus datos personales ha sido vulnerado o lesionado por alguna conducta u omisión por parte de GREENPOINT, o presume alguna violación a las disposiciones previstas en la Ley, podrá interponer su inconformidad o denuncia ante el Instituto Nacional de Transparencia, Acceso a la Información y Protección de Datos Personales (INAI). Para mayores informes, visite su página web: www.inai.org.mx.\n\nModificaciones al Aviso de Privacidad.- De igual forma, GREENPOINT se reserva el derecho de efectuar en cualquier momento modificaciones o actualizaciones al presente Aviso de Privacidad, derivado de cualquier reforma o modificación de la Ley, por política interna o por algún requerimiento para la prestación de nuestros servicios, en cuyo caso lo podrán verificar en el presente Aviso de Privacidad en este el sitio www.greenpoint.mx\n\nSi el Usuario continúa utilizando nuestros sitios y/o servicios, incluyendo el Sitio, dicha conducta indica que el Usuario acepta el uso de la información recién proporcionada de conformidad con el presente Aviso de Privacidad.\n\nSi usted tiene cualquier duda acerca de nuestras prácticas de privacidad y protección de datos personales le sugerimos nos contacte a través de la dirección de correo electrónico:",
                'listas' => [],
            ],
        ];

        foreach ($secciones as $index => $data) {
            $seccion = PaginaAvisoSeccion::create([
                'pagina_aviso_id' => $pagina->id,
                'titulo' => $data['titulo'],
                'contenido' => $data['contenido'],
                'orden' => $index + 1,
            ]);

            foreach ($data['listas'] as $li => $texto) {
                PaginaAvisoLista::create([
                    'pagina_aviso_seccion_id' => $seccion->id,
                    'texto' => $texto,
                    'orden' => $li + 1,
                ]);
            }
        }
    }
}
