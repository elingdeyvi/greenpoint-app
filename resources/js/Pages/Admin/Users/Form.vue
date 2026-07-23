<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
    item: {
        type: Object,
        default: null,
    },
    roles: {
        type: Array,
        default: () => [],
    },
});

const record = computed(() => props.user ?? props.item);
const isEdit = computed(() => !!record.value?.id);

const form = useForm({
    name: record.value?.name ?? '',
    email: record.value?.email ?? '',
    password: '',
    password_confirmation: '',
    role: record.value?.roles?.[0]?.name ?? props.roles?.[0]?.name ?? '',
    estatus: record.value?.estatus ?? 'activo',
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('admin.users.update', record.value.id));
    } else {
        form.post(route('admin.users.store'));
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Editar usuario — GreenPoint' : 'Nuevo usuario — GreenPoint'" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">{{ isEdit ? 'Editar usuario' : 'Nuevo usuario' }}</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item">
                <Link :href="route('admin.users.index')">Usuarios</Link>
            </li>
            <li class="breadcrumb-item active">{{ isEdit ? 'Editar' : 'Nuevo' }}</li>
        </template>

        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Datos del usuario</h3>
                    </div>
                    <form @submit.prevent="submit">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.name }"
                                    required
                                />
                                <div v-if="form.errors.name" class="invalid-feedback">
                                    {{ form.errors.name }}
                                </div>
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
                                <div v-if="form.errors.email" class="invalid-feedback">
                                    {{ form.errors.email }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    Contraseña
                                    <span v-if="isEdit" class="text-muted fw-normal">
                                        (dejar vacío para no cambiar)
                                    </span>
                                </label>
                                <input
                                    v-model="form.password"
                                    type="password"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.password }"
                                    :required="!isEdit"
                                    autocomplete="new-password"
                                />
                                <div v-if="form.errors.password" class="invalid-feedback">
                                    {{ form.errors.password }}
                                </div>
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
                                    <option
                                        v-for="role in roles"
                                        :key="role.id"
                                        :value="role.name"
                                    >
                                        {{ role.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.role" class="invalid-feedback">
                                    {{ form.errors.role }}
                                </div>
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
                                <div v-if="form.errors.estatus" class="invalid-feedback">
                                    {{ form.errors.estatus }}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="form.processing"
                            >
                                Guardar
                            </button>
                            <Link
                                :href="route('admin.users.index')"
                                class="btn btn-secondary ms-2"
                            >
                                Cancelar
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
