<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        default: '',
    },
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
    background: {
        type: String,
        default: '/images/demo/banners/banner1.jpg',
    },
});
</script>

<template>
    <section class="gp-page-title top-position1 py-0">
        <div
            class="page-title-section bg-img cover-background left-overlay-dark"
            :style="{ backgroundImage: `url('${background}')` }"
        >
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>{{ title }}</h1>
                        <ul v-if="breadcrumbs.length" class="gp-breadcrumb">
                            <li v-for="(crumb, index) in breadcrumbs" :key="index">
                                <Link v-if="crumb.href" :href="crumb.href">{{ crumb.label }}</Link>
                                <span v-else>{{ crumb.label }}</span>
                            </li>
                        </ul>
                        <p v-else-if="subtitle" class="text-white opacity8 mb-0 mt-2">
                            {{ subtitle }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <span class="page-title-shape1 d-none d-sm-block"></span>
        <span class="page-title-shape2 d-none d-sm-block"></span>
    </section>
</template>
