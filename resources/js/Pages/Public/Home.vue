<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import BannerCarousel from '@/Components/Public/BannerCarousel.vue';
import GalleryCarousel from '@/Components/Public/GalleryCarousel.vue';
import { usePublicSite } from '@/composables/usePublicSite';

const props = defineProps({
    banners: {
        type: Array,
        default: () => [],
    },
    servicios: {
        type: Array,
        default: () => [],
    },
    galeria: {
        type: Array,
        default: () => [],
    },
    nosotros: {
        type: Object,
        default: null,
    },
    config: {
        type: Object,
        default: () => ({}),
    },
});

const { getConfig } = usePublicSite();

const cfg = (clave, fallback = '') => {
    const fromProp = props.config?.[clave];
    if (fromProp !== undefined && fromProp !== null && fromProp !== '') {
        return fromProp;
    }
    return getConfig(clave, fallback);
};

const parseJson = (clave, fallback) => {
    try {
        const raw = cfg(clave, '');
        if (!raw) return fallback;
        const parsed = typeof raw === 'string' ? JSON.parse(raw) : raw;
        return Array.isArray(parsed) && parsed.length ? parsed : fallback;
    } catch {
        return fallback;
    }
};

// Tarjetas del index (cgi-bin): distintas del menú Servicios (Conexion / Soluciones / Hardware).
const defaultHomeServiceCards = [
    {
        nombre: 'Internet Satelital',
        descripcion:
            'Con la tecnología satelital de Greenpoint, usted puede contar con un servicio completo y en cualquier ubicación.',
        icon: '/images/demo/icons/icon-04.png',
    },
    {
        nombre: 'Nube Satelital Dedicada',
        descripcion:
            'Conexión permanente a internet de alta velocidad simétrica de subida y bajada, brindada por nuestra red.',
        icon: '/images/demo/icons/icon-02.png',
    },
    {
        nombre: 'Red Privada IP',
        descripcion:
            'La Telefonía IP que permite disfrutar de todas las características de nuestro sistema que está diseñado de manera inteligente.',
        icon: '/images/demo/icons/icon-03.png',
    },
    {
        nombre: 'Internet fijo y movil',
        descripcion:
            'Instalación en pozos y plataformas. Servicios satelitales dedicados. SCPC punto a punto Satelital. Enlaces microondas, etc.',
        icon: '/images/demo/icons/icon-01.png',
    },
];

const homeServiceCards = parseJson('home_service_cards', defaultHomeServiceCards);

const featuredServicios = computed(() =>
    homeServiceCards.map((card, index) => ({
        ...card,
        id: props.servicios[index]?.id ?? null,
    })),
);
const galleryItems = computed(() => props.galeria.slice(0, 12));
const hasBanners = computed(() => props.banners.length > 0);

const aboutChecks = parseJson('home_about_checks', [
    'Mantén tu empresa o negocio siempre conectado',
    'Internet Satelital perfecto para todas sus necesidades',
    'Utilizamos las ultimas tecnologías de conectividad',
]);

const whyLeft = parseJson('home_why_left', [
    {
        title: 'Alta Calidad',
        text: 'La banda ancha le proporciona una conexión de alta velocidad a Internet.',
        icon: '/images/demo/icons/icon-07.png',
    },
    {
        title: 'Expertos',
        text: 'Especialistas en comunicaciones satelitales para el sector energético.',
        icon: '/images/demo/icons/icon-08.png',
    },
    {
        title: 'Soporte 24/7',
        text: 'Acompañamiento técnico continuo para mantener tu operación conectada.',
        icon: '/images/demo/icons/icon-09.png',
    },
]);

const whyRight = parseJson('home_why_right', [
    {
        title: 'El mejor Costo Calidad',
        text: 'Soluciones a la medida con el mejor balance entre desempeño e inversión.',
        icon: '/images/demo/icons/icon-10.png',
    },
    {
        title: 'Internet Privada',
        text: 'Enlaces dedicados y redes privadas IP para operaciones críticas.',
        icon: '/images/demo/icons/icon-11.png',
    },
    {
        title: 'Conexión Ultrarápida',
        text: 'Tecnología iDirect y enlaces de alto rendimiento en cualquier ubicación.',
        icon: '/images/demo/icons/icon-12.png',
    },
]);

