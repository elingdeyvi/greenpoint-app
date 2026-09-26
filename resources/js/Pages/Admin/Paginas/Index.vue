<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PaginasTabs from '@/Components/Admin/PaginasTabs.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePermissions } from '@/composables/usePermissions';

const props = defineProps({
    pages: {
        type: Array,
        default: () => [],
    },
});

const { can } = usePermissions();

const visiblePages = computed(() => props.pages.filter((p) => can(p.permission)));

const pageIcons = {
    home: 'fa-house',
    nosotros: 'fa-users',
    historia: 'fa-clock-rotate-left',
    tecnologia: 'fa-microchip',
    aviso: 'fa-file-shield',
};

const openEdit = (page) => {
    router.visit(route(page.route));
};
</script>

<template>
    <Head title="Páginas — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Páginas del sitio</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Páginas</li>
        </template>

        <PaginasTabs />

        <div class="row g-3">
            <div
                v-for="page in visiblePages"
                :key="page.key"
                class="col-md-6 col-xl-4"
            >
                <button
                    type="button"
                    class="card h-100 w-100 text-start border shadow-sm page-hub-card"
                    @click="openEdit(page)"
                >
                    <div class="card-body d-flex gap-3 align-items-start">
                        <span
                            class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 2.75rem; height: 2.75rem"
                        >
                            <i
                                class="fa-solid"
                                :class="pageIcons[page.key] || 'fa-file-lines'"
                            ></i>
                        </span>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <h5 class="card-title mb-1">{{ page.titulo }}</h5>
                                <span
                                    class="badge"
                                    :class="
                                        page.estado ? 'text-bg-success' : 'text-bg-secondary'
                                    "
                                >
                                    {{ page.estado ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>
                            <p class="card-text text-muted small mb-0">
                                {{ page.descripcion }}
                            </p>
                        </div>
                    </div>
                </button>
            </div>

            <div v-if="!visiblePages.length" class="col-12">
                <div class="alert alert-secondary mb-0">
                    No tienes permisos para editar páginas.
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.page-hub-card {
    cursor: pointer;
    background: var(--bs-body-bg);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.page-hub-card:hover {
    border-color: var(--bs-success) !important;
    box-shadow: 0 0.35rem 1rem rgba(25, 135, 84, 0.12) !important;
}
</style>
