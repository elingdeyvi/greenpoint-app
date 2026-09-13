<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { usePublicImage } from '@/composables/usePublicImage';

const props = defineProps({
    clientes: {
        type: Array,
        default: () => [],
    },
    /** Si true, se comporta como carrusel autoplay (producción). */
    carousel: {
        type: Boolean,
        default: true,
    },
    intervalMs: {
        type: Number,
        default: 3000,
    },
});

const { resolveImage } = usePublicImage();
const index = ref(0);
const perView = ref(4);
let timer = null;

const items = computed(() => props.clientes);

const updatePerView = () => {
    const w = window.innerWidth;
    if (w < 576) {
        perView.value = 1;
    } else if (w < 768) {
        perView.value = 2;
    } else if (w < 992) {
        perView.value = 3;
    } else {
        perView.value = 4;
    }
};

const maxIndex = computed(() => Math.max(0, items.value.length - perView.value));

const goTo = (next) => {
    if (!items.value.length) {
        return;
    }
    if (next > maxIndex.value) {
        index.value = 0;
    } else if (next < 0) {
        index.value = maxIndex.value;
    } else {
        index.value = next;
    }
};

const next = () => goTo(index.value + 1);

const stop = () => {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
};

const start = () => {
    stop();
    if (!props.carousel || items.value.length <= perView.value) {
        return;
    }
    timer = setInterval(next, props.intervalMs);
};

const trackStyle = computed(() => {
    const pct = 100 / perView.value;
    return {
        transform: `translateX(-${index.value * pct}%)`,
        '--gp-slide-basis': `${pct}%`,
    };
});

watch([() => props.clientes.length, perView, () => props.carousel], () => {
    index.value = 0;
    start();
});

onMounted(() => {
    updatePerView();
    start();
    window.addEventListener('resize', updatePerView, { passive: true });
});

onUnmounted(() => {
    stop();
    window.removeEventListener('resize', updatePerView);
});
</script>

<template>
    <div
        v-if="clientes.length && carousel"
        class="gp-client-carousel"
        @mouseenter="stop"
        @mouseleave="start"
    >
        <div class="gp-stream-track" :style="trackStyle">
            <div
                v-for="cliente in clientes"
                :key="cliente.id"
                class="gp-stream-slide"
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
    </div>

    <div v-else-if="clientes.length" class="row g-4">
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
