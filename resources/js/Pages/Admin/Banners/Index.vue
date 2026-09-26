<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminModal from '@/Components/Admin/AdminModal.vue';
import ConfirmDeleteModal from '@/Components/Admin/ConfirmDeleteModal.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    banners: {
        type: [Object, Array],
        default: () => ({ data: [], links: [] }),
    },
});

const search = ref('');
const showFormModal = ref(false);
const showDeleteModal = ref(false);
const editing = ref(null);
const deleting = ref(null);
const previewUrl = ref(null);
const deletingProcessing = ref(false);

const rows = computed(() => {
    const source = Array.isArray(props.banners) ? props.banners : (props.banners?.data ?? []);
    const q = search.value.trim().toLowerCase();
    if (!q) return source;
    return source.filter((b) => (b.titulo || '').toLowerCase().includes(q));
});

const links = computed(() =>
    Array.isArray(props.banners) ? [] : (props.banners?.links ?? []),
);

const isEdit = computed(() => !!editing.value?.id);

const form = useForm({
    titulo: '',
    descripcion: '',
    imagen: null,
    enlace: '',
    orden: 0,
    activo: true,
});

const resetForm = (record = null) => {
    editing.value = record;
    previewUrl.value = null;
    form.clearErrors();
    form.reset();
    form.titulo = record?.titulo ?? '';
    form.descripcion = record?.descripcion ?? '';
    form.imagen = null;
    form.enlace = record?.enlace ?? '';
    form.orden = record?.orden ?? 0;
    form.activo = record?.activo ?? true;
};

const openCreate = () => {
    resetForm(null);
    showFormModal.value = true;
};

const openEdit = (banner) => {
    resetForm(banner);
    showFormModal.value = true;
};

const closeFormModal = () => {
    showFormModal.value = false;
    resetForm(null);
};

const onFileChange = (e) => {
    const file = e.target.files?.[0] ?? null;
    form.imagen = file;
    previewUrl.value = file ? URL.createObjectURL(file) : null;
};

const submit = () => {
    const options = {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => closeFormModal(),
        onFinish: () => form.transform((data) => data),
    };

    if (isEdit.value) {
        form.transform((data) => ({
            ...data,
            activo: data.activo ? 1 : 0,
            _method: 'put',
        })).post(route('admin.banners.update', editing.value.id), options);
    } else {
        form.transform((data) => ({
            ...data,
            activo: data.activo ? 1 : 0,
        })).post(route('admin.banners.store'), options);
    }
};

const openDelete = (banner) => {
    deleting.value = banner;
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
    router.delete(route('admin.banners.destroy', deleting.value.id), {
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
    <Head title="Banners — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Banners</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Banners</li>
        </template>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de banners</h3>
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
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>Enlace</th>
                            <th>Orden</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(banner, index) in rows" :key="banner.id">
                            <td>{{ index + 1 }}</td>
                            <td>
                                <img
                                    v-if="banner.imagen"
                                    :src="`/storage/${banner.imagen}`"
                                    alt=""
                                    class="rounded"
                                    width="64"
                                    height="36"
                                    style="object-fit: cover"
                                />
                                <span v-else class="text-muted">—</span>
                            </td>
                            <td>{{ banner.titulo }}</td>
                            <td>
                                <a
                                    v-if="banner.enlace"
                                    :href="banner.enlace"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    {{ banner.enlace }}
                                </a>
                                <span v-else class="text-muted">—</span>
                            </td>
                            <td>{{ banner.orden }}</td>
                            <td>
                                <span
                                    class="badge"
                                    :class="banner.activo ? 'text-bg-success' : 'text-bg-secondary'"
                                >
                                    {{ banner.activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end table-actions">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm me-1"
                                    title="Editar"
                                    @click="openEdit(banner)"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    title="Eliminar"
                                    @click="openDelete(banner)"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!rows.length">
                            <td colspan="7" class="text-center text-muted py-4">
                                No se encontraron banners
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
            :title="isEdit ? 'Editar banner' : 'Nuevo banner'"
            @close="closeFormModal"
        >
            <form id="banner-form" @submit.prevent="submit">
                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input
                        v-model="form.titulo"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.titulo }"
                        required
                    />
                    <div v-if="form.errors.titulo" class="invalid-feedback">{{ form.errors.titulo }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea
                        v-model="form.descripcion"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.descripcion }"
                        rows="2"
                        placeholder="Texto del banner (opcional)"
                    ></textarea>
                    <div v-if="form.errors.descripcion" class="invalid-feedback">{{ form.errors.descripcion }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Imagen</label>
                    <input
                        type="file"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.imagen }"
                        accept="image/*"
                        @change="onFileChange"
                    />
                    <div v-if="form.errors.imagen" class="invalid-feedback">{{ form.errors.imagen }}</div>
                    <div v-if="previewUrl || editing?.imagen" class="mt-2">
                        <img
                            :src="previewUrl || `/storage/${editing.imagen}`"
                            alt="Vista previa"
                            class="img-thumbnail"
                            style="max-height: 140px"
                        />
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Enlace</label>
                    <input
                        v-model="form.enlace"
                        type="url"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.enlace }"
                    />
                    <div v-if="form.errors.enlace" class="invalid-feedback">{{ form.errors.enlace }}</div>
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
                <div class="form-check">
                    <input id="banner-activo" v-model="form.activo" class="form-check-input" type="checkbox" />
                    <label class="form-check-label" for="banner-activo">Activo</label>
                </div>
            </form>
            <template #footer>
                <button type="button" class="btn btn-secondary" @click="closeFormModal">Cancelar</button>
                <button
                    type="submit"
                    form="banner-form"
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
            :message="`¿Eliminar el banner «${deleting?.titulo ?? ''}»?`"
            :processing="deletingProcessing"
            @close="closeDeleteModal"
            @confirm="confirmDelete"
        />
    </AuthenticatedLayout>
</template>
