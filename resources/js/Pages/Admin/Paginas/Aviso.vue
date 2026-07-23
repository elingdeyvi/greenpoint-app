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
    secciones: (props.pagina.secciones ?? []).map((s, index) => ({
        id: s.id ?? null,
        titulo: s.titulo ?? '',
        contenido: s.contenido ?? '',
        orden: s.orden ?? index,
        listas: (s.listas ?? []).map((l, li) => ({
            id: l.id ?? null,
            texto: l.texto ?? '',
            orden: l.orden ?? li,
        })),
    })),
});

const addSeccion = () => {
    form.secciones.push({
        id: null,
        titulo: '',
        contenido: '',
        orden: form.secciones.length,
        listas: [],
    });
};

const removeSeccion = (index) => form.secciones.splice(index, 1);

const addLista = (seccionIndex) => {
    const listas = form.secciones[seccionIndex].listas;
    listas.push({
        id: null,
        texto: '',
        orden: listas.length,
    });
};

const removeLista = (seccionIndex, listaIndex) => {
    form.secciones[seccionIndex].listas.splice(listaIndex, 1);
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        estado: data.estado ? 1 : 0,
        _method: 'put',
    })).post(route('admin.paginas.aviso.update'), { forceFormData: true });
};
</script>

<template>
    <Head title="Aviso de privacidad — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Aviso de privacidad</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Aviso</li>
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
                    <h3 class="card-title mb-0">Secciones</h3>
                    <button type="button" class="btn btn-sm btn-outline-primary" @click="addSeccion">
                        <i class="fa-solid fa-plus me-1"></i> Agregar sección
                    </button>
                </div>
                <div class="card-body">
                    <div
                        v-for="(seccion, sIndex) in form.secciones"
                        :key="seccion.id || `sec-${sIndex}`"
                        class="border rounded p-3 mb-3"
                    >
                        <div class="row g-2 mb-2">
                            <div class="col-md-5">
                                <label class="form-label">Título</label>
                                <input v-model="seccion.titulo" type="text" class="form-control" required />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Orden</label>
                                <input v-model.number="seccion.orden" type="number" min="0" class="form-control" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Contenido</label>
                                <textarea v-model="seccion.contenido" class="form-control" rows="2" />
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm w-100"
                                    @click="removeSeccion(sIndex)"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="ms-2 mt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="small">Lista de puntos</strong>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    @click="addLista(sIndex)"
                                >
                                    + Punto
                                </button>
                            </div>
                            <div
                                v-for="(item, lIndex) in seccion.listas"
                                :key="item.id || `li-${sIndex}-${lIndex}`"
                                class="input-group input-group-sm mb-1"
                            >
                                <input
                                    v-model="item.texto"
                                    type="text"
                                    class="form-control"
                                    placeholder="Texto del punto"
                                />
                                <input
                                    v-model.number="item.orden"
                                    type="number"
                                    min="0"
                                    class="form-control"
                                    style="max-width: 80px"
                                    title="Orden"
                                />
                                <button
                                    type="button"
                                    class="btn btn-outline-danger"
                                    @click="removeLista(sIndex, lIndex)"
                                >
                                    <i class="fa-solid fa-xmark"></i>
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
