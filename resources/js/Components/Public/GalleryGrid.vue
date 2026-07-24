<script setup>
import { ref } from 'vue';
import { usePublicImage } from '@/composables/usePublicImage';

defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});

const { resolveImage } = usePublicImage();
const activeImage = ref(null);
const activeTitle = ref('');
const isOpen = ref(false);

const open = (item) => {
    activeImage.value = resolveImage(item.imagen);
    activeTitle.value = item.titulo || '';
    isOpen.value = true;
};

const close = () => {
    isOpen.value = false;
};
</script>

<template>
    <div v-if="items.length" class="gp-gallery-grid">
        <button
            v-for="item in items"
            :key="item.id"
            type="button"
            class="gp-gallery-item"
            @click="open(item)"
        >
            <img :src="resolveImage(item.imagen)" :alt="item.titulo || 'Imagen de galería'" loading="lazy" />
            <div class="gp-gallery-overlay">
                <i class="fa-solid fa-magnifying-glass-plus"></i>
            </div>
        </button>
    </div>
    <p v-else class="text-muted text-center py-4 mb-0">
        Aún no hay imágenes en la galería.
    </p>

    <Teleport to="body">
        <div
            v-if="isOpen"
            class="gp-lightbox"
            role="dialog"
            aria-modal="true"
            @click.self="close"
        >
            <div class="gp-lightbox-dialog">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">{{ activeTitle }}</h5>
                    <button type="button" class="btn-close" aria-label="Cerrar" @click="close" />
                </div>
                <img v-if="activeImage" :src="activeImage" :alt="activeTitle" class="img-fluid rounded" />
            </div>
        </div>
    </Teleport>
</template>
