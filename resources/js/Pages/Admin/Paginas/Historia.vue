<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    pagina: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    titulo: props.pagina.titulo ?? '',
    meta_descripcion: props.pagina.meta_descripcion ?? '',
    meta_keywords: props.pagina.meta_keywords ?? '',
    estado: props.pagina.estado ?? true,
    eventos: (props.pagina.eventos ?? []).map((e, index) => ({
        id: e.id ?? null,
        anio: e.anio ?? new Date().getFullYear(),
        titulo: e.titulo ?? '',
        descripcion: e.descripcion ?? '',
        orden: e.orden ?? index,
    })),
    imagenes: (props.pagina.imagenes ?? []).map((img, index) => ({
        id: img.id ?? null,
        ruta_imagen: img.ruta_imagen ?? '',
        orden: img.orden ?? index,
        archivo: null,
    })),
});

const addEvento = () => {
    form.eventos.push({
        id: null,
        anio: new Date().getFullYear(),
        titulo: '',
        descripcion: '',
        orden: form.eventos.length,
    });
};

const removeEvento = (index) => form.eventos.splice(index, 1);

const addImagen = () => {
    form.imagenes.push({
        id: null,
        ruta_imagen: '',
        orden: form.imagenes.length,
        archivo: null,
    });
};

const removeImagen = (index) => form.imagenes.splice(index, 1);

const onImagenFile = (index, e) => {
    form.imagenes[index].archivo = e.target.files?.[0] ?? null;
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        estado: data.estado ? 1 : 0,
        _method: 'put',
    })).post(route('admin.paginas.historia.update'), { forceFormData: true });
};
</script>

<template>
    <Head title="Página Historia — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Página Historia</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Historia</li>
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
                            <label class="form-label">Meta descripción</label>
                            <input v-model="form.meta_descripcion" type="text" class="form-control" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Meta keywords</label>
                            <input v-model="form.meta_keywords" type="text" class="form-control" />
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
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
                    <h3 class="card-title mb-0">Eventos</h3>
                    <button type="button" class="btn btn-sm btn-outline-primary" @click="addEvento">
                        <i class="fa-solid fa-plus me-1"></i> Agregar
                    </button>
                </div>
                <div class="card-body">
                    <div
                        v-for="(evento, index) in form.eventos"
                        :key="evento.id || `ev-${index}`"
                        class="border rounded p-3 mb-2"
                    >
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label class="form-label">Año</label>
                                <input v-model.number="evento.anio" type="number" class="form-control" required />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Título</label>
                                <input v-model="evento.titulo" type="text" class="form-control" required />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Orden</label>
                                <input v-model.number="evento.orden" type="number" min="0" class="form-control" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Descripción</label>
                                <input v-model="evento.descripcion" type="text" class="form-control" />
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm w-100"
                                    @click="removeEvento(index)"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <p v-if="!form.eventos.length" class="text-muted mb-0">Sin eventos</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Imágenes</h3>
                    <button type="button" class="btn btn-sm btn-outline-primary" @click="addImagen">
                        <i class="fa-solid fa-plus me-1"></i> Agregar
                    </button>
                </div>
                <div class="card-body">
                    <div
                        v-for="(img, index) in form.imagenes"
                        :key="img.id || `img-${index}`"
                        class="border rounded p-3 mb-2"
                    >
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">Archivo</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    accept="image/*"
                                    @change="onImagenFile(index, $event)"
                                />
                                <div v-if="img.ruta_imagen" class="form-text">
                                    Actual: {{ img.ruta_imagen }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Orden</label>
                                <input v-model.number="img.orden" type="number" min="0" class="form-control" />
                            </div>
                            <div class="col-md-4 text-end">
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    @click="removeImagen(index)"
                                >
                                    Quitar
                                </button>
                            </div>
                        </div>
                    </div>
                    <p v-if="!form.imagenes.length" class="text-muted mb-0">Sin imágenes</p>
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
