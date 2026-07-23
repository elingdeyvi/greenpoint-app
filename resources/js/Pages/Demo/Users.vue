<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
});

const search = ref('');

const filteredUsers = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (!q) return props.users;
    return props.users.filter(
        (u) =>
            u.name.toLowerCase().includes(q) ||
            u.email.toLowerCase().includes(q) ||
            u.role.toLowerCase().includes(q),
    );
});

const roleBadge = (role) => {
    if (role === 'Admin') return 'primary';
    if (role === 'Editor') return 'info';
    return 'secondary';
};
</script>

<template>
    <Head title="Usuarios" />

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
                    <button type="button" class="btn btn-sm btn-primary">
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
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user, index) in filteredUsers" :key="user.id">
                            <td>{{ index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img
                                        :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`"
                                        class="rounded-circle"
                                        width="32"
                                        height="32"
                                        alt=""
                                    />
                                    <span>{{ user.name }}</span>
                                </div>
                            </td>
                            <td>{{ user.email }}</td>
                            <td>
                                <span class="badge" :class="`text-bg-${roleBadge(user.role)}`">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="badge"
                                    :class="user.active ? 'text-bg-success' : 'text-bg-secondary'"
                                >
                                    {{ user.active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-end table-actions">
                                <button class="btn btn-outline-primary btn-sm me-1" title="Editar">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" title="Eliminar">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!filteredUsers.length">
                            <td colspan="6" class="text-center text-muted py-4">
                                No se encontraron usuarios
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                <span class="text-muted">{{ filteredUsers.length }} registro(s)</span>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
