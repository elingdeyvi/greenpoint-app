<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { usePublicImage } from '@/composables/usePublicImage';

const props = defineProps({
    banners: {
        type: Array,
        default: () => [],
    },
    intervalMs: {
        type: Number,
        default: 6000,
    },
});

const { imageStyle } = usePublicImage();
const activeIndex = ref(0);
let timer = null;

const hasBanners = computed(() => props.banners.length > 0);

const goTo = (index) => {
    if (!props.banners.length) {
        return;
    }
    activeIndex.value = (index + props.banners.length) % props.banners.length;
};

const next = () => goTo(activeIndex.value + 1);
const prev = () => goTo(activeIndex.value - 1);

const stop = () => {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
};

const start = () => {
    stop();
    if (props.banners.length < 2) {
        return;
    }
    timer = setInterval(next, props.intervalMs);
};

watch(
    () => props.banners.length,
    () => {
        activeIndex.value = 0;
        start();
    },
);

onMounted(start);
onUnmounted(stop);
</script>

<template>
    <section
        v-if="hasBanners"
        class="p-0 full-screen banner1 top-position1"
        @mouseenter="stop"
        @mouseleave="start"
    >
        <div class="gp-carousel">
            <div
                v-for="(banner, index) in banners"
                :key="banner.id ?? index"
                class="gp-carousel-slide"
                :class="{ 'is-active': index === activeIndex }"
                :style="imageStyle(banner.imagen)"
                role="group"
                :aria-hidden="index !== activeIndex"
            >
                <div class="container gp-carousel-caption">
                    <div class="row align-items-center">
                        <div class="col-md-10 col-lg-8">
                            <h1 class="title">{{ banner.titulo }}</h1>
                            <p v-if="banner.descripcion" class="d-none d-sm-block">
                                {{ banner.descripcion }}
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                <Link
                                    v-if="banner.enlace"
                                    :href="banner.enlace"
                                    class="butn"
                                >
                                    <span>Leer Más</span>
                                </Link>
                                <Link
                                    v-else
                                    :href="route('public.nosotros')"
                                    class="butn"
                                >
                                    <span>Leer Más</span>
                                </Link>
                                <Link :href="route('public.contacto')" class="butn secondary">
                                    Contacto
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <template v-if="banners.length > 1">
                <div class="gp-carousel-dots">
                    <button
                        v-for="(banner, index) in banners"
                        :key="`dot-${banner.id ?? index}`"
                        type="button"
                        class="gp-carousel-dot"
                        :class="{ 'is-active': index === activeIndex }"
                        :aria-label="`Banner ${index + 1}`"
                        @click="goTo(index)"
                    />
                </div>

                <button type="button" class="gp-carousel-nav gp-carousel-prev" aria-label="Anterior" @click="prev">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button type="button" class="gp-carousel-nav gp-carousel-next" aria-label="Siguiente" @click="next">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </template>

            <span class="banner-shape1 d-none d-md-block ani-move"></span>
            <span class="banner-shape2 d-none d-md-block"></span>
        </div>
    </section>
</template>
