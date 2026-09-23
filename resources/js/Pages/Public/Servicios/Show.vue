<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import { usePublicImage } from '@/composables/usePublicImage';
import { usePublicSite } from '@/composables/usePublicSite';

const { resolveImage } = usePublicImage();
const { redesSociales } = usePublicSite();

const props = defineProps({
    servicio: {
        type: Object,
        required: true,
    },
});

/** Features/badges/kit opcionales embebidos en descripcion */
const parseBlocks = (descripcion = '') => {
    const featuresMatch = descripcion.match(/\[features\]([\s\S]*?)\[\/features\]/i);
    const badgesMatch = descripcion.match(/\[badges\]([\s\S]*?)\[\/badges\]/i);
    const kitMatch = descripcion.match(/\[kit\]([\s\S]*?)\[\/kit\]/i);
    let body = descripcion
        .replace(/\[features\][\s\S]*?\[\/features\]/gi, '')
        .replace(/\[badges\][\s\S]*?\[\/badges\]/gi, '')
        .replace(/\[kit\][\s\S]*?\[\/kit\]/gi, '')
        .trim();

    const features = featuresMatch
        ? featuresMatch[1]
              .split('\n')
              .map((l) => l.replace(/^[-*]\s*/, '').trim())
              .filter(Boolean)
        : [];
    const badges = badgesMatch
        ? badgesMatch[1]
              .split('\n')
              .map((l) => l.replace(/^[-*]\s*/, '').trim())
              .filter(Boolean)
        : [];
    const kit = kitMatch ? kitMatch[1].trim() : null;

    return { body, features, badges, kit };
};

const blocks = computed(() => parseBlocks(props.servicio.descripcion || ''));
const plantilla = computed(() => props.servicio.plantilla || 'detalle');
const isAboutLayout = computed(() => plantilla.value === 'about');
const isSplitLayout = computed(() => plantilla.value === 'split');
const imageUrl = computed(
    () => resolveImage(props.servicio.imagen) || '/images/demo/servicios/tec2.jpg',
);
const secondaryImageUrl = computed(() => {
    const custom = resolveImage(props.servicio.imagen_secundaria);
    if (custom) {
        return custom;
    }
    return isAboutLayout.value
        ? '/images/demo/servicios/s2.jpg'
        : '/images/demo/servicios/tec3.jpg';
});
const decoLine = '/images/demo/content/line-02.png';
const heroBg = '/images/demo/page-title/con3.jpg';

const sectionHeading = computed(() => {
    const text = props.servicio.titulo_seccion || props.servicio.nombre || '';
    const idx = text.indexOf(' ');
    if (idx === -1) {
        return { main: text, rest: '' };
    }
    return { main: text.slice(0, idx), rest: text.slice(idx) };
});

const breadcrumbs = computed(() => {
    if (isAboutLayout.value) {
        return [
            { label: 'Inicio', href: route('public.home') },
            { label: 'Nosotros' },
            { label: props.servicio.nombre },
        ];
    }
    return [
        { label: 'Inicio', href: route('public.home') },
        { label: 'Servicios', href: route('public.servicios.index') },
        {
            label: isSplitLayout.value
                ? 'Hardware'
                : props.servicio.nombre,
        },
    ];
});
</script>

