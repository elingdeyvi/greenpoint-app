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
    subtitulo: props.pagina.subtitulo ?? '',
    texto_descriptivo: props.pagina.texto_descriptivo ?? '',
    texto_adicional: props.pagina.texto_adicional ?? '',
    url_video: props.pagina.url_video ?? '',
    imagen_destacada: null,
    meta_descripcion: props.pagina.meta_descripcion ?? '',
    meta_keywords: props.pagina.meta_keywords ?? '',
    estado: props.pagina.estado ?? true,
    imagenes: (props.pagina.imagenes ?? []).map((img, index) => ({
        id: img.id ?? null,
        ruta_imagen: img.ruta_imagen ?? '',
        orden: img.orden ?? index,
        archivo: null,
    })),
    progreso: (props.pagina.progreso ?? []).map((p, index) => ({
        id: p.id ?? null,
        titulo: p.titulo ?? '',
        porcentaje: p.porcentaje ?? 0,
        descripcion: p.descripcion ?? '',
        orden: p.orden ?? index,
    })),
});

const onDestacadaChange = (e) => {
    const file = e.target.files?.[0] ?? null;
    form.imagen_destacada = file;
    previewUrl.value = file ? URL.createObjectURL(file) : null;
};

const addImagen = () => {
    form.imagenes.push({
        id: null,
        ruta_imagen: '',
        orden: form.imagenes.length,
        archivo: null,
    });
};

const removeImagen = (index) => {
    form.imagenes.splice(index, 1);
};

const onImagenFile = (index, e) => {
    form.imagenes[index].archivo = e.target.files?.[0] ?? null;
};

const addProgreso = () => {
    form.progreso.push({
        id: null,
        titulo: '',
        porcentaje: 0,
        descripcion: '',
        orden: form.progreso.length,
    });
};

const removeProgreso = (index) => {
    form.progreso.splice(index, 1);
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        estado: data.estado ? 1 : 0,
        _method: 'put',
    })).post(route('admin.paginas.nosotros.update'), { forceFormData: true });
};
</script>

<template>
    <Head title="Página Nosotros — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Página Nosotros</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Nosotros</li>
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
                            <label class="form-label">Subtítulo</label>
                            <input v-model="form.subtitulo" type="text" class="form-control" />
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Texto descriptivo</label>
                            <textarea v-model="form.texto_descriptivo" class="form-control" rows="4" />
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Texto adicional</label>
                            <textarea v-model="form.texto_adicional" class="form-control" rows="3" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">URL de video</label>
                            <input v-model="form.url_video" type="url" class="form-control" />
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

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Barras de progreso</h3>
                    <button type="button" class="btn btn-sm btn-outline-primary" @click="addProgreso">
                        <i class="fa-solid fa-plus me-1"></i> Agregar
                    </button>
                </div>
                <div class="card-body">
                    <div
                        v-for="(barra, index) in form.progreso"
                        :key="barra.id || `prog-${index}`"
                        class="border rounded p-3 mb-2"
                    >
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Título</label>
                                <input v-model="barra.titulo" type="text" class="form-control" required />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">%</label>
                                <input
                                    v-model.number="barra.porcentaje"
                                    type="number"
                                    min="0"
                                    max="100"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Orden</label>
                                <input v-model.number="barra.orden" type="number" min="0" class="form-control" />
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Descripción</label>
                                <input v-model="barra.descripcion" type="text" class="form-control" />
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm w-100"
                                    @click="removeProgreso(index)"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <p v-if="!form.progreso.length" class="text-muted mb-0">Sin barras de progreso</p>
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
