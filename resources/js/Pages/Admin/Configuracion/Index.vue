<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminModal from '@/Components/Admin/AdminModal.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});

const showFormModal = ref(false);

const form = useForm({
    items: props.items.map((i) => ({
        id: i.id,
        clave: i.clave,
        valor: i.valor ?? '',
    })),
});

watch(
    () => props.items,
    (items) => {
        form.items = items.map((i) => ({
            id: i.id,
            clave: i.clave,
            valor: i.valor ?? '',
        }));
    },
    { deep: true },
);

const openEdit = () => {
    form.clearErrors();
    form.items = props.items.map((i) => ({
        id: i.id,
        clave: i.clave,
        valor: i.valor ?? '',
    }));
    showFormModal.value = true;
};

const closeFormModal = () => {
    showFormModal.value = false;
};

const submit = () => {
    form.put(route('admin.configuracion.update'), {
        preserveScroll: true,
        onSuccess: () => closeFormModal(),
    });
};
</script>

<template>
    <Head title="Configuración — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Configuración</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Configuración</li>
        </template>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Parámetros del sitio</h3>
                <button type="button" class="btn btn-sm btn-primary" @click="openEdit">
                    <i class="fa-solid fa-pen me-1"></i> Editar
                </button>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 35%">Clave</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in items" :key="row.id || row.clave">
                            <td><code>{{ row.clave }}</code></td>
                            <td>{{ row.valor || '—' }}</td>
                        </tr>
                        <tr v-if="!items.length">
                            <td colspan="2" class="text-center text-muted py-4">
                                No hay claves de configuración
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <AdminModal
            :show="showFormModal"
            title="Editar configuración"
            size="lg"
            @close="closeFormModal"
        >
            <form id="config-form" @submit.prevent="submit">
                <div
                    v-for="(row, index) in form.items"
                    :key="row.id || row.clave"
                    class="mb-3"
                >
                    <label class="form-label"><code>{{ row.clave }}</code></label>
                    <input type="hidden" :value="row.clave" />
                    <input
                        v-model="form.items[index].valor"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': form.errors[`items.${index}.valor`] }"
                    />
                    <div
                        v-if="form.errors[`items.${index}.valor`]"
                        class="invalid-feedback"
                    >
                        {{ form.errors[`items.${index}.valor`] }}
                    </div>
                </div>
                <p v-if="!form.items.length" class="text-muted mb-0">
                    No hay claves de configuración
                </p>
            </form>
            <template #footer>
                <button type="button" class="btn btn-secondary" @click="closeFormModal">Cancelar</button>
                <button
                    type="submit"
                    form="config-form"
                    class="btn btn-primary"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-1" />
                    Guardar cambios
                </button>
            </template>
        </AdminModal>
    </AuthenticatedLayout>
</template>
