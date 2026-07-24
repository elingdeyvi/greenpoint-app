<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import GalleryGrid from '@/Components/Public/GalleryGrid.vue';
import { usePublicImage } from '@/composables/usePublicImage';
import { usePublicSite } from '@/composables/usePublicSite';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});

const { resolveImage } = usePublicImage();
const { sitioNombre } = usePublicSite();

const heroBackground = computed(
    () => resolveImage(props.items[0]?.imagen) || '/images/demo/galeria/galeria1.jpg',
);
</script>

<template>
    <Head title="Galería" />

    <PublicLayout>
        <PageHero
            title="Galería"
            :breadcrumbs="[
                { label: 'Inicio', href: route('public.home') },
                { label: 'Galería' },
            ]"
            :background="heroBackground"
        />

        <section class="gp-section">
            <div class="container">
                <div class="section-heading">
                    <span class="subtitle">{{ sitioNombre }}</span>
                    <h2>Nuestra <span class="font-weight-400">Galería</span></h2>
                </div>
                <GalleryGrid :items="items" />
            </div>
        </section>
    </PublicLayout>
</template>
