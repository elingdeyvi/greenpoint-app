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
const animKey = ref(0);
/** Igual que cgi-bin/js/main.js fullScreenHeight(): $(window).height() */
const viewportHeight = ref(null);
let timer = null;
let resizeTimer = null;

const hasBanners = computed(() => props.banners.length > 0);

const bannerSectionStyle = computed(() =>
    viewportHeight.value
        ? { minHeight: `${viewportHeight.value}px` }
        : undefined,
);

const syncFullScreenHeight = () => {
    viewportHeight.value = window.innerHeight;
};

const onResize = () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(syncFullScreenHeight, 500);
};

const splitTitle = (titulo = '') => {
    if (titulo.includes('|')) {
        const [main, light] = titulo.split('|');
        return { main: main.trim(), light: (light || '').trim() };
    }

    const patterns = [
        /^(L[ií]der en comunicaciones)\s+(para el sector Petrolero)$/i,
        /^(Comunicaciones)\s+(Mar[ií]timas?\s+ROBUSTAS)$/i,
        /^(Servicios de)\s+(Conexi[oó]n\s+SATELITAL)$/i,
    ];

    for (const pattern of patterns) {
        const match = titulo.match(pattern);
        if (match) {
            return { main: match[1], light: match[2] };
        }
    }

    return { main: titulo, light: '' };
};

const goTo = (index) => {
    if (!props.banners.length) {
        return;
    }
    activeIndex.value = (index + props.banners.length) % props.banners.length;
    animKey.value += 1;
};

const next = () => goTo(activeIndex.value + 1);
const prev = () => goTo(activeIndex.value - 1);

const stop = () => {
    if (timer) {
        clearInterval(timer);
    }
    timer = null;
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
        animKey.value += 1;
        start();
    },
);

onMounted(() => {
    syncFullScreenHeight();
    start();
    window.addEventListener('resize', onResize, { passive: true });
});

onUnmounted(() => {
    stop();
    clearTimeout(resizeTimer);
    window.removeEventListener('resize', onResize);
});
</script>

<template>
    <section
        v-if="hasBanners"
        class="p-0 full-screen banner1 top-position1 gp-full-bleed gp-banner-section"
        :style="bannerSectionStyle"
        @mouseenter="stop"
        @mouseleave="start"
    >
        <div class="gp-carousel" :style="bannerSectionStyle">
            <div
                v-for="(banner, index) in banners"
                :key="banner.id ?? index"
                class="gp-carousel-slide"
                :class="{ 'is-active': index === activeIndex }"
                :style="imageStyle(banner.imagen)"
                role="group"
                :aria-hidden="index !== activeIndex"
            >
                <div class="container d-flex flex-column gp-carousel-caption">
                    <div
                        class="row justify-content-center justify-content-sm-start align-items-center gp-banner-row"
                        :style="bannerSectionStyle"
                    >
                        <div class="col-md-10 col-lg-8">
                            <div
                                v-if="index === activeIndex"
                                :key="`cap-${animKey}`"
                                class="gp-slide-anim"
                            >
                                <h1 class="mb-2-2 title gp-anim-up" style="animation-delay: 0.15s">
                                    {{ splitTitle(banner.titulo).main }}
                                    <span
                                        v-if="splitTitle(banner.titulo).light"
                                        class="font-weight-400"
                                    >
                                        {{ splitTitle(banner.titulo).light }}
                                    </span>
                                </h1>
                                <p
                                    v-if="banner.descripcion"
                                    class="display-28 w-sm-95 w-md-90 mb-2-2 opacity8 d-none d-sm-block gp-anim-up"
                                    style="animation-delay: 0.35s"
                                >
                                    {{ banner.descripcion }}
                                </p>
                                <div class="gp-anim-up" style="animation-delay: 0.55s">
                                    <Link
                                        :href="banner.enlace || route('public.nosotros')"
                                        class="butn me-2 my-1 my-sm-0"
                                    >
                                        <span>Leer Mas</span>
                                    </Link>
                                    <Link
                                        :href="route('public.contacto')"
                                        class="butn secondary my-1 my-sm-0"
                                    >
                                        Contacto
                                    </Link>
                                </div>
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

                <button
                    type="button"
                    class="gp-carousel-nav gp-carousel-prev d-none d-md-inline-flex"
                    aria-label="Anterior"
                    @click="prev"
                >
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button
                    type="button"
                    class="gp-carousel-nav gp-carousel-next d-none d-md-inline-flex"
                    aria-label="Siguiente"
                    @click="next"
                >
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </template>
        </div>

        <!-- Decos exactos cgi-bin (z-index: borde blanco encima del verde) -->
        <span class="banner-shape1 ani-top-bottom d-none d-md-block"></span>
        <span class="banner-shape2 d-none d-md-block"></span>
        <div
            class="d-none d-sm-inline-block px-1-9 py-1-6 border position-absolute left bottom-5 border-radius-5 z-index-3"
        ></div>
        <div
            class="d-none d-sm-inline-block px-1-9 py-1-6 bg-secondary position-absolute left-5 bottom-10 border-radius-5 z-index-2"
        ></div>
        <div
            class="d-inline-block p-2 bg-secondary rounded-circle position-absolute right-20 bottom-25 ani-move z-index-2"
        ></div>
        <div
            class="d-inline-block p-2 bg-white rounded-circle position-absolute left-15 top-20 ani-move z-index-2"
        ></div>
    </section>
</template>
