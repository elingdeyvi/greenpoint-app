<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminModal from '@/Components/Admin/AdminModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    formularios: {
        type: [Object, Array],
        default: () => ({ data: [], links: [] }),
    },
});

const search = ref('');
const showDetailModal = ref(false);
const selected = ref(null);
const marking = ref(false);

const rows = computed(() => {
    const source = Array.isArray(props.formularios)
        ? props.formularios
        : (props.formularios?.data ?? []);
    const q = search.value.trim().toLowerCase();
    if (!q) return source;
    return source.filter(
        (f) =>
            (f.nombre || '').toLowerCase().includes(q) ||
            (f.email || '').toLowerCase().includes(q) ||
            (f.telefono || '').toLowerCase().includes(q),
    );
});

const links = computed(() =>
    Array.isArray(props.formularios) ? [] : (props.formularios?.links ?? []),
);

const formatDate = (value) => {
    if (!value) return '—';
    return new Date(value).toLocaleString('es-MX');
};

const openDetail = (row) => {
    selected.value = { ...row };
    showDetailModal.value = true;
};

const closeDetail = () => {
    showDetailModal.value = false;
    selected.value = null;
    marking.value = false;
};

const markAsRead = () => {
    if (!selected.value || selected.value.leido) return;
    marking.value = true;
    router.put(
        route('admin.formularios-contacto.update', selected.value.id),
        { leido: true },
        {
            preserveScroll: true,
            onSuccess: () => {
                selected.value = { ...selected.value, leido: true };
            },
            onFinish: () => {
                marking.value = false;
            },
        },
    );
};
</script>

<template>
    <Head title="Formularios de contacto — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Formularios de contacto</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Formularios</li>
        </template>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Mensajes recibidos</h3>
                <div class="card-tools">
                    <input
                        v-model="search"
                        type="search"
                        class="form-control form-control-sm"
                        placeholder="Buscar..."
                        style="width: 200px"
                    />
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in rows" :key="row.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ row.nombre }}</td>
                            <td>{{ row.email }}</td>
                            <td>{{ row.telefono || '—' }}</td>
                            <td>
                                <span
                                    class="badge"
                                    :class="row.leido ? 'text-bg-secondary' : 'text-bg-warning'"
                                >
                                    {{ row.leido ? 'Leído' : 'No leído' }}
                                </span>
                            </td>
                            <td>{{ formatDate(row.created_at) }}</td>
                            <td class="text-end table-actions">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm"
                                    title="Ver"
                                    @click="openDetail(row)"
                                >
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!rows.length">
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay mensajes
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

        <AdminModal
            :show="showDetailModal"
            title="Detalle del mensaje"
            size="lg"
            @close="closeDetail"
        >
            <template v-if="selected">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="mb-0">{{ selected.nombre }}</h5>
                    <span
                        class="badge"
                        :class="selected.leido ? 'text-bg-secondary' : 'text-bg-warning'"
                    >
                        {{ selected.leido ? 'Leído' : 'No leído' }}
                    </span>
                </div>
                <dl class="row mb-0">
                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9">{{ selected.email }}</dd>
                    <dt class="col-sm-3">Teléfono</dt>
                    <dd class="col-sm-9">{{ selected.telefono || '—' }}</dd>
                    <dt class="col-sm-3">Fecha</dt>
                    <dd class="col-sm-9">{{ formatDate(selected.created_at) }}</dd>
                    <dt class="col-sm-3">Mensaje</dt>
                    <dd class="col-sm-9">
                        <p class="mb-0" style="white-space: pre-wrap">{{ selected.mensaje }}</p>
                    </dd>
                </dl>
            </template>
            <template #footer>
                <button type="button" class="btn btn-secondary" @click="closeDetail">Cerrar</button>
                <button
                    v-if="selected && !selected.leido"
                    type="button"
                    class="btn btn-primary"
                    :disabled="marking"
                    @click="markAsRead"
                >
                    <span v-if="marking" class="spinner-border spinner-border-sm me-1" />
                    <i v-else class="fa-solid fa-check me-1"></i>
                    Marcar como leído
                </button>
            </template>
        </AdminModal>
    </AuthenticatedLayout>
</template>
