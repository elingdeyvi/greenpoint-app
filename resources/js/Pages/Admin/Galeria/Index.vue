<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    items: {
        type: [Object, Array],
        default: () => ({ data: [], links: [] }),
    },
    galeria: {
        type: [Object, Array],
        default: null,
    },
});

const search = ref('');
const collection = computed(() => props.galeria ?? props.items);

const rows = computed(() => {
    const source = Array.isArray(collection.value)
        ? collection.value
        : (collection.value?.data ?? []);
    const q = search.value.trim().toLowerCase();
    if (!q) return source;
    return source.filter(
        (g) =>
            (g.titulo || '').toLowerCase().includes(q) ||
            (g.descripcion || '').toLowerCase().includes(q),
    );
});

const links = computed(() =>
    Array.isArray(collection.value) ? [] : (collection.value?.links ?? []),
);

const destroy = (id) => {
    if (!confirm('¿Eliminar esta imagen de galería?')) return;
    router.delete(route('admin.galeria.destroy', id));
};
</script>

<template>
    <Head title="Galería — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Galería</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Galería</li>
        </template>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Imágenes de galería</h3>
                <div class="card-tools d-flex gap-2">
                    <input
                        v-model="search"
                        type="search"
                        class="form-control form-control-sm"
                        placeholder="Buscar..."
                        style="width: 200px"
                    />
                    <Link :href="route('admin.galeria.create')" class="btn btn-sm btn-primary">
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
                            <th>Título</th>
                            <th>Orden</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in rows" :key="row.id">
                            <td>{{ index + 1 }}</td>
                            <td>
                                <img
                                    v-if="row.imagen"
                                    :src="`/storage/${row.imagen}`"
                                    alt=""
                                    class="rounded"
                                    width="40"
                                    height="40"
                                    style="object-fit: cover"
                                />
                                <span v-else class="text-muted">—</span>
                            </td>
                            <td>{{ row.titulo }}</td>
                            <td>{{ row.orden }}</td>
                            <td>
                                <span
                                    class="badge"
                                    :class="row.activo ? 'text-bg-success' : 'text-bg-secondary'"
                                >
                                    {{ row.activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end table-actions">
                                <Link
                                    :href="route('admin.galeria.edit', row.id)"
                                    class="btn btn-outline-primary btn-sm me-1"
                                    title="Editar"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </Link>
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    title="Eliminar"
                                    @click="destroy(row.id)"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!rows.length">
                            <td colspan="6" class="text-center text-muted py-4">
                                No se encontraron imágenes
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
