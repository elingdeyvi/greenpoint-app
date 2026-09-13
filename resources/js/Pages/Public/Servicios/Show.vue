<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
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

/** Features/badges opcionales embebidos en descripcion con marcadores, o defaults prod */
const parseBlocks = (descripcion = '') => {
    const featuresMatch = descripcion.match(/\[features\]([\s\S]*?)\[\/features\]/i);
    const badgesMatch = descripcion.match(/\[badges\]([\s\S]*?)\[\/badges\]/i);
    let body = descripcion
        .replace(/\[features\][\s\S]*?\[\/features\]/gi, '')
        .replace(/\[badges\][\s\S]*?\[\/badges\]/gi, '')
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

    return { body, features, badges };
};

const blocks = computed(() => parseBlocks(props.servicio.descripcion || ''));
const imageUrl = computed(
    () => resolveImage(props.servicio.imagen) || '/images/demo/servicios/tec2.jpg',
);
const heroBg = '/images/demo/page-title/con3.jpg';
</script>

<template>
    <Head :title="servicio.nombre" />

    <PublicLayout>
        <PageHero
            :title="servicio.nombre"
            :breadcrumbs="[
                { label: 'Inicio', href: route('public.home') },
                { label: 'Servicios', href: route('public.servicios.index') },
                { label: servicio.nombre },
            ]"
            :background="heroBg"
        />

        <!-- cgi-bin conexion_satelital.html -->
        <section>
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
                                            <h3 class="h4 mb-3">{{ servicio.nombre }}</h3>
                                            <p
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

                                    <div class="mt-1-9">
                                        <Link :href="route('public.contacto')" class="butn">
                                            Contacto
                                        </Link>
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
                                    backgroundImage: `url('/images/demo/servicios/tec3.jpg')`,
                                }"
                            >
                                <div class="position-relative z-index-9 p-1-9 p-md-5">
                                    <br /><br /><br /><br /><br /><br /><br />
                                </div>
                            </div>
                            <br /><br />
                            <div class="widget" v-reveal="{ delay: 250 }">
                                <h6 class="widget-title">
                                    <span class="me-2 text-primary">|</span>Redes Sociales
                                </h6>
                                <div class="widget-body">
                                    <ul class="blog-social-icon">
                                        <li
                                            v-for="red in redesSociales"
                                            :key="red.id"
                                        >
                                            <a
                                                :href="red.url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                <i :class="red.icono || 'fab fa-facebook-f'"></i>
                                            </a>
                                        </li>
                                        <template v-if="!redesSociales?.length">
                                            <li>
                                                <a href="#!"><i class="fab fa-facebook-f"></i></a>
                                            </li>
                                            <li>
                                                <a href="#!"><i class="fab fa-twitter"></i></a>
                                            </li>
                                            <li>
                                                <a href="#!"><i class="fab fa-youtube"></i></a>
                                            </li>
                                            <li>
                                                <a href="#!"><i class="fab fa-linkedin-in"></i></a>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
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
