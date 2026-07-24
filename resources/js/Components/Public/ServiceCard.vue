<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { usePublicImage } from '@/composables/usePublicImage';

const props = defineProps({
    servicio: {
        type: Object,
        required: true,
    },
});

const { resolveImage } = usePublicImage();
const imageUrl = computed(() => resolveImage(props.servicio.imagen));
const excerpt = computed(() => {
    const text = props.servicio.descripcion || '';
    return text.length > 110 ? `${text.slice(0, 110)}…` : text;
});
</script>

<template>
    <Link
        :href="route('public.servicios.show', servicio.id)"
        class="gp-card text-decoration-none d-block text-reset h-100"
    >
        <img
            v-if="imageUrl"
            :src="imageUrl"
            :alt="servicio.nombre"
            class="gp-card-img"
        />
        <div v-else class="gp-card-img d-flex align-items-center justify-content-center">
            <i class="fa-solid fa-satellite-dish fs-1 text-white opacity-75"></i>
        </div>
        <div class="gp-card-body">
            <h3 class="gp-card-title h5 mb-2">{{ servicio.nombre }}</h3>
            <p class="mb-0" style="color: var(--gp-body);">{{ excerpt }}</p>
            <span class="d-inline-flex align-items-center gap-1 text-gp-primary fw-semibold mt-3">
                Ver más <i class="fa-solid fa-arrow-right-long small"></i>
            </span>
        </div>
    </Link>
</template>
