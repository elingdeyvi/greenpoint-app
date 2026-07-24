<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import { usePublicImage } from '@/composables/usePublicImage';

const { resolveImage } = usePublicImage();

const props = defineProps({
    pagina: {
        type: Object,
        default: null,
    },
});

const featuredImage = computed(() =>
    props.pagina?.imagen_destacada ? resolveImage(props.pagina.imagen_destacada) : null,
);
const imagenes = computed(() => props.pagina?.imagenes ?? []);
const progreso = computed(() => props.pagina?.progreso ?? []);
const heroBackground = computed(
    () => featuredImage.value || resolveImage(imagenes.value[0]?.ruta_imagen) || '/images/demo/banners/banner1.jpg',
);
</script>

<template>
    <Head :title="pagina?.titulo || 'Nosotros'" />

    <PublicLayout>
        <template v-if="pagina">
            <PageHero
                :title="pagina.titulo || 'Quiénes Somos'"
                :breadcrumbs="[
                    { label: 'Inicio', href: route('public.home') },
                    { label: 'Nosotros' },
                    { label: pagina.titulo || 'Quiénes Somos' },
                ]"
                :background="heroBackground"
            />

            <section class="gp-section about-style02 position-relative">
                <div class="container">
                    <div class="row align-items-center g-5">
                        <div class="col-lg-7">
                            <div class="row g-3">
                                <div class="col-sm-5 d-none d-sm-block">
                                    <img
                                        v-if="imagenes[0]"
                                        :src="resolveImage(imagenes[0].ruta_imagen)"
                                        alt=""
                                        class="img-fluid border-radius-10 w-100 mb-3"
                                        style="height: 220px; object-fit: cover;"
                                    />
                                    <div
                                        v-else
                                        class="border-radius-10 mb-3 bg-gp-soft"
                                        style="height: 220px;"
                                    ></div>
                                    <div class="w-100 mb-2 bg-gp-primary" style="height: 6px; border-radius: 4px;"></div>
                                    <div class="w-50 bg-gp-primary" style="height: 6px; border-radius: 4px;"></div>
                                </div>
                                <div class="col-sm-7">
                                    <img
                                        v-if="featuredImage"
                                        :src="featuredImage"
                                        :alt="pagina.titulo"
                                        class="img-fluid border-radius-10 w-100"
                                        style="min-height: 320px; object-fit: cover;"
                                    />
                                    <div
                                        v-else
                                        class="border-radius-10 w-100 bg-gp-soft d-flex align-items-center justify-content-center"
                                        style="min-height: 320px;"
                                    >
                                        <i class="fa-solid fa-users fs-1 text-gp-primary opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="section-heading text-start mb-4">
                                <span class="subtitle text-gp-secondary">Greenpoint</span>
                                <h2 class="mb-0 w-100 h1">{{ pagina.titulo || 'Quiénes Somos' }}</h2>
                            </div>
                            <p
                                v-if="pagina.texto_descriptivo"
                                class="mb-4"
                                style="white-space: pre-line; text-align: justify;"
                            >
                                {{ pagina.texto_descriptivo }}
                            </p>
                            <p
                                v-if="pagina.texto_adicional"
                                class="mb-4 text-muted"
                                style="white-space: pre-line; text-align: justify;"
                            >
                                {{ pagina.texto_adicional }}
                            </p>
                            <Link :href="route('public.contacto')" class="btn btn-outline-secondary">
                                Contactar
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <section
                v-if="imagenes.length"
                class="gp-video-cta"
                :style="{
                    backgroundImage: `url('${resolveImage(imagenes[1]?.ruta_imagen || imagenes[0].ruta_imagen)}')`,
                }"
            >
                <div class="container text-center">
                    <h2 class="display-5 fw-bolder text-capitalize mb-0">
                        Greenpoint: Servicios Satelitales
                    </h2>
                </div>
            </section>

            <section v-if="progreso.length" class="gp-section gp-section-alt">
                <div class="container">
                    <div class="row align-items-center g-5">
                        <div class="col-lg-6">
                            <div class="section-heading text-start mb-4">
                                <span class="subtitle">Greenpoint</span>
                                <h2 class="w-100">Nuestras <span class="font-weight-400">Fortalezas</span></h2>
                            </div>
                            <div
                                v-for="item in progreso"
                                :key="item.id"
                                class="gp-progress-item"
                            >
                                <div class="gp-progress-label">
                                    <span>{{ item.titulo }}</span>
                                    <span class="gp-progress-pct">{{ item.porcentaje }}%</span>
                                </div>
                                <div class="gp-progress-track">
                                    <div
                                        class="gp-progress-bar"
                                        :style="{ width: `${item.porcentaje}%` }"
                                    ></div>
                                </div>
                                <p v-if="item.descripcion" class="text-muted small mt-2 mb-0">
                                    {{ item.descripcion }}
                                </p>
                            </div>
                        </div>
                        <div v-if="imagenes[2] || featuredImage" class="col-lg-6">
                            <img
                                :src="resolveImage(imagenes[2]?.ruta_imagen) || featuredImage"
                                alt=""
                                class="img-fluid border-radius-10 w-100"
                                style="max-height: 420px; object-fit: cover;"
                            />
                        </div>
                    </div>
                </div>
            </section>
        </template>

        <section v-else class="gp-section text-center">
            <div class="container">
                <i class="fa-solid fa-users fs-1 text-gp-primary mb-3"></i>
                <h1 class="h3">Contenido no disponible</h1>
                <p class="text-muted mb-0">
                    La información de esta sección aún no ha sido publicada.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
