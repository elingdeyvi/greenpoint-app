<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import { usePublicImage } from '@/composables/usePublicImage';
import { usePublicSite } from '@/composables/usePublicSite';

const { resolveImage } = usePublicImage();
const { telefonoPrincipal, emailPrincipal, direccionMatriz, redesSociales } = usePublicSite();

const props = defineProps({
    pagina: {
        type: Object,
        default: null,
    },
});

const eventos = computed(() => props.pagina?.eventos ?? []);
const pageTitle = computed(() => props.pagina?.titulo || 'Historia');
const cvUrl = computed(() => (props.pagina?.cv_pdf ? route('public.cv') : null));
const cvLabel = computed(() => props.pagina?.cv_etiqueta || 'Servicios Greenpoint');
const infoImage = '/images/demo/historia/h1.jpg';
const avatarImage = '/images/demo/historia/h2.jpg';
const direccion = computed(
    () => direccionMatriz.value || 'Tabasco, Cd. del Carmen, Veracruz',
);
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
                    { label: 'Historia' },
                ]"
                background="/images/demo/page-title/con1.jpg"
            />

            <section>
                <div class="container">
                    <div class="row">
                        <!-- Sidebar — cgi-bin historia.html -->
                        <div class="col-lg-4 order-2 order-lg-1">
                            <div class="service-details-sidebar">
                                <aside class="widget widget-address" v-reveal="{ delay: 100 }">
                                    <h4 class="widget-title">
                                        <span class="me-2 text-primary">|</span>Info de Contacto
                                    </h4>
                                    <div v-if="emailPrincipal" class="d-flex align-items-center mb-4">
                                        <div class="flex-shrink-0">
                                            <div class="contact-icon">
                                                <i class="far fa-envelope"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">Email</h6>
                                            <p class="mb-0">{{ emailPrincipal }}</p>
                                        </div>
                                    </div>
                                    <div v-if="telefonoPrincipal" class="d-flex align-items-center mb-4">
                                        <div class="flex-shrink-0">
                                            <div class="contact-icon">
                                                <i class="fas fa-mobile-alt"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">Teléfono</h6>
                                            <p class="mb-0">{{ telefonoPrincipal }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="flex-shrink-0">
                                            <div class="contact-icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">Dirección</h6>
                                            <p class="mb-0">{{ direccion }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="contact-icon">
                                                <i class="far fa-clock"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">Horarios</h6>
                                            <p class="mb-0">Lun a Vie - 9:00am a 6:00pm</p>
                                        </div>
                                    </div>
                                </aside>

                                <aside
                                    v-if="cvUrl"
                                    class="widget widget-brochure"
                                    v-reveal="{ delay: 150 }"
                                >
                                    <h4 class="widget-title">
                                        <span class="me-2 text-primary">|</span>Curriculum
                                    </h4>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-3">
                                            <a :href="cvUrl" target="_blank" rel="noopener noreferrer">
                                                <i class="far fa-file-pdf display-26 me-3"></i>
                                                {{ cvLabel }}
                                                <span>PDF</span>
                                            </a>
                                        </li>
                                    </ul>
                                </aside>

                                <aside
                                    class="bg-img cover-background border-radius-10 widget-info gp-overlay-dark-7"
                                    v-reveal="{ delay: 200 }"
                                    :style="{ backgroundImage: `url('${infoImage}')` }"
                                >
                                    <div class="position-relative z-index-9 text-center py-4 py-md-5">
                                        <img
                                            class="border-radius-50 mb-4"
                                            :src="avatarImage"
                                            alt=""
                                            width="80"
                                            height="80"
                                        />
                                        <h5 class="text-white mb-3">Alguna duda?</h5>
                                        <ul class="text-center list-unstyled mb-4">
                                            <li v-if="telefonoPrincipal" class="text-white mb-2">
                                                <i class="fa fa-phone-alt small text-white me-2"></i>
                                                <a
                                                    :href="`tel:${telefonoPrincipal.replace(/[^\d+]/g, '')}`"
                                                    class="text-white"
                                                >
                                                    {{ telefonoPrincipal }}
                                                </a>
                                            </li>
                                            <li v-if="emailPrincipal" class="text-white">
                                                <i class="fa fa-envelope-open small text-white me-2"></i>
                                                <a :href="`mailto:${emailPrincipal}`" class="text-white">
                                                    {{ emailPrincipal }}
                                                </a>
                                            </li>
                                        </ul>
                                        <ul v-if="redesSociales?.length" class="social-icons">
                                            <li v-for="red in redesSociales" :key="red.id">
                                                <a :href="red.url" target="_blank" rel="noopener noreferrer">
                                                    <i :class="red.icono || 'fab fa-facebook-f'"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </aside>
                            </div>
                        </div>

                        <!-- Main — años en prosa como prod -->
                        <div class="col-lg-8 order-1 order-lg-2 mb-2-6 mb-lg-0">
                            <div class="ps-lg-1-6">
                                <div class="row mb-2-2" v-reveal="{ delay: 100 }">
                                    <div class="col-lg-12">
                                        <h3 class="h4 mb-3">Historia Greenpoint</h3>
                                        <p
                                            v-if="eventos.length"
                                            class="w-95 mb-2-2"
                                            style="text-align: justify"
                                        >
                                            <template v-for="(evento, index) in eventos" :key="evento.id">
                                                <strong>{{ evento.anio }}:</strong>
                                                {{ evento.descripcion || evento.titulo }}
                                                <br v-if="index < eventos.length - 1" />
                                            </template>
                                        </p>
                                        <p v-else class="text-muted mb-0">
                                            Muy pronto compartiremos los hitos de nuestra historia.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>

        <section v-else class="gp-section text-center">
            <div class="container">
                <i class="fa-solid fa-clock-rotate-left fs-1 text-gp-primary mb-3"></i>
                <h1 class="h3">Contenido no disponible</h1>
                <p class="text-muted mb-0">
                    La información de esta sección aún no ha sido publicada.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
