<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { usePermissions } from '@/composables/usePermissions';

defineProps({
    pages: {
        type: Array,
        default: () => [],
    },
});

const { can } = usePermissions();

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

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Contenido editable</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Página</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="page in pages.filter((p) => can(p.permission))"
                            :key="page.key"
                        >
                            <td class="fw-semibold">{{ page.titulo }}</td>
                            <td class="text-muted">{{ page.descripcion }}</td>
                            <td>
                                <span
                                    class="badge"
                                    :class="page.estado ? 'text-bg-success' : 'text-bg-secondary'"
                                >
                                    {{ page.estado ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm"
                                    title="Editar"
                                    @click="openEdit(page)"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!pages.filter((p) => can(p.permission)).length">
                            <td colspan="4" class="text-center text-muted py-4">
                                No tienes permisos para editar páginas
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
