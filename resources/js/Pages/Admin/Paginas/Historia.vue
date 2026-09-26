<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminModal from '@/Components/Admin/AdminModal.vue';
import PaginasTabs from '@/Components/Admin/PaginasTabs.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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
    cv_etiqueta: props.pagina.cv_etiqueta ?? 'Servicios Greenpoint',
    cv_pdf: null,
    eliminar_cv_pdf: false,
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

const cvFileName = ref('');
const hasStoredCv = computed(() => !!props.pagina.cv_pdf && !form.eliminar_cv_pdf);

const closeEditor = () => {
    router.visit(route('admin.paginas.index'));
};

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

const onCvFile = (e) => {
    const file = e.target.files?.[0] ?? null;
    form.cv_pdf = file;
    form.eliminar_cv_pdf = false;
    cvFileName.value = file?.name ?? '';
};

const markRemoveCv = () => {
    form.cv_pdf = null;
    form.eliminar_cv_pdf = true;
    cvFileName.value = '';
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        estado: data.estado ? 1 : 0,
        eliminar_cv_pdf: data.eliminar_cv_pdf ? 1 : 0,
        _method: 'put',
    })).post(route('admin.paginas.historia.update'), {
        forceFormData: true,
        onSuccess: () => router.visit(route('admin.paginas.index')),
    });
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
            <li class="breadcrumb-item"><Link :href="route('admin.paginas.index')">Páginas</Link></li>
            <li class="breadcrumb-item active">Editar</li>
        </template>

        <PaginasTabs active="historia" />

        <AdminModal
            :show="true"
            title="Editar Historia"
            size="xl"
            @close="closeEditor"
        >
            <form id="historia-form" @submit.prevent="submit">
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
                    <div class="card-header">
                        <h3 class="card-title mb-0">Curriculum (PDF)</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            Archivo del widget “Curriculum” en /historia. Se publica en
                            <code>/cv.pdf</code> (igual que en producción).
                        </p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Etiqueta del enlace</label>
                                <input
                                    v-model="form.cv_etiqueta"
                                    type="text"
                                    class="form-control"
                                    placeholder="Servicios Greenpoint"
                                />
                                <div v-if="form.errors.cv_etiqueta" class="text-danger small mt-1">
                                    {{ form.errors.cv_etiqueta }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Archivo PDF</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    accept="application/pdf,.pdf"
                                    @change="onCvFile"
                                />
                                <div v-if="form.errors.cv_pdf" class="text-danger small mt-1">
                                    {{ form.errors.cv_pdf }}
                                </div>
                                <div v-if="cvFileName" class="form-text">Nuevo archivo: {{ cvFileName }}</div>
                                <div
                                    v-else-if="hasStoredCv"
                                    class="form-text d-flex flex-wrap gap-2 align-items-center"
                                >
                                    <span>
                                        Actual:
                                        <a :href="route('public.cv')" target="_blank" rel="noopener noreferrer">
                                            Ver /cv.pdf
                                        </a>
                                    </span>
                                    <button
                                        type="button"
                                        class="btn btn-link btn-sm text-danger p-0"
                                        @click="markRemoveCv"
                                    >
                                        Quitar PDF
                                    </button>
                                </div>
                                <div v-else-if="form.eliminar_cv_pdf" class="form-text text-warning">
                                    El PDF se eliminará al guardar.
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

                <div class="card mb-0">
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
            </form>
            <template #footer>
                <button type="button" class="btn btn-secondary" @click="closeEditor">Cancelar</button>
                <button
                    type="submit"
                    form="historia-form"
                    class="btn btn-primary"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-1" />
                    Guardar
                </button>
            </template>
        </AdminModal>
    </AuthenticatedLayout>
</template>
