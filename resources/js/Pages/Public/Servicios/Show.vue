<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import { usePublicImage } from '@/composables/usePublicImage';

const { resolveImage } = usePublicImage();

const props = defineProps({
    servicio: {
        type: Object,
        required: true,
    },
});

const imageUrl = computed(() => resolveImage(props.servicio.imagen));
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
            :background="imageUrl || '/images/demo/servicios/gestion-ambiental.jpg'"
        />

        <section class="gp-section">
            <div class="container">
                <div class="row g-5 align-items-start">
                    <div class="col-lg-8">
                        <div class="card card-style5 border-0">
                            <div class="card-body p-0 p-md-2">
                                <img
                                    v-if="imageUrl"
                                    :src="imageUrl"
                                    :alt="servicio.nombre"
                                    class="img-fluid border-radius-10 w-100 mb-4"
                                    style="object-fit: cover; max-height: 420px;"
                                />
                                <h2 class="h3 mb-3">{{ servicio.nombre }}</h2>
                                <p class="fs-5 mb-4" style="white-space: pre-line; text-align: justify;">
                                    {{ servicio.descripcion }}
                                </p>
                                <Link :href="route('public.contacto')" class="butn">
                                    <span>Solicitar información</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="gp-widget gp-widget-cta">
                            <h4 class="h5 mb-3">¿Interesado en este servicio?</h4>
                            <p class="mb-4 opacity9">
                                Contáctenos para recibir una asesoría personalizada sobre
                                {{ servicio.nombre }}.
                            </p>
                            <Link :href="route('public.contacto')" class="butn secondary sm">
                                Contactar
                            </Link>
                        </div>

                        <div class="gp-widget">
                            <h4 class="h5 mb-3">Otros servicios</h4>
                            <Link
                                :href="route('public.servicios.index')"
                                class="fw-semibold"
                            >
                                Ver catálogo completo
                                <i class="fa-solid fa-arrow-right-long ms-1 small"></i>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
