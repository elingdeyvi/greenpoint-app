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
                'descripcion' => "Equipado con la mejor tecnología a nivel mundial. Permite entregar a sus clientes una conexión robusta, compartida o servicios dedicados con administración del QoS, para asignar prioridades a las aplicaciones críticas como son voz, video o información en tiempo real.\n\n[features]\nAnchos de banda desde 64 Kbps hasta 100 Mbps\nServicio dedicado o en tasa compartida\nCIR (committed information rate)\nIP Públicas Homologadas\nConexión al backbone de internet o WAN empresarial\nQoS ajustable a las necesidades de cada cliente\n[/features]\n\n[badges]\nVSAT FIJA MANUAL\nVSAT SEMI-FIJA AUTOMATICA\nVSAT AUTOESTABILIZADA\nVSAT BAJO PERFIL EN MOVIMIENTO\nSERVICIOS SATELITALES IP\nSERVICIOS DE ACCESO A INTERNET\nRED PRIVADA SATELITAL\nTRANSPORTE DE VOZ, DATOS Y VIDEO\nCELLULAR BACKHAUL\nDISTRIBUCION DE CONTENIDO\n[/badges]",
                'imagen' => 'images/demo/servicios/tec2.jpg',
                'orden' => 1,
                'activo' => true,
            ],
            [
                'nombre' => 'Soluciones',
                'descripcion' => "Soluciones a medida para el sector petrolero, marítimo y energético. Conectividad satelital e inalámbrica diseñada para operaciones críticas en tierra y costa afuera.\n\n[features]\nEnlaces satelitales dedicados\nComunicaciones marítimas\nRedes privadas IP\nSoporte 24/7 en campo\n[/features]",
                'imagen' => 'images/demo/servicios/internet-satelital.jpg',
                'orden' => 2,
                'activo' => true,
            ],
            [
                'nombre' => 'Hardware Satelital y Redes Wan',
                'descripcion' => "Hardware satelital y redes WAN para mantener su operación conectada con la mejor tecnología disponible.\n\n[features]\nAntenas VSAT fijas y móviles\nEquipos iDirect\nEnlaces microondas\nIntegración WAN empresarial\n[/features]",
                'imagen' => 'images/demo/servicios/red-privada-ip.jpg',
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
        // Fotos de antenas de producción:
        // - img/proyectos/01-07.jpg → home carousel
        // - galeria/g1-g8.jpg → página Galería
        $items = [
            [
                'titulo' => 'Antena VSAT — instalación en campo',
                'descripcion' => 'Proyecto de enlace satelital en operación.',
                'imagen' => 'images/demo/proyectos/01.jpg',
                'orden' => 1,
            ],
            [
                'titulo' => 'Telepuerto GreenPoint',
                'descripcion' => 'Antenas de comunicación satelital.',
                'imagen' => 'images/demo/proyectos/02.jpg',
                'orden' => 2,
            ],
            [
                'titulo' => 'Enlace satelital dedicado',
                'descripcion' => 'Infraestructura para sector energético.',
                'imagen' => 'images/demo/proyectos/03.jpg',
                'orden' => 3,
            ],
            [
                'titulo' => 'Antena nocturna — telepuerto',
                'descripcion' => 'Operación continua 24/7.',
                'imagen' => 'images/demo/proyectos/04.jpg',
                'orden' => 4,
            ],
            [
                'titulo' => 'Instalación offshore',
                'descripcion' => 'Comunicaciones marítimas y plataformas.',
                'imagen' => 'images/demo/proyectos/05.jpg',
                'orden' => 5,
            ],
            [
                'titulo' => 'Proyecto satelital al atardecer',
                'descripcion' => 'Despliegue en sitio remoto.',
                'imagen' => 'images/demo/proyectos/06.jpg',
                'orden' => 6,
            ],
            [
                'titulo' => 'Array de antenas GreenPoint',
                'descripcion' => 'Capacidad multi-enlace.',
                'imagen' => 'images/demo/proyectos/07.jpg',
                'orden' => 7,
            ],
            [
                'titulo' => 'Galería — antena 01',
                'descripcion' => 'Registro fotográfico de proyectos realizados.',
                'imagen' => 'images/demo/galeria/galeria1.jpg',
                'orden' => 8,
            ],
            [
                'titulo' => 'Galería — antena 02',
                'descripcion' => 'Registro fotográfico de proyectos realizados.',
                'imagen' => 'images/demo/galeria/galeria2.jpg',
                'orden' => 9,
            ],
            [
                'titulo' => 'Galería — antena 03',
                'descripcion' => 'Registro fotográfico de proyectos realizados.',
                'imagen' => 'images/demo/galeria/galeria3.jpg',
                'orden' => 10,
            ],
            [
                'titulo' => 'Galería — antena 04',
                'descripcion' => 'Registro fotográfico de proyectos realizados.',
                'imagen' => 'images/demo/galeria/galeria4.jpg',
                'orden' => 11,
            ],
            [
                'titulo' => 'Galería — antena 05',
                'descripcion' => 'Registro fotográfico de proyectos realizados.',
                'imagen' => 'images/demo/galeria/galeria5.jpg',
                'orden' => 12,
            ],
            [
                'titulo' => 'Galería — antena 06',
                'descripcion' => 'Registro fotográfico de proyectos realizados.',
                'imagen' => 'images/demo/galeria/galeria6.jpg',
                'orden' => 13,
            ],
            [
                'titulo' => 'Galería — antena 07',
                'descripcion' => 'Registro fotográfico de proyectos realizados.',
                'imagen' => 'images/demo/galeria/galeria7.jpg',
                'orden' => 14,
            ],
            [
                'titulo' => 'Galería — antena 08',
                'descripcion' => 'Registro fotográfico de proyectos realizados.',
                'imagen' => 'images/demo/galeria/galeria8.jpg',
                'orden' => 15,
            ],
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
        ]);
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
                'contenido' => 'GREENPOINT S.A. de C.V., y/o sus empresas asociadas, afiliadas y subsidiarias (“GREENPOINT”), con domicilio en Francisco Sarabia # 126, Col. Gil y Saenz, Municipio Centro, Villahermosa, Tabasco, México, es responsable de recabar sus datos personales, incluyendo: datos personales de identificación: (i) nombre completo; (ii) domicilio; (iii) teléfono; (iv) correo electrónico; y/o (v) datos financieros, ya sea en las páginas web de GREENPOINT o a través de cualquier otro medio autorizado por GREENPOINT, le comunicamos por este medio, que GREENPOINT es el único responsable del tratamiento, uso, almacenamiento y/o divulgación, tratamiento y adecuada protección de sus datos personales conforme a lo establecido en la Ley de la materia y el presente Aviso de Privacidad.

