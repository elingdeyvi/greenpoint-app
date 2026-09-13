<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';

const props = defineProps({
    pagina: {
        type: Object,
        default: null,
    },
});

const secciones = computed(() => props.pagina?.secciones ?? []);
const pageTitle = computed(() => props.pagina?.titulo || 'Aviso de privacidad');
</script>

<template>
    <Head :title="pageTitle" />

    <PublicLayout>
        <template v-if="pagina">
            <PageHero
                :title="pageTitle"
                :breadcrumbs="[
                    { label: 'Inicio', href: route('public.home') },
                    { label: 'Nosotros' },
                    { label: 'Aviso de Privacidad' },
                ]"
                background="/images/demo/page-title/con1.jpg"
            />

            <section>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div
                                class="p-1-6 p-md-2-2 border border-color-extra-light-gray border-radius-10 gp-legal-box"
                            >
                                <template v-if="secciones.length">
                                    <div
                                        v-for="(seccion, sIndex) in secciones"
                                        :key="seccion.id"
                                        class="mb-1-6 mb-lg-1-9 mb-xl-2-5"
                                        :class="{ 'mb-0': sIndex === secciones.length - 1 }"
                                    >
                                        <h3 v-if="seccion.titulo" class="mb-3 h5">
                                            {{ seccion.titulo }}
                                        </h3>
                                        <p
                                            v-if="seccion.contenido"
                                            class="mb-0"
                                            style="text-align: justify; white-space: pre-line"
                                        >
                                            {{ seccion.contenido }}
                                        </p>
                                        <ul
                                            v-if="seccion.listas?.length"
                                            class="list-style1 mb-0 mt-3"
                                        >
                                            <li
                                                v-for="(lista, index) in seccion.listas"
                                                :key="lista.id ?? index"
                                            >
                                                <i
                                                    class="ti-check text-primary me-3 font-weight-600"
                                                ></i>
                                                <span>{{ lista.texto }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </template>
                                <p v-else class="text-muted text-center py-4 mb-0">
                                    Aún no se ha publicado el contenido de este documento.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>

        <section v-else class="gp-section text-center">
            <div class="container">
                <i class="fa-solid fa-file-shield fs-1 text-gp-primary mb-3"></i>
                <h1 class="h3">Contenido no disponible</h1>
                <p class="text-muted mb-0">
                    La información de esta sección aún no ha sido publicada.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