/** Márgenes cgi-bin WHY CHOOSE (mb-1-9 / mb-md-6 / ms-lg-6 / me-lg-6) */
const whyLeftItemClass = (index) =>
    [
        'ms-lg-6 mb-1-9 mb-md-6',
        'mb-1-9 mb-md-6 me-lg-6',
        'mb-1-9 mb-md-0 ms-lg-6',
    ][index] ?? 'mb-1-9';

const whyRightItemClass = (index) =>
    [
        'me-lg-6 mb-1-9 mb-md-6',
        'mb-1-9 mb-md-6 ms-lg-6',
        'me-lg-6',
    ][index] ?? 'mb-1-9';

const featureCards = parseJson('home_feature_cards', [
    { title: 'Servicios de Calidad', icon: 'ti-medall' },
    { title: 'Internet ilimitado', icon: 'ti-cloud-down' },
    { title: 'Garantía Greenpoint', icon: 'ti-calendar' },
    { title: 'Soporte Profesional', icon: 'ti-user' },
]);

// Arte del about del index (cgi-bin nos1/nos2); no reutilizar imágenes de /nosotros (nos10/nos11).
const nosotrosImagen = '/images/demo/home/nos1.jpg';
const nosotrosImagenAlt = '/images/demo/home/nos2.jpg';
const aboutVideoThumb = '/images/demo/home/nos3.jpg';
const videoBackground = '/images/demo/home/nos6.jpg';
const offerBackground = '/images/demo/home/nos4.jpg';
const galleryBackground = '/images/demo/home/nos7.jpg';
const videoUrl = computed(() =>
    cfg('home_video_url', 'https://www.youtube.com/watch?v=yd1JhZzoS6A'),
);
</script>

