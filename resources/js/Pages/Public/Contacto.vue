<script setup>
import { onMounted } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';
import { usePublicSite } from '@/composables/usePublicSite';

const props = defineProps({
    contactos: {
        type: Array,
        default: () => [],
    },
    redesSociales: {
        type: Array,
        default: () => [],
    },
});

const { sitioNombre } = usePublicSite();

const form = useForm({
    nombre: '',
    email: '',
    telefono: '',
    mensaje: '',
});

const submit = () => {
    form.post(route('public.contacto.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

/** Compatibilidad con enlaces antiguos /contacto#oficina-{id} */
onMounted(() => {
    const hash = window.location.hash || '';
    const match = hash.match(/^#oficina-(\d+)$/);
    if (!match) {
        return;
    }
    const id = Number(match[1]);
    if (props.contactos.some((c) => c.id === id)) {
        router.visit(route('public.contacto.show', id), { replace: true });
    }
});
</script>

<template>
    <Head title="Contacto" />

    <PublicLayout>
        <PageHero
            title="Contacto"
            :breadcrumbs="[
                { label: 'Inicio', href: route('public.home') },
                { label: 'Contacto' },
            ]"
            background="/images/demo/banners/banner2.jpg"
        />

        <section class="gp-section bg-light">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-5 col-xxl-4">
                        <div class="section-heading text-start mb-4">
                            <span class="subtitle">{{ sitioNombre }}</span>
                            <h2 class="w-100">Nuestras <span class="font-weight-400">Oficinas</span></h2>
                        </div>

                        <div v-if="contactos.length" class="d-flex flex-column gap-3">
                            <a
                                v-for="contacto in contactos"
                                :id="`oficina-${contacto.id}`"
                                :key="contacto.id"
                                :href="route('public.contacto.show', contacto.id)"
                                class="gp-contact-card text-decoration-none text-reset"
                            >
                                <span class="contact-icon-box">
                                    <i class="fa-solid fa-location-dot"></i>
                                </span>
                                <div>
                                    <h3 class="h5 mb-2">{{ contacto.ubicacion }}</h3>
                                    <p v-if="contacto.direccion" class="mb-2 text-muted">
                                        {{ contacto.direccion }}
                                    </p>
                                    <p v-if="contacto.telefono" class="mb-1">
                                        {{ contacto.telefono }}
                                    </p>
                                    <p v-if="contacto.email" class="mb-0">
                                        {{ contacto.email }}
                                    </p>
                                </div>
                            </a>
                        </div>
                        <p v-else class="text-muted">
                            Muy pronto publicaremos nuestros datos de contacto.
                        </p>

                        <div v-if="redesSociales.length" class="mt-4">
                            <h3 class="h6 text-uppercase">Síguenos</h3>
                            <ul class="footer-social-style mt-2">
                                <li v-for="red in redesSociales" :key="red.nombre">
                                    <a
                                        :href="red.url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        :title="red.nombre"
                                    >
                                        <i :class="red.icono || 'fa-solid fa-link'"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-lg-7 col-xxl-8">
                        <div class="gp-card p-4 p-md-5">
                            <div class="section-heading text-start mb-4">
                                <span class="subtitle">{{ sitioNombre }}</span>
                                <h2 class="w-100 h3 mb-0">Envíanos un <span class="font-weight-400">mensaje</span></h2>
                            </div>

                            <form @submit.prevent="submit">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="nombre">Nombre</label>
                                        <input
                                            id="nombre"
                                            v-model="form.nombre"
                                            type="text"
                                            class="form-control gp-form-control"
                                            :class="{ 'is-invalid': form.errors.nombre }"
                                            required
                                        />
                                        <div v-if="form.errors.nombre" class="invalid-feedback">
                                            {{ form.errors.nombre }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="email">Correo electrónico</label>
                                        <input
                                            id="email"
                                            v-model="form.email"
                                            type="email"
                                            class="form-control gp-form-control"
                                            :class="{ 'is-invalid': form.errors.email }"
                                            required
                                        />
                                        <div v-if="form.errors.email" class="invalid-feedback">
                                            {{ form.errors.email }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold" for="telefono">Teléfono</label>
                                        <input
                                            id="telefono"
                                            v-model="form.telefono"
                                            type="text"
                                            class="form-control gp-form-control"
                                            :class="{ 'is-invalid': form.errors.telefono }"
                                        />
                                        <div v-if="form.errors.telefono" class="invalid-feedback">
                                            {{ form.errors.telefono }}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold" for="mensaje">Mensaje</label>
                                        <textarea
                                            id="mensaje"
                                            v-model="form.mensaje"
                                            rows="5"
                                            class="form-control gp-form-control"
                                            :class="{ 'is-invalid': form.errors.mensaje }"
                                            required
                                        ></textarea>
                                        <div v-if="form.errors.mensaje" class="invalid-feedback">
                                            {{ form.errors.mensaje }}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button
                                            type="submit"
                                            class="butn"
                                            :disabled="form.processing"
                                        >
                                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                            <span>Enviar mensaje</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
