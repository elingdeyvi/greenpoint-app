<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const roles = computed(() => page.props.auth.roles ?? []);
const activeTab = ref('info');

const avatarUrl = computed(
    () =>
        `https://ui-avatars.com/api/?name=${encodeURIComponent(user.value?.name || 'U')}&background=198754&color=fff&size=128`,
);

const estatusBadge = computed(() => {
    const estatus = user.value?.estatus || 'activo';
    if (estatus === 'activo') return 'text-bg-success';
    if (estatus === 'suspendido') return 'text-bg-warning';
    return 'text-bg-secondary';
});
</script>

<template>
    <Head title="Perfil" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Mi perfil</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Perfil</li>
        </template>

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img
                            :src="avatarUrl"
                            alt="Avatar"
                            class="rounded-circle shadow mb-3"
                            width="96"
                            height="96"
                        />
                        <h4 class="mb-1">{{ user?.name }}</h4>
                        <p class="text-muted mb-3">{{ user?.email }}</p>

                        <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                            <span
                                v-for="role in roles"
                                :key="role"
                                class="badge text-bg-primary"
                            >
                                {{ role }}
                            </span>
                            <span
                                v-if="user?.estatus"
                                class="badge"
                                :class="estatusBadge"
                            >
                                {{ user.estatus }}
                            </span>
                        </div>

                        <ul class="list-group list-group-flush text-start">
                            <li
                                class="list-group-item d-flex justify-content-between px-0"
                            >
                                <span class="text-muted">Cuenta</span>
                                <span class="fw-semibold">{{ user?.email }}</span>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between px-0"
                            >
                                <span class="text-muted">Verificado</span>
                                <span
                                    class="badge"
                                    :class="
                                        user?.email_verified_at
                                            ? 'text-bg-success'
                                            : 'text-bg-secondary'
                                    "
                                >
                                    {{
                                        user?.email_verified_at
                                            ? 'Sí'
                                            : 'Pendiente'
                                    }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs px-3 pt-2">
                            <li class="nav-item">
                                <button
                                    type="button"
                                    class="nav-link"
                                    :class="{ active: activeTab === 'info' }"
                                    @click="activeTab = 'info'"
                                >
                                    <i class="fa-solid fa-user me-1"></i>
                                    Información
                                </button>
                            </li>
                            <li class="nav-item">
                                <button
                                    type="button"
                                    class="nav-link"
                                    :class="{ active: activeTab === 'password' }"
                                    @click="activeTab = 'password'"
                                >
                                    <i class="fa-solid fa-key me-1"></i>
                                    Contraseña
                                </button>
                            </li>
                            <li class="nav-item">
                                <button
                                    type="button"
                                    class="nav-link text-danger"
                                    :class="{ active: activeTab === 'danger' }"
                                    @click="activeTab = 'danger'"
                                >
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                    Zona peligrosa
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <UpdateProfileInformationForm
                            v-show="activeTab === 'info'"
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />
                        <UpdatePasswordForm v-show="activeTab === 'password'" />
                        <DeleteUserForm v-show="activeTab === 'danger'" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
