<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import BannerCarousel from '@/Components/Public/BannerCarousel.vue';
import ServiceCard from '@/Components/Public/ServiceCard.vue';
import { usePublicImage } from '@/composables/usePublicImage';
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
    nosotros: {
        type: Object,
        default: null,
    },
    config: {
        type: Object,
        default: () => ({}),
    },
});

const { resolveImage } = usePublicImage();
const { getConfig } = usePublicSite();

const featuredServicios = computed(() => props.servicios.slice(0, 4));
const hasBanners = computed(() => props.banners.length > 0);

const cfg = (clave, fallback = '') => {
    const fromProp = props.config?.[clave];
    if (fromProp !== undefined && fromProp !== null && fromProp !== '') {
        return fromProp;
    }
    return getConfig(clave, fallback);
};

const nosotrosTitulo = computed(() => props.nosotros?.titulo || 'Quiénes Somos');
const nosotrosTexto = computed(
    () => props.nosotros?.texto_descriptivo || props.nosotros?.texto_adicional || '',
);
const nosotrosImagen = computed(() =>
    resolveImage(props.nosotros?.imagen_destacada) || '/images/demo/nosotros/destacada.jpg',
);
const ctaBackground = computed(() => {
    const banner = props.banners[1] || props.banners[0];
    return resolveImage(banner?.imagen) || '/images/demo/banners/banner2.jpg';
});
const serviciosIntroTitulo = computed(() => cfg('home_servicios_titulo', 'Nuestros Servicios'));
const serviciosIntroSubtitulo = computed(() => cfg('home_servicios_subtitulo', ''));
const ctaTitulo = computed(() => cfg('home_cta_titulo', '¿Listo para dar el siguiente paso?'));
const ctaTexto = computed(() => cfg('home_cta_texto', ''));
</script>

<template>
    <Head title="Inicio" />

    <PublicLayout>
        <BannerCarousel v-if="hasBanners" :banners="banners" />

        <section v-else class="gp-hero">
            <div class="container">
                <span class="gp-eyebrow mb-3" style="color: #fff !important; border-color: #fff;">
                    {{ cfg('sitio_nombre', 'GreenPoint') }}
                </span>
                <h1 class="mb-3">{{ cfg('sitio_nombre', 'GreenPoint') }}</h1>
                <p v-if="cfg('empresa_descripcion')" class="gp-hero-subtitle mb-4">
                    {{ cfg('empresa_descripcion') }}
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <Link :href="route('public.nosotros')" class="butn">
                        <span>Leer Más</span>
                    </Link>
                    <Link :href="route('public.contacto')" class="butn secondary">Contacto</Link>
                </div>
            </div>
        </section>

        <section class="p-0 overflow-visible">
            <div class="container">
                <div class="service-style1">
                    <div class="row g-0 align-items-center">
                        <div class="col-lg-4 mb-4 mb-lg-0">
                            <div class="px-4 px-lg-5 py-4 py-lg-5">
                                <div class="section-heading text-start mb-0">
                                    <span class="subtitle">{{ cfg('sitio_nombre', 'Greenpoint') }}</span>
                                    <h2 class="mb-0 w-100">
                                        {{ serviciosIntroTitulo }}
                                        <span v-if="serviciosIntroSubtitulo" class="font-weight-400">
                                            {{ serviciosIntroSubtitulo }}
                                        </span>
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div v-if="featuredServicios.length" class="row g-0">
                                <div
                                    v-for="(servicio, index) in featuredServicios"
                                    :key="servicio.id"
                                    class="col-md-6"
                                >
                                    <div
                                        class="card border-0 card-style1 h-100"
                                        :class="{ active: index === 0 }"
                                    >
                                        <div class="card-body">
                                            <div class="card-icon">
                                                <i class="fa-solid fa-satellite-dish"></i>
                                            </div>
                                            <h3 class="h5 fw-bold mb-3">{{ servicio.nombre }}</h3>
                                            <p class="mb-3 text-muted">
                                                {{
                                                    (servicio.descripcion || '').length > 110
                                                        ? `${servicio.descripcion.slice(0, 110)}…`
                                                        : servicio.descripcion
                                                }}
                                            </p>
                                            <Link
                                                :href="route('public.servicios.show', servicio.id)"
                                                class="fw-semibold text-gp-primary"
                                            >
                                                Ver más <i class="fa-solid fa-arrow-right-long ms-1 small"></i>
                                            </Link>
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
        </section>

        <section v-if="nosotros" class="gp-section gp-section-alt">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <div class="section-heading text-start mb-4">
                            <span class="subtitle">{{ nosotros.subtitulo || cfg('sitio_nombre', 'Greenpoint') }}</span>
                            <h2 class="w-100">{{ nosotrosTitulo }}</h2>
                        </div>
                        <p
                            v-if="nosotrosTexto"
                            class="mb-4"
                            style="white-space: pre-line; text-align: justify;"
                        >
                            {{ nosotrosTexto }}
                        </p>
                        <Link :href="route('public.nosotros')" class="butn">
                            <span>Conocer más</span>
                        </Link>
                    </div>
                    <div class="col-lg-6">
                        <img
                            :src="nosotrosImagen"
                            :alt="nosotrosTitulo"
                            class="img-fluid border-radius-10 w-100"
                            style="object-fit: cover; max-height: 420px;"
                        />
                    </div>
                </div>
            </div>
        </section>

        <section
            class="gp-video-cta"
            :style="{ backgroundImage: `url('${ctaBackground}')` }"
        >
            <div class="container text-center">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <h2 class="display-5 fw-bolder text-capitalize mb-4">
                            {{ ctaTitulo }}
                        </h2>
                        <Link :href="route('public.contacto')" class="butn secondary">
                            Contáctanos
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <section class="gp-section">
            <div class="container">
                <div class="section-heading">
                    <span class="subtitle">{{ cfg('sitio_nombre', 'Greenpoint') }}</span>
                    <h2>Nuestros <span class="font-weight-400">Servicios</span></h2>
                </div>

                <div v-if="servicios.length" class="row g-4">
                    <div
                        v-for="servicio in servicios.slice(0, 6)"
                        :key="`grid-${servicio.id}`"
                        class="col-md-6 col-lg-4"
                    >
                        <ServiceCard :servicio="servicio" />
                    </div>
                </div>

                <div v-if="servicios.length > 6" class="text-center mt-5">
                    <Link :href="route('public.servicios.index')" class="butn">
                        <span>Ver todos los servicios</span>
                    </Link>
                </div>
            </div>
        </section>

        <section class="gp-section gp-section-alt">
            <div class="container">
                <div class="gp-cta text-center">
                    <h2 class="mb-3">{{ ctaTitulo }}</h2>
                    <p
                        v-if="ctaTexto"
                        class="mb-4 mx-auto text-white opacity9"
                        style="max-width: 40rem;"
                    >
                        {{ ctaTexto }}
                    </p>
                    <Link :href="route('public.contacto')" class="butn secondary">
                        Solicitar información
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
