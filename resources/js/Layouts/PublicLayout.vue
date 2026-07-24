<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import PublicNavbar from '@/Components/Public/PublicNavbar.vue';
import PublicFooter from '@/Components/Public/PublicFooter.vue';

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);
const showScrollTop = ref(false);

const onScroll = () => {
    showScrollTop.value = window.scrollY > 320;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

onMounted(() => {
    document.body.className = 'gp-public';
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
});
</script>

<template>
    <div class="gp-public-wrapper public-layout d-flex flex-column min-vh-100">
        <PublicNavbar />

        <div v-if="flashSuccess" class="container" style="margin-top: 7rem;">
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{ flashSuccess }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        </div>

        <main class="flex-grow-1">
            <slot />
        </main>

        <PublicFooter />

        <button
            type="button"
            class="scroll-to-top"
            :class="{ 'is-visible': showScrollTop }"
            aria-label="Volver arriba"
            @click="scrollToTop"
        >
            <i class="fa-solid fa-wifi fa-rotate-270"></i>
        </button>
    </div>
</template>
