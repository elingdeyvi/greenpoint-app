<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminModal from '@/Components/Admin/AdminModal.vue';
import ConfirmDeleteModal from '@/Components/Admin/ConfirmDeleteModal.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    redes: {
        type: [Object, Array],
        default: () => ({ data: [], links: [] }),
    },
});

const search = ref('');
const showFormModal = ref(false);
const showDeleteModal = ref(false);
const editing = ref(null);
const deleting = ref(null);
const deletingProcessing = ref(false);

const rows = computed(() => {
    const source = Array.isArray(props.redes) ? props.redes : (props.redes?.data ?? []);
    const q = search.value.trim().toLowerCase();
    if (!q) return source;
    return source.filter(
        (r) =>
            (r.nombre || '').toLowerCase().includes(q) ||
            (r.url || '').toLowerCase().includes(q),
    );
});

const links = computed(() =>
    Array.isArray(props.redes) ? [] : (props.redes?.links ?? []),
);

const isEdit = computed(() => !!editing.value?.id);

const form = useForm({
    nombre: '',
    url: '',
    icono: '',
    orden: 0,
});

const resetForm = (record = null) => {
    editing.value = record;
    form.clearErrors();
    form.reset();
    form.nombre = record?.nombre ?? '';
    form.url = record?.url ?? '';
    form.icono = record?.icono ?? '';
    form.orden = record?.orden ?? 0;
};

const openCreate = () => {
    resetForm(null);
    showFormModal.value = true;
};

const openEdit = (red) => {
    resetForm(red);
    showFormModal.value = true;
};

const closeFormModal = () => {
    showFormModal.value = false;
    resetForm(null);
};

const submit = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => closeFormModal(),
    };

    if (isEdit.value) {
        form.put(route('admin.redes-sociales.update', editing.value.id), options);
    } else {
        form.post(route('admin.redes-sociales.store'), options);
    }
};

const openDelete = (red) => {
    deleting.value = red;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    deleting.value = null;
    deletingProcessing.value = false;
};

const confirmDelete = () => {
    if (!deleting.value) return;
    deletingProcessing.value = true;
    router.delete(route('admin.redes-sociales.destroy', deleting.value.id), {
        preserveScroll: true,
        onFinish: () => closeDeleteModal(),
    });
};

watch(showFormModal, (open) => {
    if (!open) {
        form.clearErrors();
    }
});
</script>

<template>
    <Head title="Redes sociales — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Redes sociales</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Redes sociales</li>
        </template>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de redes</h3>
                <div class="card-tools d-flex gap-2">
                    <input
                        v-model="search"
                        type="search"
                        class="form-control form-control-sm"
                        placeholder="Buscar..."
                        style="width: 200px"
                    />
                    <button type="button" class="btn btn-sm btn-primary" @click="openCreate">
                        <i class="fa-solid fa-plus me-1"></i> Nuevo
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Icono</th>
                            <th>Nombre</th>
                            <th>URL</th>
                            <th>Orden</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(red, index) in rows" :key="red.id">
                            <td>{{ index + 1 }}</td>
                            <td>
                                <i v-if="red.icono" :class="red.icono"></i>
                                <span v-else class="text-muted">—</span>
                            </td>
                            <td>{{ red.nombre }}</td>
                            <td>
                                <a :href="red.url" target="_blank" rel="noopener">{{ red.url }}</a>
                            </td>
                            <td>{{ red.orden }}</td>
                            <td class="text-end table-actions">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm me-1"
                                    title="Editar"
                                    @click="openEdit(red)"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    title="Eliminar"
                                    @click="openDelete(red)"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!rows.length">
                            <td colspan="6" class="text-center text-muted py-4">
                                No se encontraron redes sociales
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
            :show="showFormModal"
            :title="isEdit ? 'Editar red social' : 'Nueva red social'"
            @close="closeFormModal"
        >
            <form id="red-social-form" @submit.prevent="submit">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input
                        v-model="form.nombre"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.nombre }"
                        required
                    />
                    <div v-if="form.errors.nombre" class="invalid-feedback">{{ form.errors.nombre }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">URL</label>
                    <input
                        v-model="form.url"
                        type="url"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.url }"
                        required
                    />
                    <div v-if="form.errors.url" class="invalid-feedback">{{ form.errors.url }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Icono (clase CSS)</label>
                    <input
                        v-model="form.icono"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.icono }"
                        placeholder="ej. fa-brands fa-facebook"
                    />
                    <div v-if="form.errors.icono" class="invalid-feedback">{{ form.errors.icono }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Orden</label>
                    <input
                        v-model.number="form.orden"
                        type="number"
                        min="0"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.orden }"
                    />
                    <div v-if="form.errors.orden" class="invalid-feedback">{{ form.errors.orden }}</div>
                </div>
            </form>
            <template #footer>
                <button type="button" class="btn btn-secondary" @click="closeFormModal">Cancelar</button>
                <button
                    type="submit"
                    form="red-social-form"
                    class="btn btn-primary"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-1" />
                    Guardar
                </button>
            </template>
        </AdminModal>

        <ConfirmDeleteModal
            :show="showDeleteModal"
            :message="`¿Eliminar la red social «${deleting?.nombre ?? ''}»?`"
            :processing="deletingProcessing"
            @close="closeDeleteModal"
            @confirm="confirmDelete"
        />
    </AuthenticatedLayout>
</template>