<template>
    <Head :title="servicio.nombre" />

    <PublicLayout>
        <PageHero
            :title="servicio.nombre"
            :breadcrumbs="breadcrumbs"
            :background="heroBg"
        />

        <!-- cgi-bin soluciones.html — about-style1 -->
        <section v-if="isAboutLayout" class="about-style1">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mb-1-9 mb-sm-2-2 mb-lg-0">
                        <!-- cgi-bin soluciones.html — mismo markup que producción -->
                        <div class="position-relative h-100 text-center text-sm-start">
                            <img
                                :src="imageUrl"
                                class="border-radius-10 mb-sm-10 mb-lg-0"
                                :alt="servicio.nombre"
                            />
                            <img
                                :src="secondaryImageUrl"
                                class="position-absolute border-radius-10 bottom-0 end-0 d-none d-sm-block"
                                alt=""
                            />
                            <img
                                :src="decoLine"
                                class="position-absolute right-25 top-10 ani-top-bottom d-none d-sm-block"
                                alt=""
                            />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="ps-xl-6">
                            <h2 class="h1 mb-4 font-weight-700">
                                {{ sectionHeading.main }}
                                <span v-if="sectionHeading.rest" class="font-weight-400">{{
                                    sectionHeading.rest
                                }}</span>
                            </h2>
                            <p v-if="servicio.subtitulo" class="lead text-primary">
                                {{ servicio.subtitulo }}
                            </p>
                            <div
                                v-for="(item, index) in blocks.features"
                                :key="`about-${index}`"
                                class="about-list"
                                :class="{
                                    active: index === 0,
                                    'mb-3': index === 0,
                                }"
                            >
                                <div class="d-flex align-items-center">
                                    <i class="ti-check text-primary display-26"></i>
                                    <div class="ms-3">
                                        <h4 class="h6 mb-0">{{ item }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="d-inline-block px-5 py-6 border border-primary position-absolute right-5 top-5 border-radius-10 ani-move d-none d-lg-inline-block"
            ></div>
        </section>

        <!-- cgi-bin hardware.html — texto + imagen -->
        <section v-else-if="isSplitLayout">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 mb-2-5 mb-lg-0" v-reveal="{ delay: 100 }">
                        <h2 class="h1 font-weight-700 mb-4">
                            {{ sectionHeading.main }}
                            <span v-if="sectionHeading.rest" class="font-weight-400">{{
                                sectionHeading.rest
                            }}</span>
                        </h2>
                        <p
                            v-if="blocks.body"
                            class="mb-4"
                            style="text-align: justify; white-space: pre-line"
                        >
                            {{ blocks.body }}
                        </p>
                        <p v-if="blocks.kit" class="mb-4">
                            <strong>{{ blocks.kit }}</strong>
                        </p>
                        <div v-if="blocks.features.length" class="row mb-1-9">
                            <div class="col-lg-12">
                                <ul class="list-style1 mb-0">
                                    <li
                                        v-for="(item, i) in blocks.features"
                                        :key="`split-${i}`"
                                    >
                                        <i
                                            class="ti-check text-primary me-3 font-weight-600"
                                        ></i>
                                        {{ item }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" v-reveal="{ delay: 200 }">
                        <div class="ps-lg-2-5 ps-xl-7">
                            <div
                                class="bg-img cover-background p-1-9 py-sm-2-9 px-sm-2-5 py-md-8 px-md-6 border-radius-10 gp-servicio-split-media"
                                :style="{ backgroundImage: `url('${imageUrl}')` }"
                            >
                                <div class="position-relative z-index-9">
                                    <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- cgi-bin conexion_satelital.html — detalle -->
        <section v-else>
            <div class="container">
                <div class="row mb-2-5">
                    <div class="col-lg-12" v-reveal="{ delay: 100 }">
                        <img
                            :src="imageUrl"
                            class="border-radius-10 w-100"
                            :alt="servicio.nombre"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 mb-2-9 mb-lg-0">
                        <div class="pe-lg-1-6">
                            <article class="card card-style5" v-reveal="{ delay: 150 }">
                                <div class="card-body">
                                    <div class="row mb-1-9">
                                        <div class="col-md-12">
                                            <h3 class="h4 mb-3">
                                                {{
                                                    servicio.titulo_seccion || servicio.nombre
                                                }}
                                            </h3>
                                            <p
                                                v-if="blocks.body"
                                                class="w-95 mb-0"
                                                style="text-align: justify; white-space: pre-line"
                                            >
                                                {{ blocks.body }}
                                            </p>
                                        </div>
                                    </div>

                                    <div v-if="blocks.features.length" class="row mb-1-9">
                                        <div class="col-lg-12">
                                            <ul class="list-style1 mb-0">
                                                <li
                                                    v-for="(item, i) in blocks.features"
                                                    :key="`f-${i}`"
                                                >
                                                    <i
                                                        class="ti-check text-primary me-3 font-weight-600"
                                                    ></i>
                                                    {{ item }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div v-if="blocks.badges.length">
                                        <ul class="list-style1 mb-0">
                                            <li
                                                v-for="(badge, i) in blocks.badges"
                                                :key="`b-${i}`"
                                            >
                                                <i
                                                    class="ti-check text-primary me-3 font-weight-600"
                                                ></i>
                                                <span class="text-white bg-primary px-2 py-1">{{
                                                    badge
                                                }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="blog-sidebar">
                            <div
                                class="bg-img cover-background secondary-overlay border-radius-10"
                                v-reveal="{ delay: 200 }"
                                :style="{
                                    backgroundImage: `url('${secondaryImageUrl}')`,
                                }"
                            >
                                <div class="position-relative z-index-9 p-1-9 p-md-5">
                                    <br /><br /><br /><br /><br /><br /><br />
                                </div>
                            </div>
                            <br /><br />
                            <!-- cgi-bin conexion_satelital.html — redes entre imágenes -->
                            <aside
                                v-if="redesSociales.length"
                                class="widget"
                                v-reveal="{ delay: 250 }"
                            >
                                <span class="me-2 text-primary">|</span>Redes Sociales
                                <div class="widget-body mt-3">
                                    <ul class="blog-social-icon">
                                        <li
                                            v-for="red in redesSociales"
                                            :key="red.id"
                                        >
                                            <a
                                                :href="red.url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                :aria-label="red.nombre"
                                            >
                                                <i
                                                    :class="red.icono || 'fab fa-facebook-f'"
                                                ></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </aside>
                            <div
                                class="bg-img cover-background secondary-overlay border-radius-10"
                                v-reveal="{ delay: 300 }"
                                :style="{
                                    backgroundImage: `url('/images/demo/servicios/tec4.jpg')`,
                                }"
                            >
                                <div class="position-relative z-index-9 p-1-9 p-md-5">
                                    <br /><br /><br /><br /><br /><br /><br />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
