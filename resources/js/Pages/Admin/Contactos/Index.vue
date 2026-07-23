<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    contactos: {
        type: [Object, Array],
        default: () => ({ data: [], links: [] }),
    },
});

const search = ref('');

const rows = computed(() => {
    const source = Array.isArray(props.contactos)
        ? props.contactos
        : (props.contactos?.data ?? []);
    const q = search.value.trim().toLowerCase();
    if (!q) return source;
    return source.filter(
        (c) =>
            (c.ubicacion || '').toLowerCase().includes(q) ||
            (c.email || '').toLowerCase().includes(q) ||
            (c.telefono || '').toLowerCase().includes(q),
    );
});

const links = computed(() =>
    Array.isArray(props.contactos) ? [] : (props.contactos?.links ?? []),
);

const destroy = (id) => {
    if (!confirm('¿Eliminar este contacto?')) return;
    router.delete(route('admin.contactos.destroy', id));
};
</script>

<template>
    <Head title="Contactos — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Contactos</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Contactos</li>
        </template>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Datos de contacto</h3>
                <div class="card-tools d-flex gap-2">
                    <input
                        v-model="search"
                        type="search"
                        class="form-control form-control-sm"
                        placeholder="Buscar..."
                        style="width: 200px"
                    />
                    <Link :href="route('admin.contactos.create')" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-plus me-1"></i> Nuevo
                    </Link>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Ubicación</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Orden</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(contacto, index) in rows" :key="contacto.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ contacto.ubicacion }}</td>
                            <td>{{ contacto.telefono }}</td>
                            <td>{{ contacto.email || '—' }}</td>
                            <td>{{ contacto.orden }}</td>
                            <td class="text-end table-actions">
                                <Link
                                    :href="route('admin.contactos.edit', contacto.id)"
                                    class="btn btn-outline-primary btn-sm me-1"
                                    title="Editar"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </Link>
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    title="Eliminar"
                                    @click="destroy(contacto.id)"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!rows.length">
                            <td colspan="6" class="text-center text-muted py-4">
                                No se encontraron contactos
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
