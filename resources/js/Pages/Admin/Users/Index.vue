<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminModal from '@/Components/Admin/AdminModal.vue';
import ConfirmDeleteModal from '@/Components/Admin/ConfirmDeleteModal.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    users: {
        type: [Object, Array],
        default: () => ({ data: [], links: [] }),
    },
    roles: {
        type: Array,
        default: () => [],
    },
});

const search = ref('');
const showFormModal = ref(false);
const showDeleteModal = ref(false);
const editing = ref(null);
const deleting = ref(null);
const deletingProcessing = ref(false);

const rows = computed(() => {
    const source = Array.isArray(props.users) ? props.users : (props.users?.data ?? []);
    const q = search.value.trim().toLowerCase();
    if (!q) return source;
    return source.filter(
        (u) =>
            (u.name || '').toLowerCase().includes(q) ||
            (u.email || '').toLowerCase().includes(q),
    );
});

const links = computed(() =>
    Array.isArray(props.users) ? [] : (props.users?.links ?? []),
);

const isEdit = computed(() => !!editing.value?.id);

const roleName = (user) => user.roles?.[0]?.name ?? '—';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
    estatus: 'activo',
});

const resetForm = (record = null) => {
    editing.value = record;
    form.clearErrors();
    form.reset();
    form.name = record?.name ?? '';
    form.email = record?.email ?? '';
    form.password = '';
    form.password_confirmation = '';
    form.role = record?.roles?.[0]?.name ?? props.roles?.[0]?.name ?? '';
    form.estatus = record?.estatus ?? 'activo';
};

const openCreate = () => {
    resetForm(null);
    showFormModal.value = true;
};

const openEdit = (user) => {
    resetForm(user);
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
        form.put(route('admin.users.update', editing.value.id), options);
    } else {
        form.post(route('admin.users.store'), options);
    }
};

const openDelete = (user) => {
    deleting.value = user;
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
    router.delete(route('admin.users.destroy', deleting.value.id), {
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
    <Head title="Usuarios — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Usuarios</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Usuarios</li>
        </template>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Listado de usuarios</h3>
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
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estatus</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user, index) in rows" :key="user.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>
                                <span class="badge text-bg-primary">{{ roleName(user) }}</span>
                            </td>
                            <td>
                                <span
                                    class="badge"
                                    :class="
                                        user.estatus === 'activo'
                                            ? 'text-bg-success'
                                            : user.estatus === 'suspendido'
                                              ? 'text-bg-warning'
                                              : 'text-bg-secondary'
                                    "
                                >
                                    {{ user.estatus }}
                                </span>
                            </td>
                            <td class="text-end table-actions">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm me-1"
                                    title="Editar"
                                    @click="openEdit(user)"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    title="Desactivar"
                                    @click="openDelete(user)"
                                >
                                    <i class="fa-solid fa-user-slash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!rows.length">
                            <td colspan="6" class="text-center text-muted py-4">
                                No se encontraron usuarios
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
            :title="isEdit ? 'Editar usuario' : 'Nuevo usuario'"
            @close="closeFormModal"
        >
            <form id="user-form" @submit.prevent="submit">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input
                        v-model="form.name"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.name }"
                        required
                    />
                    <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.email }"
                        required
                    />
                    <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        Contraseña
                        <span v-if="isEdit" class="text-muted fw-normal">(dejar vacío para no cambiar)</span>
                    </label>
                    <input
                        v-model="form.password"
                        type="password"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors.password }"
                        :required="!isEdit"
                        autocomplete="new-password"
                    />
                    <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmar contraseña</label>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        class="form-control"
                        :required="!isEdit && !!form.password"
                        autocomplete="new-password"
                    />
                </div>
                <div class="mb-3">
                    <label class="form-label">Rol</label>
                    <select
                        v-model="form.role"
                        class="form-select"
                        :class="{ 'is-invalid': form.errors.role }"
                        required
                    >
                        <option v-for="role in roles" :key="role.id" :value="role.name">
                            {{ role.name }}
                        </option>
                    </select>
                    <div v-if="form.errors.role" class="invalid-feedback">{{ form.errors.role }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estatus</label>
                    <select
                        v-model="form.estatus"
                        class="form-select"
                        :class="{ 'is-invalid': form.errors.estatus }"
                    >
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="suspendido">Suspendido</option>
                    </select>
                    <div v-if="form.errors.estatus" class="invalid-feedback">{{ form.errors.estatus }}</div>
                </div>
            </form>
            <template #footer>
                <button type="button" class="btn btn-secondary" @click="closeFormModal">Cancelar</button>
                <button
                    type="submit"
                    form="user-form"
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
            title="Confirmar desactivación"
            :message="`¿Desactivar al usuario «${deleting?.name ?? ''}»?`"
            confirm-label="Desactivar"
            :processing="deletingProcessing"
            @close="closeDeleteModal"
            @confirm="confirmDelete"
        />
    </AuthenticatedLayout>
</template>
