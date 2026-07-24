<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import { usePublicImage } from '@/composables/usePublicImage';
import { usePublicSite } from '@/composables/usePublicSite';

const { resolveImage } = usePublicImage();
const { telefonoPrincipal, emailPrincipal } = usePublicSite();

const props = defineProps({
    pagina: {
        type: Object,
        default: null,
    },
});

const eventos = computed(() => props.pagina?.eventos ?? []);
const imagenes = computed(() => props.pagina?.imagenes ?? []);
const heroBackground = computed(
    () => resolveImage(imagenes.value[0]?.ruta_imagen) || '/images/demo/historia/historia1.jpg',
);
</script>

<template>
    <Head :title="pagina?.titulo || 'Nuestra historia'" />

    <PublicLayout>
        <template v-if="pagina">
            <PageHero
                :title="pagina.titulo || 'Historia'"
                :breadcrumbs="[
                    { label: 'Inicio', href: route('public.home') },
                    { label: 'Nosotros' },
                    { label: 'Historia' },
                ]"
                :background="heroBackground"
            />

            <section class="gp-section">
                <div class="container">
                    <div class="row g-5">
                        <div class="col-lg-4">
                            <aside class="service-details-sidebar">
                                <div v-if="telefonoPrincipal || emailPrincipal" class="gp-widget">
                                    <h4 class="h5 mb-3">Contacto</h4>
                                    <div v-if="telefonoPrincipal" class="d-flex gap-3 mb-3">
                                        <span class="contact-icon">
                                            <i class="fa-solid fa-phone"></i>
                                        </span>
                                        <div>
                                            <div class="fw-semibold">Teléfono</div>
                                            <a :href="`tel:${telefonoPrincipal.replace(/[^\d+]/g, '')}`">
                                                {{ telefonoPrincipal }}
                                            </a>
                                        </div>
                                    </div>
                                    <div v-if="emailPrincipal" class="d-flex gap-3">
                                        <span class="contact-icon">
                                            <i class="fa-solid fa-envelope"></i>
                                        </span>
                                        <div>
                                            <div class="fw-semibold">Email</div>
                                            <a :href="`mailto:${emailPrincipal}`">{{ emailPrincipal }}</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="gp-widget gp-widget-cta">
                                    <h4 class="h5 mb-3">¿Necesita asesoría?</h4>
                                    <p class="mb-4 opacity9">
                                        Contáctenos y conozca nuestras soluciones de comunicación satelital.
                                    </p>
                                    <Link :href="route('public.contacto')" class="butn secondary sm">
                                        Contactar
                                    </Link>
                                </div>

                                <div v-if="imagenes.length" class="row g-3">
                                    <div v-for="imagen in imagenes" :key="imagen.id" class="col-6">
                                        <img
                                            :src="resolveImage(imagen.ruta_imagen)"
                                            alt=""
                                            class="img-fluid border-radius-10 w-100"
                                            style="height: 120px; object-fit: cover;"
                                        />
                                    </div>
                                </div>
                            </aside>
                        </div>

                        <div class="col-lg-8">
                            <ol v-if="eventos.length" class="gp-timeline">
                                <li v-for="evento in eventos" :key="evento.id" class="gp-timeline-item">
                                    <span class="gp-timeline-dot"></span>
                                    <span class="gp-timeline-year">{{ evento.anio }}</span>
                                    <h3 class="gp-timeline-title h4">{{ evento.titulo }}</h3>
                                    <p class="gp-timeline-desc mb-0">{{ evento.descripcion }}</p>
                                </li>
                            </ol>
                            <p v-else class="text-muted mb-0">
                                Muy pronto compartiremos los hitos de nuestra historia.
                            </p>
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