Los sitios, programas y/o servicios que ofrezca GREENPOINT tienen la finalidad de garantizar, recopilar, mantener y proteger la privacidad de la información personal sobre los suscriptores, visitantes, clientes, proveedores, usuarios y cualquier otra persona interesada en los servicios de GREENPOINT (los “Usuarios”), de conformidad con este Aviso de Privacidad, así como con las leyes, reglamentos y demás normatividad aplicable.',
                'listas' => [],
            ],
            [
                'titulo' => '',
                'contenido' => 'Protección de Datos Personales.- En cumplimiento con lo establecido en la Ley Federal de Protección de Datos Personales en Posesión de los Particulares, publicada en el Diario Oficial de la Federación el 5 de Julio de 2010, su Reglamento y/o Lineamientos aplicables (en lo sucesivo la “Ley”), y con la finalidad de garantizar la privacidad de nuestros Usuarios en compartir sus datos personales, le informamos lo siguiente: Los datos personales de nuestros Usuarios, serán utilizados para los siguientes fines: Para ser contactado vía correo electrónico, por escrito, por teléfono, por mensaje de texto o cualquier otro medio, ya sea físico o electrónico del que se disponga y enviar información a fin de dar seguimiento a sus peticiones, sugerencias, reclamaciones y/o cualquier otra solicitud o para realizar acciones de actualizaciones técnicas de los productos o servicios que hubiese adquirido, para crear una relación comercial, dar seguimiento y mantener comunicación en general.',
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
                'titulo' => 'Medios para ejercer los derechos ARCO.',
                'contenido' => 'El Usuario, en relación con sus datos personales, podrá ejercer sus derechos de accesar a sus datos personales que GREENPOINT posee, rectificar sus datos personales cuando sean inexactos o incompletos, cancelar sus datos personales cuando considere que son excesivos o innecesarios para las finalidades del tratamiento o haya finalizado la relación contractual o de servicio u oponerse a proporcionar sus datos personales para los fines aquí mencionados, así como revocar su consentimiento para el tratamiento de sus datos personales, a fin de que GREENPOINT deje de hacer uso de los mismos. Lo anterior lo podrá realizar el Usuario a través de una solicitud por escrito dirigida a GREENPOINT, la cual podrá presentarse mediante correo electrónico: contacto@greenpoint.mx, o directamente en el domicilio de GREENPOINT.

De igual forma, GREENPOINT podrá negar el acceso, rectificación, cancelación u oposición a los datos personales del Usuario, cuando:',
                'listas' => [
                    '1) El Usuario no sea el titular de los datos personales, o el representante legal no esté debidamente acreditado para ello;',
                    '2) Cuando en su base de datos, no se encuentren los datos personales del solicitante;',
                    '3) Cuando se lesionen los derechos de un tercero;',
                    '4) Cuando exista un impedimento legal, o la resolución de una autoridad competente, que restrinja el acceso a los datos personales del Usuario, o no permita la rectificación, cancelación u oposición de los mismos,',
                    '5) Cuando la rectificación, cancelación u oposición haya sido previamente realizada.',
                ],
            ],
            [
                'titulo' => '',
                'contenido' => 'Modificaciones al Aviso de Privacidad.- GREENPOINT se reserva el derecho de efectuar en cualquier momento modificaciones o actualizaciones al presente Aviso de Privacidad. Si usted tiene cualquier duda acerca de nuestras prácticas de privacidad y protección de datos personales le sugerimos nos contacte a través de: contacto@greenpoint.mx',
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
