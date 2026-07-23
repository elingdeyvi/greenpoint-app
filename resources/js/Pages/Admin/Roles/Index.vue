<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

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

const selected = reactive({});

const syncSelected = () => {
    props.roles.forEach((role) => {
        selected[role.id] = (role.permissions ?? []).map((p) => p.name);
    });
};

syncSelected();

watch(
    () => props.roles,
    () => syncSelected(),
    { deep: true },
);

const togglePermission = (roleId, permName, checked) => {
    const list = selected[roleId] ?? [];
    if (checked) {
        if (!list.includes(permName)) {
            selected[roleId] = [...list, permName];
        }
    } else {
        selected[roleId] = list.filter((n) => n !== permName);
    }
};

const isChecked = (roleId, permName) => (selected[roleId] ?? []).includes(permName);

const saveRole = (role) => {
    router.put(route('admin.roles.update', role.id), {
        permissions: selected[role.id] ?? [],
    });
};
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

        <div class="row">
            <div v-for="role in roles" :key="role.id" class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">{{ role.name }}</h3>
                    </div>
                    <div class="card-body" style="max-height: 320px; overflow-y: auto">
                        <div
                            v-for="perm in permissions"
                            :key="`${role.id}-${perm.id}`"
                            class="form-check mb-1"
                        >
                            <input
                                :id="`role-${role.id}-perm-${perm.id}`"
                                class="form-check-input"
                                type="checkbox"
                                :checked="isChecked(role.id, perm.name)"
                                @change="
                                    togglePermission(role.id, perm.name, $event.target.checked)
                                "
                            />
                            <label
                                class="form-check-label"
                                :for="`role-${role.id}-perm-${perm.id}`"
                            >
                                {{ perm.name }}
                            </label>
                        </div>
                        <p v-if="!permissions.length" class="text-muted mb-0">
                            No hay permisos registrados
                        </p>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary btn-sm" @click="saveRole(role)">
                            Guardar permisos
                        </button>
                    </div>
                </div>
            </div>
            <div v-if="!roles.length" class="col-12">
                <div class="alert alert-info mb-0">No hay roles configurados</div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
