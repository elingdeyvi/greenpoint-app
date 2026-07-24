<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import ServiceCard from '@/Components/Public/ServiceCard.vue';
import { usePublicSite } from '@/composables/usePublicSite';

defineProps({
    servicios: {
        type: Array,
        default: () => [],
    },
});

const { sitioNombre } = usePublicSite();
</script>

<template>
    <Head title="Servicios" />

    <PublicLayout>
        <PageHero
            title="Soluciones"
            :breadcrumbs="[
                { label: 'Inicio', href: route('public.home') },
                { label: 'Servicios' },
                { label: 'Soluciones' },
            ]"
            background="/images/demo/servicios/gestion-ambiental.jpg"
        />

        <section class="gp-section">
            <div class="container">
                <div class="section-heading">
                    <span class="subtitle">{{ sitioNombre }}</span>
                    <h2>Nuestras <span class="font-weight-400">Soluciones</span></h2>
                </div>

                <div v-if="servicios.length" class="row g-4">
                    <div
                        v-for="servicio in servicios"
                        :key="servicio.id"
                        class="col-md-6 col-lg-4"
                    >
                        <ServiceCard :servicio="servicio" />
                    </div>
                </div>
                <p v-else class="text-muted text-center py-4 mb-0">
                    Muy pronto publicaremos nuestro catálogo de servicios.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
