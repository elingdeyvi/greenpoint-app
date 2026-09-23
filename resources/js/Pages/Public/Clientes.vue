<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import ClientLogoGrid from '@/Components/Public/ClientLogoGrid.vue';
import { usePublicSite } from '@/composables/usePublicSite';

defineProps({
    clientes: {
        type: Array,
        default: () => [],
    },
});

const { sitioNombre } = usePublicSite();
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
            background="/images/demo/page-title/con3.jpg"
        />

        <!-- cgi-bin clientes.html — OUR TEAM / logos grid -->
        <section>
            <div class="container">
                <div class="section-heading" v-reveal="{ delay: 100 }">
                    <span class="subtitle">{{ sitioNombre || 'Greenpoint' }}</span>
                    <h2>Algunos <span class="font-weight-400">Clientes</span></h2>
                </div>
                <ClientLogoGrid :clientes="clientes" :carousel="false" />
            </div>
        </section>
    </PublicLayout>
</template>
