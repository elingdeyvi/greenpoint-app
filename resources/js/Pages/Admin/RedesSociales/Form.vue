<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    redSocial: {
        type: Object,
        default: null,
    },
    item: {
        type: Object,
        default: null,
    },
});

const record = computed(() => props.redSocial ?? props.item);
const isEdit = computed(() => !!record.value?.id);

const form = useForm({
    nombre: record.value?.nombre ?? '',
    url: record.value?.url ?? '',
    icono: record.value?.icono ?? '',
    orden: record.value?.orden ?? 0,
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('admin.redes-sociales.update', record.value.id));
    } else {
        form.post(route('admin.redes-sociales.store'));
    }
};
</script>

<template>
    <Head
        :title="isEdit ? 'Editar red social — GreenPoint' : 'Nueva red social — GreenPoint'"
    />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">{{ isEdit ? 'Editar red social' : 'Nueva red social' }}</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item">
                <Link :href="route('admin.redes-sociales.index')">Redes sociales</Link>
            </li>
            <li class="breadcrumb-item active">{{ isEdit ? 'Editar' : 'Nuevo' }}</li>
        </template>

        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Datos de la red</h3>
                    </div>
                    <form @submit.prevent="submit">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input
                                    v-model="form.nombre"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.nombre }"
                                    required
                                />
                                <div v-if="form.errors.nombre" class="invalid-feedback">
                                    {{ form.errors.nombre }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL</label>
                                <input
                                    v-model="form.url"
                                    type="url"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.url }"
                                    placeholder="https://"
                                    required
                                />
                                <div v-if="form.errors.url" class="invalid-feedback">
                                    {{ form.errors.url }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Icono (clase CSS)</label>
                                <input
                                    v-model="form.icono"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.icono }"
                                    placeholder="fa-brands fa-facebook"
                                />
                                <div v-if="form.errors.icono" class="invalid-feedback">
                                    {{ form.errors.icono }}
                                </div>
                                <div v-if="form.icono" class="form-text">
                                    Vista previa: <i :class="form.icono"></i>
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
                                <div v-if="form.errors.orden" class="invalid-feedback">
                                    {{ form.errors.orden }}
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
                                :href="route('admin.redes-sociales.index')"
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
