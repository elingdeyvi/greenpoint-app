<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
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
const secciones = computed(() => props.pagina?.secciones ?? []);
const heroBackground = computed(
    () => featuredImage.value || '/images/demo/tecnologia/destacada.jpg',
);
</script>

<template>
    <Head :title="pagina?.titulo || 'Tecnología'" />

    <PublicLayout>
        <template v-if="pagina">
            <PageHero
                :title="pagina.titulo || 'Tecnología'"
                :breadcrumbs="[
                    { label: 'Inicio', href: route('public.home') },
                    { label: 'Tecnología' },
                ]"
                :background="heroBackground"
            />

            <section class="gp-section">
                <div class="container">
                    <div v-if="featuredImage" class="mb-4 mb-lg-5">
                        <img
                            :src="featuredImage"
                            :alt="pagina.titulo"
                            class="img-fluid border-radius-10 w-100"
                            style="max-height: 520px; object-fit: cover;"
                        />
                    </div>

                    <p
                        v-if="pagina.contenido"
                        class="fs-5 mb-5"
                        style="white-space: pre-line; text-align: justify;"
                    >
                        {{ pagina.contenido }}
                    </p>

                    <div v-if="secciones.length" class="row g-4">
                        <div
                            v-for="seccion in secciones"
                            :key="seccion.id"
                            class="col-md-6"
                        >
                            <div class="gp-card h-100">
                                <div class="gp-card-body">
                                    <h3 class="gp-card-title h5 mb-2">{{ seccion.titulo }}</h3>
                                    <p class="mb-0" style="white-space: pre-line;">
                                        {{ seccion.contenido }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>

        <section v-else class="gp-section text-center">
            <div class="container">
                <i class="fa-solid fa-microchip fs-1 text-gp-primary mb-3"></i>
                <h1 class="h3">Contenido no disponible</h1>
                <p class="text-muted mb-0">
                    La información de esta sección aún no ha sido publicada.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
