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
    <div v-if="items.length" class="row portfolio-gallery-isotope mt-n1-9">
        <div
            v-for="(item, index) in items"
            :key="item.id"
            class="col-md-6 col-lg-4 col-xl-3 mt-1-9"
            v-reveal="{ delay: 200 + (index % 4) * 100 }"
        >
            <button
                type="button"
                class="portfolio-image border-0 bg-transparent p-0 w-100 text-start"
                :title="item.titulo"
                @click="open(item)"
            >
                <img
                    :src="resolveImage(item.imagen)"
                    :alt="item.titulo || 'Imagen de galería'"
                    class="border-radius-10"
                    loading="lazy"
                />
                <span class="portfolio-icon" aria-hidden="true">
                    <i class="fas fa-search"></i>
                </span>
            </button>
        </div>
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
                    <h5 class="mb-0 text-white">{{ activeTitle }}</h5>
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        aria-label="Cerrar"
                        @click="close"
                    />
                </div>
                <img
                    v-if="activeImage"
                    :src="activeImage"
                    :alt="activeTitle"
                    class="img-fluid rounded"
                />
            </div>
        </div>
    </Teleport>
</template>
