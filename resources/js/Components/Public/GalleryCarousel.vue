<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { usePublicImage } from '@/composables/usePublicImage';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    intervalMs: {
        type: Number,
        default: 3200,
    },
});

const { resolveImage } = usePublicImage();
const trackRef = ref(null);
const index = ref(0);
const perView = ref(6);
let timer = null;
let resizeObserver = null;

const slides = computed(() => props.items.slice(0, 12));

const updatePerView = () => {
    const w = window.innerWidth;
    if (w < 576) {
        perView.value = 1;
    } else if (w < 768) {
        perView.value = 2;
    } else if (w < 992) {
        perView.value = 3;
    } else if (w < 1200) {
        perView.value = 4;
    } else {
        perView.value = 6;
    }
};

const maxIndex = computed(() => Math.max(0, slides.value.length - perView.value));

const goTo = (next) => {
    if (!slides.value.length) {
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
    if (slides.value.length <= perView.value) {
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

watch([() => props.items.length, perView], () => {
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
    resizeObserver?.disconnect();
});
</script>

<template>
    <div
        v-if="slides.length"
        class="gp-stream-carousel"
        @mouseenter="stop"
        @mouseleave="start"
    >
        <div ref="trackRef" class="gp-stream-track" :style="trackStyle">
            <div
                v-for="item in slides"
                :key="item.id"
                class="gp-stream-slide"
            >
                <Link :href="route('public.galeria')" class="gp-gallery-home-item stream-wrapper">
                    <div class="gp-gallery-home-img stream-img overflow-hidden rounded mb-4">
                        <img
                            :src="resolveImage(item.imagen)"
                            :alt="item.titulo"
                            class="rounded"
                        />
                    </div>
                    <h4 class="mb-0 h5">
                        <span class="text-white">Ver galeria</span>
                    </h4>
                </Link>
            </div>
        </div>
    </div>
</template>
