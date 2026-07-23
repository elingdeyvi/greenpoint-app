<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});

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

const submit = () => {
    form.put(route('admin.configuracion.update'));
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

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Parámetros del sitio</h3>
            </div>
            <form @submit.prevent="submit">
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 30%">Clave</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in form.items" :key="row.id || row.clave">
                                <td>
                                    <code>{{ row.clave }}</code>
                                    <input type="hidden" :value="row.clave" />
                                </td>
                                <td>
                                    <input
                                        v-model="form.items[index].valor"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': form.errors[`items.${index}.valor`],
                                        }"
                                    />
                                    <div
                                        v-if="form.errors[`items.${index}.valor`]"
                                        class="invalid-feedback"
                                    >
                                        {{ form.errors[`items.${index}.valor`] }}
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!form.items.length">
                                <td colspan="2" class="text-center text-muted py-4">
                                    No hay claves de configuración
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
