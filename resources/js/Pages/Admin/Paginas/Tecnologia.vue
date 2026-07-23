<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    pagina: {
        type: Object,
        required: true,
    },
});

const previewUrl = ref(null);

const form = useForm({
    titulo: props.pagina.titulo ?? '',
    contenido: props.pagina.contenido ?? '',
    imagen_destacada: null,
    meta_descripcion: props.pagina.meta_descripcion ?? '',
    meta_keywords: props.pagina.meta_keywords ?? '',
    estado: props.pagina.estado ?? true,
    secciones: (props.pagina.secciones ?? []).map((s, index) => ({
        id: s.id ?? null,
        titulo: s.titulo ?? '',
        contenido: s.contenido ?? '',
        orden: s.orden ?? index,
    })),
});

const onDestacadaChange = (e) => {
    const file = e.target.files?.[0] ?? null;
    form.imagen_destacada = file;
    previewUrl.value = file ? URL.createObjectURL(file) : null;
};

const addSeccion = () => {
    form.secciones.push({
        id: null,
        titulo: '',
        contenido: '',
        orden: form.secciones.length,
    });
};

const removeSeccion = (index) => form.secciones.splice(index, 1);

const submit = () => {
    form.transform((data) => ({
        ...data,
        estado: data.estado ? 1 : 0,
        _method: 'put',
    })).post(route('admin.paginas.tecnologia.update'), { forceFormData: true });
};
</script>

<template>
    <Head title="Página Tecnología — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Página Tecnología</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Tecnología</li>
        </template>

        <form @submit.prevent="submit">
            <div class="card card-primary mb-3">
                <div class="card-header">
                    <h3 class="card-title">Contenido principal</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Título</label>
                            <input v-model="form.titulo" type="text" class="form-control" required />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Imagen destacada</label>
                            <input
                                type="file"
                                class="form-control"
                                accept="image/*"
                                @change="onDestacadaChange"
                            />
                            <div v-if="previewUrl || pagina.imagen_destacada" class="mt-2">
                                <img
                                    :src="previewUrl || `/storage/${pagina.imagen_destacada}`"
                                    alt=""
                                    class="img-thumbnail"
                                    style="max-height: 120px"
                                />
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Contenido</label>
                            <textarea v-model="form.contenido" class="form-control" rows="5" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Meta descripción</label>
                            <input v-model="form.meta_descripcion" type="text" class="form-control" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Meta keywords</label>
                            <input v-model="form.meta_keywords" type="text" class="form-control" />
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input
                                    id="estado"
                                    v-model="form.estado"
                                    class="form-check-input"
                                    type="checkbox"
                                />
                                <label class="form-check-label" for="estado">Página activa</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Secciones</h3>
                    <button type="button" class="btn btn-sm btn-outline-primary" @click="addSeccion">
                        <i class="fa-solid fa-plus me-1"></i> Agregar
                    </button>
                </div>
                <div class="card-body">
                    <div
                        v-for="(seccion, index) in form.secciones"
                        :key="seccion.id || `sec-${index}`"
                        class="border rounded p-3 mb-2"
                    >
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Título</label>
                                <input v-model="seccion.titulo" type="text" class="form-control" required />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Orden</label>
                                <input v-model.number="seccion.orden" type="number" min="0" class="form-control" />
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Contenido</label>
                                <textarea v-model="seccion.contenido" class="form-control" rows="2" />
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm w-100"
                                    @click="removeSeccion(index)"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <p v-if="!form.secciones.length" class="text-muted mb-0">Sin secciones</p>
                </div>
            </div>

            <div class="mb-4">
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    Guardar página
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
