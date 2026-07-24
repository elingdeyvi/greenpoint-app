<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import ClientLogoGrid from '@/Components/Public/ClientLogoGrid.vue';
import { usePublicImage } from '@/composables/usePublicImage';
import { usePublicSite } from '@/composables/usePublicSite';

const props = defineProps({
    clientes: {
        type: Array,
        default: () => [],
    },
});

const { resolveImage } = usePublicImage();
const { sitioNombre } = usePublicSite();

const heroBackground = computed(
    () => resolveImage(props.clientes[0]?.logo) || '/images/demo/banners/banner3.jpg',
);
</script>

<template>
    <Head title="Clientes" />

    <PublicLayout>
        <PageHero
            title="Clientes"
            :breadcrumbs="[
                { label: 'Inicio', href: route('public.home') },
                { label: 'Nosotros' },
                { label: 'Clientes' },
            ]"
            :background="heroBackground"
        />

        <section class="gp-section">
            <div class="container">
                <div class="section-heading">
                    <span class="subtitle">{{ sitioNombre }}</span>
                    <h2>Algunos <span class="font-weight-400">Clientes</span></h2>
                </div>
                <ClientLogoGrid :clientes="clientes" />
            </div>
        </section>
    </PublicLayout>
</template>
