<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';

const props = defineProps({
    contacto: {
        type: Object,
        required: true,
    },
});

const isCarmen = computed(
    () =>
        /carmen/i.test(props.contacto.ubicacion || '') ||
        /carmen/i.test(props.contacto.subtitulo || ''),
);

/** cgi-bin: carmen.html hero = ciudad; tabasco/veracruz = ubicacion */
const heroTitle = computed(() =>
    isCarmen.value
        ? props.contacto.subtitulo || 'Ciudad del Carmen'
        : props.contacto.ubicacion || 'Contacto',
);

/** cgi-bin section-heading: subtitle = ciudad, h2 = estado/region */
const sectionSubtitle = computed(
    () => props.contacto.subtitulo || props.contacto.ubicacion || '',
);
const sectionHeading = computed(
    () =>
        props.contacto.region ||
        (isCarmen.value ? 'Campeche' : props.contacto.ubicacion || ''),
);

/** Varios teléfonos separados por salto de línea (carmen.html) */
const telefonos = computed(() =>
    String(props.contacto.telefono || '')
        .split(/\r?\n|;(?=\s*\()/)
        .map((t) => t.trim())
        .filter(Boolean),
);
</script>

<template>
    <Head :title="heroTitle" />

    <PublicLayout>
        <PageHero
            :title="heroTitle"
            :breadcrumbs="[
                { label: 'Inicio', href: route('public.home') },
                { label: 'Contacto' },
            ]"
            background="/images/demo/page-title/con1.jpg"
        />

        <!-- cgi-bin tabasco.html / veracruz.html / carmen.html -->
        <section class="p-0 bg-light">
            <div class="row g-0">
                <div class="col-lg-6 col-xxl-4">
                    <div class="p-1-6 p-sm-6 p-lg-8">
                        <div class="section-heading text-start mb-4">
                            <span class="subtitle">{{ sectionSubtitle }}</span>
                            <h2 class="w-100 mb-0">
                                {{ sectionHeading }}
                                <span class="font-weight-400"> </span>
                            </h2>
                        </div>

                        <div
                            v-if="contacto.direccion"
                            class="card border-color-extra-light-gray border-radius-10 mb-4"
                        >
                            <div class="card-body p-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-map-marker-alt contact-icon-box mb-0"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-4">
                                        <h3 class="h4">Oficinas</h3>
                                        <p class="mb-0 w-sm-80 display-md-28">
                                            {{ contacto.direccion }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="telefonos.length"
                            class="card border-color-extra-light-gray border-radius-10 mb-4"
                        >
                            <div class="card-body p-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-phone-alt contact-icon-box mb-0"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-4">
                                        <h3 class="h4">Telefono</h3>
                                        <!-- cgi-bin: texto body, no link naranja -->
                                        <p
                                            v-for="(tel, i) in telefonos"
                                            :key="tel"
                                            class="display-md-28 gp-contact-plain"
                                            :class="i < telefonos.length - 1 ? 'mb-1' : 'mb-0'"
                                        >
                                            {{ tel }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="contacto.email"
                            class="card border-color-extra-light-gray border-radius-10"
                        >
                            <div class="card-body p-4">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="far fa-envelope contact-icon-box mb-0"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-4">
                                        <h3 class="h4">Email</h3>
                                        <p class="mb-1 display-md-28 gp-contact-plain">
                                            {{ contacto.email }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-xxl-8">
                    <iframe
                        v-if="contacto.mapa_url"
                        :src="contacto.mapa_url"
                        width="100%"
                        height="800"
                        style="border: 0"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        :title="`Mapa ${heroTitle}`"
                    ></iframe>
                    <div
                        v-else
                        class="d-flex align-items-center justify-content-center bg-white border"
                        style="min-height: 400px"
                    >
                        <p class="text-muted mb-0">Mapa no disponible</p>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
