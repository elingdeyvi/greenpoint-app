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
        default: '/images/demo/page-title/con1.jpg',
    },
});
</script>

<template>
    <!-- cgi-bin page-title: shapes + decos + pill h1 -->
    <section class="gp-page-title top-position1 py-0">
        <div
            class="page-title-section bg-img cover-background left-overlay-dark"
            data-overlay-dark="7"
            :style="{ backgroundImage: `url('${background}')` }"
        >
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>{{ title }}</h1>
                        <div v-if="breadcrumbs.length" class="breadcrumb">
                            <ul>
                                <li v-for="(crumb, index) in breadcrumbs" :key="index">
                                    <Link v-if="crumb.href" :href="crumb.href">{{ crumb.label }}</Link>
                                    <a v-else href="#!">{{ crumb.label }}</a>
                                </li>
                            </ul>
                        </div>
                        <p v-else-if="subtitle" class="text-white opacity8 mb-0 mt-2">
                            {{ subtitle }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <span class="page-title-shape1 d-none d-sm-block"></span>
        <span class="page-title-shape2 d-none d-sm-block"></span>
        <div
            class="d-inline-block p-2 border-secondary border border-width-2 position-absolute left-5 bottom-10 bottom-sm-25 ani-left-right z-index-1"
        ></div>
        <div
            class="d-inline-block p-2 bg-secondary rounded-circle position-absolute right-40 top-25 ani-move z-index-1"
        ></div>
    </section>
</template>
