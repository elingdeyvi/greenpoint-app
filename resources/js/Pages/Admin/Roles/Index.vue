<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminModal from '@/Components/Admin/AdminModal.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    roles: {
        type: Array,
        default: () => [],
    },
    permissions: {
        type: Array,
        default: () => [],
    },
});

const showFormModal = ref(false);
const editing = ref(null);

const form = useForm({
    permissions: [],
});

const isEdit = computed(() => !!editing.value?.id);

const openEdit = (role) => {
    editing.value = role;
    form.clearErrors();
    form.permissions = (role.permissions ?? []).map((p) => p.name);
    showFormModal.value = true;
};

const closeFormModal = () => {
    showFormModal.value = false;
    editing.value = null;
    form.reset();
    form.clearErrors();
};

const togglePermission = (permName, checked) => {
    if (checked) {
        if (!form.permissions.includes(permName)) {
            form.permissions = [...form.permissions, permName];
        }
    } else {
        form.permissions = form.permissions.filter((n) => n !== permName);
    }
};

const submit = () => {
    if (!editing.value) return;
    form.put(route('admin.roles.update', editing.value.id), {
        preserveScroll: true,
        onSuccess: () => closeFormModal(),
    });
};

watch(showFormModal, (open) => {
    if (!open) form.clearErrors();
});
</script>

<template>
    <Head title="Roles y permisos — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Roles y permisos</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Roles</li>
        </template>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Roles del sistema</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Rol</th>
                            <th>Permisos</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(role, index) in roles" :key="role.id">
                            <td>{{ index + 1 }}</td>
                            <td class="fw-semibold">{{ role.name }}</td>
                            <td>
                                <span class="badge text-bg-secondary">
                                    {{ (role.permissions ?? []).length }} permiso(s)
                                </span>
                            </td>
                            <td class="text-end">
                                <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm"
                                    title="Editar permisos"
                                    @click="openEdit(role)"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!roles.length">
                            <td colspan="4" class="text-center text-muted py-4">
                                No hay roles configurados
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <AdminModal
            :show="showFormModal"
            :title="`Permisos — ${editing?.name ?? ''}`"
            size="lg"
            @close="closeFormModal"
        >
            <form id="role-form" @submit.prevent="submit">
                <div v-if="form.errors.permissions" class="alert alert-danger py-2">
                    {{ form.errors.permissions }}
                </div>
                <div class="row">
                    <div
                        v-for="perm in permissions"
                        :key="perm.id"
                        class="col-md-6"
                    >
                        <div class="form-check mb-2">
                            <input
                                :id="`modal-perm-${perm.id}`"
                                class="form-check-input"
                                type="checkbox"
                                :checked="form.permissions.includes(perm.name)"
                                @change="togglePermission(perm.name, $event.target.checked)"
                            />
                            <label class="form-check-label" :for="`modal-perm-${perm.id}`">
                                {{ perm.name }}
                            </label>
                        </div>
                    </div>
                </div>
                <p v-if="!permissions.length" class="text-muted mb-0">No hay permisos registrados</p>
            </form>
            <template #footer>
                <button type="button" class="btn btn-secondary" @click="closeFormModal">Cancelar</button>
                <button
                    type="submit"
                    form="role-form"
                    class="btn btn-primary"
                    :disabled="form.processing || !isEdit"
                >
                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-1" />
                    Guardar permisos
                </button>
            </template>
        </AdminModal>
    </AuthenticatedLayout>
</template>
