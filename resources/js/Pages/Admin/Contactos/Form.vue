<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    contacto: {
        type: Object,
        default: null,
    },
    item: {
        type: Object,
        default: null,
    },
});

const record = computed(() => props.contacto ?? props.item);
const isEdit = computed(() => !!record.value?.id);

const form = useForm({
    ubicacion: record.value?.ubicacion ?? '',
    direccion: record.value?.direccion ?? '',
    telefono: record.value?.telefono ?? '',
    email: record.value?.email ?? '',
    mapa_url: record.value?.mapa_url ?? '',
    orden: record.value?.orden ?? 0,
});

const submit = () => {
    if (isEdit.value) {
        form.put(route('admin.contactos.update', record.value.id));
    } else {
        form.post(route('admin.contactos.store'));
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Editar contacto — GreenPoint' : 'Nuevo contacto — GreenPoint'" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">{{ isEdit ? 'Editar contacto' : 'Nuevo contacto' }}</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item">
                <Link :href="route('admin.contactos.index')">Contactos</Link>
            </li>
            <li class="breadcrumb-item active">{{ isEdit ? 'Editar' : 'Nuevo' }}</li>
        </template>

        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Datos de contacto</h3>
                    </div>
                    <form @submit.prevent="submit">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Ubicación</label>
                                <input
                                    v-model="form.ubicacion"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.ubicacion }"
                                    required
                                />
                                <div v-if="form.errors.ubicacion" class="invalid-feedback">
                                    {{ form.errors.ubicacion }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Dirección</label>
                                <textarea
                                    v-model="form.direccion"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.direccion }"
                                    rows="2"
                                />
                                <div v-if="form.errors.direccion" class="invalid-feedback">
                                    {{ form.errors.direccion }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input
                                    v-model="form.telefono"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.telefono }"
                                    required
                                />
                                <div v-if="form.errors.telefono" class="invalid-feedback">
                                    {{ form.errors.telefono }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.email }"
                                />
                                <div v-if="form.errors.email" class="invalid-feedback">
                                    {{ form.errors.email }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL del mapa</label>
                                <input
                                    v-model="form.mapa_url"
                                    type="url"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.mapa_url }"
                                    placeholder="https://"
                                />
                                <div v-if="form.errors.mapa_url" class="invalid-feedback">
                                    {{ form.errors.mapa_url }}
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
                                :href="route('admin.contactos.index')"
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
