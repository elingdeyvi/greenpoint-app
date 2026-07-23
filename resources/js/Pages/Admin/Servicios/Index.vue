<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    servicios: {
        type: [Object, Array],
        default: () => ({ data: [], links: [] }),
    },
});

const search = ref('');

const rows = computed(() => {
    const source = Array.isArray(props.servicios) ? props.servicios : (props.servicios?.data ?? []);
    const q = search.value.trim().toLowerCase();
    if (!q) return source;
    return source.filter(
        (s) =>
            (s.nombre || '').toLowerCase().includes(q) ||
            (s.descripcion || '').toLowerCase().includes(q),
    );
});

const links = computed(() =>
    Array.isArray(props.servicios) ? [] : (props.servicios?.links ?? []),
);

const destroy = (id) => {
    if (!confirm('¿Eliminar este servicio?')) return;
    router.delete(route('admin.servicios.destroy', id));
};
</script>

<template>
    <Head title="Servicios — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Servicios</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Servicios</li>
        </template>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Catálogo de servicios</h3>
                <div class="card-tools d-flex gap-2">
                    <input
                        v-model="search"
                        type="search"
                        class="form-control form-control-sm"
                        placeholder="Buscar..."
                        style="width: 200px"
                    />
                    <Link :href="route('admin.servicios.create')" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-plus me-1"></i> Nuevo
                    </Link>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Orden</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(servicio, index) in rows" :key="servicio.id">
                            <td>{{ index + 1 }}</td>
                            <td>
                                <img
                                    v-if="servicio.imagen"
                                    :src="`/storage/${servicio.imagen}`"
                                    alt=""
                                    class="rounded"
                                    width="40"
                                    height="40"
                                    style="object-fit: cover"
                                />
                                <span v-else class="text-muted">—</span>
                            </td>
                            <td>{{ servicio.nombre }}</td>
                            <td>{{ servicio.orden }}</td>
                            <td>
                                <span
                                    class="badge"
                                    :class="servicio.activo ? 'text-bg-success' : 'text-bg-secondary'"
                                >
                                    {{ servicio.activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end table-actions">
                                <Link
                                    :href="route('admin.servicios.edit', servicio.id)"
                                    class="btn btn-outline-primary btn-sm me-1"
                                    title="Editar"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </Link>
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    title="Eliminar"
                                    @click="destroy(servicio.id)"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!rows.length">
                            <td colspan="6" class="text-center text-muted py-4">
                                No se encontraron servicios
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix d-flex justify-content-between align-items-center">
                <span class="text-muted">{{ rows.length }} registro(s)</span>
                <ul v-if="links.length > 3" class="pagination pagination-sm mb-0">
                    <li
                        v-for="(link, i) in links"
                        :key="i"
                        class="page-item"
                        :class="{ active: link.active, disabled: !link.url }"
                    >
                        <Link
                            v-if="link.url"
                            class="page-link"
                            :href="link.url"
                            v-html="link.label"
                        />
                        <span v-else class="page-link" v-html="link.label" />
                    </li>
                </ul>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
