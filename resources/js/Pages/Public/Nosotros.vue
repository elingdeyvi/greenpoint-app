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

const featuredImage = computed(
    () =>
        resolveImage(props.pagina?.imagen_destacada) ||
        '/images/demo/nosotros/nos10.jpg',
);
const imagenes = computed(() => props.pagina?.imagenes ?? []);
const progreso = computed(() => props.pagina?.progreso ?? []);
const sideImage = computed(
    () =>
        resolveImage(imagenes.value[0]?.ruta_imagen) ||
        '/images/demo/nosotros/nos11.jpg',
);
const videoBg = computed(
    () =>
        resolveImage(imagenes.value[1]?.ruta_imagen) ||
        '/images/demo/home/nos6.jpg',
);
const fortalezasImage = computed(
    () =>
        resolveImage(imagenes.value[2]?.ruta_imagen) ||
        '/images/demo/nosotros/con4.jpg',
);
const videoUrl = computed(
    () => props.pagina?.url_video || 'https://www.youtube.com/watch?v=yd1JhZzoS6A',
);
const pageTitle = computed(() => props.pagina?.titulo || 'Quienes Somos');
</script>

<template>
    <Head :title="pageTitle" />

    <PublicLayout>
        <template v-if="pagina">
            <PageHero
                :title="pageTitle"
                :breadcrumbs="[
                    { label: 'Inicio', href: route('public.home') },
                    { label: 'Nosotros' },
                    { label: pageTitle },
                ]"
                background="/images/demo/page-title/con1.jpg"
            />

            <!-- ABOUTUS — cgi-bin nosotros.html about-style02 -->
            <section class="about-style02 position-relative">
                <div class="container px-xl-10">
                    <div class="row align-items-center mt-n2-6">
                        <div class="col-lg-7 mt-2-6" v-reveal="{ delay: 100 }">
                            <div class="pe-lg-4 pe-xl-5">
                                <div class="row">
                                    <div class="col-sm-5 d-none d-sm-block">
                                        <div class="mb-1-9">
                                            <img
                                                :src="sideImage"
                                                alt=""
                                                class="border-radius-10 w-100"
                                            />
                                        </div>
                                        <div class="about-line-shape w-100 mb-3 bg-primary"></div>
                                        <div class="about-line-shape w-50 bg-primary"></div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="text-center">
                                            <img
                                                :src="featuredImage"
                                                :alt="pageTitle"
                                                class="border-radius-10 w-100"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 mt-2-6" v-reveal="{ delay: 200 }">
                            <div class="mb-1-9 title-style">
                                <div class="position-relative d-inline-block title-shape">
                                    <div class="shape1 bg-primary position-absolute border-radius-10"></div>
                                    <div class="shape2 bg-primary position-absolute border-radius-10"></div>
                                </div>
                                <span class="text-secondary font-weight-600 ms-3">{{
                                    pagina.subtitulo || 'Greenpoint'
                                }}</span>
                                <h2 class="mb-0 h1">{{ pageTitle }}</h2>
                            </div>
                            <p
                                v-if="pagina.texto_descriptivo"
                                class="mb-1-9"
                                style="text-align: justify; white-space: pre-line"
                            >
                                {{ pagina.texto_descriptivo }}
                            </p>
                            <p
                                v-if="pagina.texto_adicional"
                                class="mb-1-9"
                                style="text-align: justify; white-space: pre-line"
                            >
                                {{ pagina.texto_adicional }}
                            </p>
                            <Link :href="route('public.contacto')" class="btn btn-outline-secondary">
                                Contactar
                            </Link>
                        </div>
                    </div>
                </div>
                <img
                    src="/images/demo/content/shape-01.png"
                    class="position-absolute left-5 bottom-10 ani-top-bottom d-none d-lg-block"
                    alt=""
                />
                <div
                    class="d-inline-block p-2 bg-primary rounded-circle position-absolute right-5 top-5 ani-move"
                ></div>
                <div
                    class="d-inline-block px-5 py-6 border border-primary position-absolute right-5 top-5 border-radius-10 ani-move"
                ></div>
            </section>

            <!-- Video band -->
            <section
                class="bg-img cover-background gp-overlay-dark-4 gp-inner-video"
                :style="{ backgroundImage: `url('${videoBg}')` }"
            >
                <div class="container">
                    <div class="row align-items-center justify-content-center text-center">
                        <div class="col-lg-9 col-xl-8 col-xxl-6">
                            <h2 class="display-3 fw-bolder text-white mb-5 text-capitalize">
                                Greenpoint: Servicios Satelitales
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

            <!-- Fortalezas / progress -->
            <section v-if="progreso.length" class="bg-light md position-relative">
                <div class="container z-index-3 position-relative">
                    <div class="row align-items-center">
                        <div class="col-lg-6" v-reveal="{ delay: 100 }">
                            <div class="pe-lg-5">
                                <div class="section-heading text-start">
                                    <span class="subtitle">Experiencia y calidad en servicios</span>
                                    <h2 class="mb-0 w-100">
                                        El mejor internet
                                        <span class="font-weight-400">para su empresa</span>
                                    </h2>
                                </div>
                                <div v-for="(item, index) in progreso" :key="item.id" class="mb-1-9">
                                    <div class="progress-text">
                                        <div class="row mb-2">
                                            <div class="col-6">{{ item.titulo }}</div>
                                            <div class="col-6 text-end">{{ item.porcentaje }}%</div>
                                        </div>
                                    </div>
                                    <div
                                        class="custom-progress progress progress-medium border-radius-10"
                                        :class="{ 'mb-0': index === progreso.length - 1 }"
                                        style="height: 8px"
                                    >
                                        <div
                                            class="custom-bar progress-bar bg-primary"
                                            :style="{ width: `${item.porcentaje}%` }"
                                            role="progressbar"
                                            :aria-valuenow="item.porcentaje"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 d-none d-lg-block" v-reveal="{ delay: 200 }">
                            <div class="vw-lg-50 position-relative">
                                <img
                                    :src="fortalezasImage"
                                    class="rounded-bottom-left-10px w-100"
                                    alt=""
                                />
                                <div
                                    class="d-inline-block position-absolute z-index-1 bottom-0 start-0 bg-secondary p-2-3 rounded-bottom-left-10px rounded-top-right-10px"
                                >
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-users display-10 text-white"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-4">
                                            <p class="text-white lead mb-0">
                                                Somos la mejor opción<br />
                                                Experiencia e innovación
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="d-inline-block p-2 border-secondary border border-width-2 position-absolute left-5 bottom-25 ani-left-right"
                ></div>
                <div
                    class="d-inline-block p-2 bg-secondary rounded-circle position-absolute left-10 top-25 ani-move"
                ></div>
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
