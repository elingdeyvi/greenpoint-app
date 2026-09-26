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

const pageTitle = computed(() => props.pagina?.titulo || 'Tecnologia');
const featuredImage = computed(
    () =>
        resolveImage(props.pagina?.imagen_destacada) ||
        '/images/demo/tecnologia/tec1.jpg',
);
const secciones = computed(() => props.pagina?.secciones ?? []);
const contenido = computed(() => props.pagina?.contenido || '');
</script>

<template>
    <Head :title="pageTitle" />

    <PublicLayout>
        <template v-if="pagina">
            <PageHero
                :title="pageTitle"
                :breadcrumbs="[
                    { label: 'Inicio', href: route('public.home') },
                    { label: 'Tecnologia' },
                ]"
                background="/images/demo/page-title/con3.jpg"
            />

            <section>
                <div class="container">
                    <div class="row mb-2-5">
                        <div class="col-lg-12" v-reveal="{ delay: 100 }">
                            <img
                                :src="featuredImage"
                                class="border-radius-10 w-100"
                                :alt="pageTitle"
                            />
                        </div>
                    </div>

                    <div v-if="contenido" class="row mb-2-5" v-reveal="{ delay: 150 }">
                        <div class="col-lg-10 mx-auto">
                            <div class="lead" style="white-space: pre-line">{{ contenido }}</div>
                        </div>
                    </div>

                    <div v-if="secciones.length" class="row">
                        <div
                            v-for="(seccion, index) in secciones"
                            :key="seccion.id || index"
                            class="col-md-6 mb-1-9"
                            v-reveal="{ delay: 100 + index * 80 }"
                        >
                            <h3 class="h5 mb-3">{{ seccion.titulo }}</h3>
                            <p class="mb-0" style="white-space: pre-line">{{ seccion.contenido }}</p>
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
