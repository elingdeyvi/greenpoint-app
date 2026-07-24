<script setup>
import { usePublicImage } from '@/composables/usePublicImage';

defineProps({
    clientes: {
        type: Array,
        default: () => [],
    },
});

const { resolveImage } = usePublicImage();
</script>

<template>
    <div v-if="clientes.length" class="row g-4">
        <div
            v-for="cliente in clientes"
            :key="cliente.id"
            class="col-sm-6 col-lg-3"
        >
            <component
                :is="cliente.enlace ? 'a' : 'div'"
                :href="cliente.enlace || undefined"
                :target="cliente.enlace ? '_blank' : undefined"
                :rel="cliente.enlace ? 'noopener noreferrer' : undefined"
                class="card card-style3 text-decoration-none h-100"
                :title="cliente.nombre"
            >
                <div class="social-icon-wrapper">
                    <img
                        v-if="cliente.logo"
                        :src="resolveImage(cliente.logo)"
                        :alt="cliente.nombre"
                        class="border-radius-10"
                    />
                    <span v-else class="text-muted small text-center">{{ cliente.nombre }}</span>
                </div>
            </component>
        </div>
    </div>
    <p v-else class="text-muted text-center py-4 mb-0">
        Aún no hay clientes registrados.
    </p>
</template>
