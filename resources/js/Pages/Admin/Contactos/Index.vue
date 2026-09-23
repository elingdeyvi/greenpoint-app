<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminModal from '@/Components/Admin/AdminModal.vue';
import ConfirmDeleteModal from '@/Components/Admin/ConfirmDeleteModal.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    contactos: {
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

const isEdit = computed(() => !!editing.value?.id);

const form = useForm({
    ubicacion: '',
    subtitulo: '',
    direccion: '',
    telefono: '',
    email: '',
    mapa_url: '',
    orden: 0,
});

const resetForm = (record = null) => {
    editing.value = record;
    form.clearErrors();
    form.reset();
    form.ubicacion = record?.ubicacion ?? '';
    form.subtitulo = record?.subtitulo ?? '';
    form.direccion = record?.direccion ?? '';
    form.telefono = record?.telefono ?? '';
    form.email = record?.email ?? '';
    form.mapa_url = record?.mapa_url ?? '';
    form.orden = record?.orden ?? 0;
};

const openCreate = () => {
    resetForm(null);
    showFormModal.value = true;
};

const openEdit = (contacto) => {
    resetForm(contacto);
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
        form.put(route('admin.contactos.update', editing.value.id), options);
    } else {
        form.post(route('admin.contactos.store'), options);
    }
};

const openDelete = (contacto) => {
    deleting.value = contacto;
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
    router.delete(route('admin.contactos.destroy', deleting.value.id), {
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
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm me-1"
                                    title="Editar"
                                    @click="openEdit(contacto)"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    title="Eliminar"
                                    @click="openDelete(contacto)"
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

        <AdminModal
            :show="showFormModal"
            :title="isEdit ? 'Editar contacto' : 'Nuevo contacto'"
            @close="closeFormModal"
        >
            <form id="contacto-form" @submit.prevent="submit">
                <div class="mb-3">
                    <label class="form-label">Ubicación</label>
                    <input
                        v-model="form.ubicacion"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.ubicacion }"
                        required
                    />
                    <div v-if="form.errors.ubicacion" class="invalid-feedback">
                        {{ form.errors.ubicacion }}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Subtítulo</label>
                    <input
                        v-model="form.subtitulo"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.subtitulo }"
                        placeholder="Ej. Villahermosa"
                    />
                    <div v-if="form.errors.subtitulo" class="invalid-feedback">
                        {{ form.errors.subtitulo }}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Dirección</label>
                    <textarea
                        v-model="form.direccion"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.direccion }"
                        rows="3"
                    />
                    <div v-if="form.errors.direccion" class="invalid-feedback">
                        {{ form.errors.direccion }}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Teléfono</label>
                    <input
                        v-model="form.telefono"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.telefono }"
                    />
                    <div v-if="form.errors.telefono" class="invalid-feedback">
                        {{ form.errors.telefono }}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.email }"
                    />
                    <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">URL embed del mapa (iframe Google Maps)</label>
                    <textarea
                        v-model="form.mapa_url"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.mapa_url }"
                        rows="3"
                        placeholder="https://www.google.com/maps/embed?pb=..."
                    />
                    <div v-if="form.errors.mapa_url" class="invalid-feedback">
                        {{ form.errors.mapa_url }}
                    </div>
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
                    form="contacto-form"
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
            :message="`¿Eliminar el contacto «${deleting?.ubicacion ?? ''}»?`"
            :processing="deletingProcessing"
            @close="closeDeleteModal"
            @confirm="confirmDelete"
        />
    </AuthenticatedLayout>
</template>
