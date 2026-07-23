<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    servicio: {
        type: Object,
        default: null,
    },
    item: {
        type: Object,
        default: null,
    },
});

const record = computed(() => props.servicio ?? props.item);
const isEdit = computed(() => !!record.value?.id);
const previewUrl = ref(null);

const form = useForm({
    nombre: record.value?.nombre ?? '',
    descripcion: record.value?.descripcion ?? '',
    imagen: null,
    orden: record.value?.orden ?? 0,
    activo: record.value?.activo ?? true,
});

const onFileChange = (e) => {
    const file = e.target.files?.[0] ?? null;
    form.imagen = file;
    previewUrl.value = file ? URL.createObjectURL(file) : null;
};

const submit = () => {
    const options = { forceFormData: true };
    if (isEdit.value) {
        form.transform((data) => ({
            ...data,
            activo: data.activo ? 1 : 0,
            _method: 'put',
        })).post(route('admin.servicios.update', record.value.id), options);
    } else {
        form.transform((data) => ({
            ...data,
            activo: data.activo ? 1 : 0,
        })).post(route('admin.servicios.store'), options);
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Editar servicio — GreenPoint' : 'Nuevo servicio — GreenPoint'" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">{{ isEdit ? 'Editar servicio' : 'Nuevo servicio' }}</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item">
                <Link :href="route('admin.servicios.index')">Servicios</Link>
            </li>
            <li class="breadcrumb-item active">{{ isEdit ? 'Editar' : 'Nuevo' }}</li>
        </template>

        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Datos del servicio</h3>
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
                                <label class="form-label">Descripción</label>
                                <textarea
                                    v-model="form.descripcion"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.descripcion }"
                                    rows="4"
                                />
                                <div v-if="form.errors.descripcion" class="invalid-feedback">
                                    {{ form.errors.descripcion }}
                                </div>
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
                                <div v-if="form.errors.imagen" class="invalid-feedback">
                                    {{ form.errors.imagen }}
                                </div>
                                <div v-if="previewUrl || record?.imagen" class="mt-2">
                                    <img
                                        :src="previewUrl || `/storage/${record.imagen}`"
                                        alt="Vista previa"
                                        class="img-thumbnail"
                                        style="max-height: 140px"
                                    />
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
                            <div class="form-check">
                                <input
                                    id="activo"
                                    v-model="form.activo"
                                    class="form-check-input"
                                    type="checkbox"
                                />
                                <label class="form-check-label" for="activo">Activo</label>
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
                                :href="route('admin.servicios.index')"
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