<template>
    <Head title="Inicio" />

    <PublicLayout>
        <BannerCarousel v-if="hasBanners" :banners="banners" />

        <section v-else class="gp-hero">
            <div class="container">
                <h1 class="mb-3">
                    Líder en comunicaciones
                    <span class="font-weight-400">para el sector Petrolero</span>
                </h1>
                <p v-if="cfg('empresa_descripcion')" class="gp-hero-subtitle mb-4">
                    {{ cfg('empresa_descripcion') }}
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <Link :href="route('public.nosotros')" class="butn"><span>Leer Más</span></Link>
                    <Link :href="route('public.contacto')" class="butn secondary">Contacto</Link>
                </div>
            </div>
        </section>

        <!-- Servicios destacados (tarjeta flotante) — mismo markup que greenpoint.com.mx / cgi-bin -->
        <section class="p-0 overflow-visible">
            <div class="container">
                <div class="service-style1 pt-6 pt-lg-0">
                    <div class="row g-0 align-items-center">
                        <div class="col-lg-4 mb-5 mb-lg-0">
                            <div class="px-5">
                                <div class="section-heading text-start mb-0">
                                    <span class="subtitle">{{ cfg('sitio_nombre', 'Greenpoint') }}</span>
                                    <h2 class="mb-0 w-100">
                                        {{ cfg('home_servicios_titulo', 'Internet Satelital') }}
                                        <span class="font-weight-400">{{
                                            cfg('home_servicios_subtitulo', 'Expertos en comunicaciones')
                                        }}</span>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div v-if="featuredServicios.length" class="row g-0">
                                <div
                                    v-for="(servicio, index) in featuredServicios"
                                    :key="`home-svc-${index}`"
                                    class="col-md-6"
                                    v-reveal="{ delay: 150 + index * 100 }"
                                >
                                    <div
                                        class="card border-0 card-style1 h-100"
                                        :class="{ active: index === 0 }"
                                    >
                                        <div class="card-body">
                                            <div class="card-icon">
                                                <img
                                                    :src="servicio.icon"
                                                    :alt="servicio.nombre"
                                                    class="mb-4"
                                                />
                                                <span class="round-shape"></span>
                                            </div>
                                            <h4 class="mb-4">
                                                <Link
                                                    :href="
                                                        servicio.id
                                                            ? route('public.servicios.show', servicio.id)
                                                            : route('public.servicios.index')
                                                    "
                                                    class="text-reset"
                                                >
                                                    {{ servicio.nombre }}
                                                </Link>
                                            </h4>
                                            <p class="mb-0">{{ servicio.descripcion }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="p-5 text-muted">
                                Muy pronto publicaremos nuestro catálogo de servicios.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-inline-block p-2 border-secondary border border-width-2 position-absolute left-5 bottom-25 ani-left-right"></div>
            <div class="d-inline-block p-2 bg-primary rounded-circle position-absolute left-10 top-25 ani-move"></div>
        </section>

        <!-- Quiénes somos / About (cgi-bin index.html ABOUTUS) -->
        <section class="bg-light pt-16 pt-md-18 pt-lg-22 about-style1 overflow-visible">
            <div class="container">
                <div class="row align-items-xl-center">
                    <div class="col-lg-6 mb-1-9 mb-sm-2-2 mb-lg-0" v-reveal="{ effect: 'fade-right', delay: 100 }">
                        <div class="position-relative">
                            <div class="text-center text-sm-end text-md-center text-lg-end pe-xxl-1-9 overflow-hidden position-relative">
                                <img
                                    :src="nosotrosImagen"
                                    :alt="nosotros?.titulo || 'Nosotros'"
                                />
                                <span class="about-shape1"></span>
                                <span class="about-shape2"></span>
                            </div>
                            <img
                                :src="nosotrosImagenAlt"
                                alt="Greenpoint"
                                class="border-radius-10 position-absolute top-15 d-none d-sm-block"
                            />
                            <div class="bg-white text-center border-radius-10 p-1-9 d-inline-block position-absolute bottom-10 left-10">
                                <h4 class="h1 mb-0">
                                    <span>{{ cfg('anos_experiencia', '18') }}</span>+
                                </h4>
                                <span>Años de experiencia</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" v-reveal="{ effect: 'fade-left', delay: 200 }">
                        <div class="ps-xl-6">
                            <h2 class="h1 mb-4 font-weight-700">
                                Greenpoint
                                <span class="font-weight-400">internet de alta calidad</span>
                            </h2>
                            <p class="lead text-primary">
                                Facilitamos internet en lugares fijos y en movimiento.
                            </p>
                            <p class="mb-4">
                                El servicio de Internet puede alcanzar cualquier velocidad con
                                diseños de banda ancha personalizados.
                            </p>
                            <div
                                v-for="(item, index) in aboutChecks"
                                :key="item"
                                class="about-list"
                                :class="{
                                    active: index === 0,
                                    'mb-3': index < aboutChecks.length - 1,
                                }"
                            >
                                <div class="d-flex align-items-center">
                                    <i class="ti-check text-primary display-26"></i>
                                    <div class="ms-3">
                                        <h4 class="h6 mb-0">{{ item }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-1-9">
                                <div class="d-flex align-items-center">
                                    <div
                                        class="bg-img px-7 text-center py-3 cover-background border-radius-10 border-primary border border-width-3"
                                        :style="{ backgroundImage: `url('${aboutVideoThumb}')` }"
                                    >
                                        <div class="z-index-1 position-relative">
                                            <a
                                                class="text-primary"
                                                :href="videoUrl"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                aria-label="Ver video"
                                            >
                                                <i class="fas fa-play display-20 text-primary"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="ms-2 ms-md-5">
                                        <h4 class="mb-0 h5">Greenpoint</h4>
                                        <span class="small">Calidad e Innovación</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <img
                src="/images/demo/home/line-01.png"
                class="position-absolute top-n15 right-5 ani-top-bottom"
                alt=""
            />
        </section>

        <!-- Video CTA (cgi-bin COUNTER: nos6.jpg + overlay-dark 4; desktop padding 50px) -->
        <section
            class="bg-img cover-background gp-video-banner gp-overlay-dark-4"
            data-overlay-dark="4"
            :style="{ backgroundImage: `url('${videoBackground}')` }"
        >
            <div class="container">
                <div class="row align-items-center justify-content-center text-center">
                    <div class="col-lg-9 col-xl-8 col-xxl-6">
                        <h2 class="display-3 fw-bolder text-white mb-5 text-capitalize">
                            {{ cfg('home_cta_titulo', 'Greenpoint, internet satelital') }}
                        </h2>
                        <a
                            class="video_btn border-radius-5"
                            :href="videoUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Reproducir video"
                        >
                            <i class="fas fa-play"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Por qué elegirnos (cgi-bin WHY CHOOSE US): <section> plano → padding 50 / 90 (≤1199) / 70 (≤991) -->
        <section class="gp-why-section">
            <div class="container z-index-2 position-relative">
                <div class="section-heading" v-reveal>
                    <span class="subtitle">El mejor servicio</span>
                    <h2>
                        Internet Satelital
                        <span class="font-weight-400">para Empresas</span>
                    </h2>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row justify-content-center z-index-2 position-relative">
                    <div class="col-xxl-9">
                        <div class="row align-items-md-center">
                            <div class="col-sm-6 col-lg-4" v-reveal="{ effect: 'fade-right', delay: 100 }">
                                <div
                                    v-for="(item, index) in whyLeft"
                                    :key="item.title"
                                    class="d-flex"
                                    :class="whyLeftItemClass(index)"
                                >
                                    <div
                                        class="flex-grow-1 text-lg-end ms-4 ms-lg-0 me-lg-1-9 order-2 order-lg-1"
                                    >
                                        <h4 class="h5 mb-1 mb-md-3">{{ item.title }}</h4>
                                        <p class="mb-0">{{ item.text }}</p>
                                    </div>
                                    <div class="flex-shrink-0 position-relative order-1 order-lg-2">
                                        <div class="icon-box">
                                            <img :src="item.icon" :alt="item.title" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="col-lg-4 text-center d-none d-lg-block my-md-5 my-xl-0"
                                v-reveal="{ delay: 200 }"
                            >
                                <img
                                    src="/images/demo/home/why-choose.png"
                                    alt="Greenpoint"
                                    class="img-fluid"
                                />
                            </div>
                            <div class="col-sm-6 col-lg-4" v-reveal="{ effect: 'fade-left', delay: 300 }">
                                <div
                                    v-for="(item, index) in whyRight"
                                    :key="item.title"
                                    class="d-flex"
                                    :class="whyRightItemClass(index)"
                                >
                                    <div class="flex-shrink-0 position-relative">
                                        <div class="icon-box right">
                                            <img :src="item.icon" :alt="item.title" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-1-6 ms-md-1-9">
                                        <h4 class="h5 mb-1 mb-md-3">{{ item.title }}</h4>
                                        <p class="mb-0">{{ item.text }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <span class="why-choose-shape1"></span>
            <img
                src="/images/demo/home/line-02.png"
                class="position-absolute left-5 bottom-10 ani-top-bottom d-none d-sm-block"
                alt=""
            />
        </section>

        <!-- Oferta / soluciones flexibles (cgi-bin OFFER) -->
        <section class="py-0">
            <div class="container-fluid px-lg-0">
                <div class="row g-0 overlap-column">
                    <div class="col-lg-6" v-reveal="{ effect: 'fade' }">
                        <div
                            class="bg-dark py-6 py-lg-8 py-xl-10 py-xxl-13 px-1-9 px-xxl-9 border-radius-10 position-relative"
                        >
                            <div class="w-lg-80 mx-auto">
                                <h2 class="h1 font-weight-700 text-white mb-4">
                                    Soluciones Flexibles
                                    <span class="font-weight-400">para su Empresa</span>
                                </h2>
                                <p class="mb-1-9 text-white opacity8">
                                    {{
                                        cfg(
                                            'home_cta_texto',
                                            'Greenpoint cuenta hoy en dia con la tecnología para mantenerlo siempre conectado, ya sea a internet o incluso a su red corporativa, y con esto aprovechar las ventajas que tienes al estarlo. Poder realizar llamadas teléfonicas, videoconferencias, transferencias de cualquier tipo de datos, acceso a internet y los mas importante, desde cualquier sitio, sin importar si estas en pozo petrolero de Tabasco o en una mina en Chihuahua, o bien en un barco en el Golfo de México.',
                                        )
                                    }}
                                </p>
                                <div class="d-flex align-items-center mb-1-9">
                                    <div class="flex-shrink-0">
                                        <img src="/images/demo/icons/icon-01.png" alt="" />
                                    </div>
                                    <div class="flex-grow-1 ms-4">
                                        <h5 class="text-white">Greenpoint</h5>
                                        <span class="text-white">Máxima calidad de internet Satelital.</span>
                                    </div>
                                </div>
                                <!-- prod: class="butn small" — .butn.small no existe en el tema, aplica padding default -->
                                <Link :href="route('public.contacto')" class="butn small">
                                    Contacto
                                </Link>
                            </div>
                        </div>
                    </div>
                    <!-- ≤991: se oculta (en prod la col vacía colapsa); ≥992: overlap con top 3rem -->
                    <div
                        class="col-lg-6 d-none d-lg-block bg-img cover-background border-radius-10"
                        v-reveal="{ effect: 'fade', delay: 200 }"
                        :style="{ backgroundImage: `url('${offerBackground}')` }"
                    >
                        <span class="offer-shape1"></span>
                        <span class="offer-shape2"></span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features (cgi-bin PRICING / soluciones a su medida) -->
        <section class="pb-0">
            <div class="container">
                <div class="section-heading" v-reveal>
                    <span class="subtitle">{{ cfg('sitio_nombre', 'Greenpoint') }}</span>
                    <h2 class="w-100">Soluciones <span class="font-weight-400">a su medida</span></h2>
                </div>
                <div class="row mt-n1-9">
                    <div
                        v-for="(card, index) in featureCards"
                        :key="card.title"
                        class="col-sm-6 col-lg-3 mt-1-9"
                        v-reveal="{ delay: 100 + index * 80 }"
                    >
                        <div class="card-style7 card border-0 h-100">
                            <div class="card-body p-1-9 p-xl-5">
                                <div class="card-icon">
                                    <i :class="[card.icon, 'display-10', 'd-block', 'mb-4']"></i>
                                    <span class="round-shape"></span>
                                </div>
                                <h3 class="mb-0 h5">{{ card.title }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <br /><br />

        <!-- Galería (cgi-bin STREAMING) -->
        <section
            v-if="galleryItems.length"
            class="bg-img cover-background gp-gallery-home gp-full-bleed"
            data-overlay-dark="8"
            :style="{ backgroundImage: `url('${galleryBackground}')` }"
        >
            <div class="container-fluid px-7">
                <div class="section-heading white z-index-2" v-reveal>
                    <span class="subtitle white">{{ cfg('sitio_nombre', 'Greenpoint') }}</span>
                    <h2 class="text-white">
                        Galeria de Proyectos
                        <span class="font-weight-400">Realizados</span>
                    </h2>
                </div>
                <GalleryCarousel :items="galleryItems" />
            </div>
            <span class="gp-gallery-shape"></span>
        </section>

        <!-- Franja naranja (cgi-bin: bg-primary counter-style1) -->
        <section class="bg-primary counter-style1 gp-full-bleed">
            <div class="container position-relative z-index-9">
                <div class="row mt-n1-9">
                    <div class="col-sm-6 col-lg-3 mt-1-9">
                        <div class="d-flex"></div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mt-1-9">
                        <div class="d-flex"></div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mt-1-9">
                        <div class="d-flex"></div>
                    </div>
                    <div class="col-sm-6 col-lg-3 mt-1-9">
                        <div class="d-flex"></div>
                    </div>
                </div>
            </div>
            <span class="counter-shape1"></span>
            <span class="counter-shape2"></span>
            <span class="counter-shape3"></span>
        </section>
    </PublicLayout>
</template>
