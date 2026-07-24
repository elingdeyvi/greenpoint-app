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
</script>

<template>
    <Head :title="pagina?.titulo || 'Aviso de privacidad'" />

    <PublicLayout>
        <template v-if="pagina">
            <PageHero
                :title="pagina.titulo || 'Aviso de privacidad'"
                :breadcrumbs="[
                    { label: 'Inicio', href: route('public.home') },
                    { label: 'Nosotros' },
                    { label: 'Aviso de Privacidad' },
                ]"
                background="/images/demo/banners/banner1.jpg"
            />

            <section class="gp-section">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div class="gp-legal-box">
                                <div v-if="secciones.length" class="d-flex flex-column gap-4">
                                    <article
                                        v-for="seccion in secciones"
                                        :key="seccion.id"
                                        class="mb-1"
                                    >
                                        <h2 class="h4 mb-3">{{ seccion.titulo }}</h2>
                                        <p
                                            v-if="seccion.contenido"
                                            style="white-space: pre-line; text-align: justify;"
                                            class="mb-3"
                                        >
                                            {{ seccion.contenido }}
                                        </p>
                                        <ul v-if="seccion.listas?.length" class="list-style1">
                                            <li
                                                v-for="(lista, index) in seccion.listas"
                                                :key="lista.id ?? index"
                                            >
                                                <i class="fa-solid fa-check text-gp-primary"></i>
                                                <span>{{ lista.texto }}</span>
                                            </li>
                                        </ul>
                                    </article>
                                </div>
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
