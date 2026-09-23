<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import GalleryGrid from '@/Components/Public/GalleryGrid.vue';
import { usePublicSite } from '@/composables/usePublicSite';

defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});

const { sitioNombre } = usePublicSite();
</script>

<template>
    <Head title="Galeria" />

    <PublicLayout>
        <PageHero
            title="Galeria de proyectos"
            :breadcrumbs="[
                { label: 'Inicio', href: route('public.home') },
                { label: 'Galeria' },
            ]"
            background="/images/demo/page-title/con3.jpg"
        />

        <!-- cgi-bin galeria.html — PORTFOLIO 4 COLUMNS -->
        <section>
            <div class="container">
                <div class="section-heading" v-reveal="{ delay: 100 }">
                    <span class="subtitle">{{ sitioNombre || 'Greenpoint' }}</span>
                    <h2>Galeria de <span class="font-weight-400">Proyectos</span></h2>
                </div>
                <GalleryGrid :items="items" />
            </div>
        </section>
    </PublicLayout>
</template>
