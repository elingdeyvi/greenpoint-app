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

            <!-- cgi-bin tecnologia.html — solo imagen destacada (tec1) -->
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
