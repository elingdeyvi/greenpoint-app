<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    item: {
        type: Object,
        default: null,
    },
});

const isEdit = computed(() => !!props.item?.id);
const previewUrl = ref(null);

const form = useForm({
    titulo: props.item?.titulo ?? '',
    descripcion: props.item?.descripcion ?? '',
    imagen: null,
    orden: props.item?.orden ?? 0,
    activo: props.item?.activo ?? true,
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
        })).post(route('admin.galeria.update', props.item.id), options);
    } else {
        form.transform((data) => ({
            ...data,
            activo: data.activo ? 1 : 0,
        })).post(route('admin.galeria.store'), options);
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Editar galería — GreenPoint' : 'Nueva imagen — GreenPoint'" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">{{ isEdit ? 'Editar imagen' : 'Nueva imagen' }}</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item">
                <Link :href="route('admin.galeria.index')">Galería</Link>
            </li>
            <li class="breadcrumb-item active">{{ isEdit ? 'Editar' : 'Nuevo' }}</li>
        </template>

        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Datos de galería</h3>
                    </div>
                    <form @submit.prevent="submit">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Título</label>
                                <input
                                    v-model="form.titulo"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.titulo }"
                                    required
                                />
                                <div v-if="form.errors.titulo" class="invalid-feedback">
                                    {{ form.errors.titulo }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea
                                    v-model="form.descripcion"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.descripcion }"
                                    rows="3"
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
                                <div v-if="previewUrl || item?.imagen" class="mt-2">
                                    <img
                                        :src="previewUrl || `/storage/${item.imagen}`"
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
                                :href="route('admin.galeria.index')"
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
