<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AdminModal from '@/Components/Admin/AdminModal.vue';
import PaginasTabs from '@/Components/Admin/PaginasTabs.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';

const props = defineProps({
    home: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    home_servicios_titulo: props.home.home_servicios_titulo ?? '',
    home_servicios_subtitulo: props.home.home_servicios_subtitulo ?? '',
    home_cta_titulo: props.home.home_cta_titulo ?? '',
    home_cta_texto: props.home.home_cta_texto ?? '',
    home_video_url: props.home.home_video_url ?? '',
    anos_experiencia: props.home.anos_experiencia ?? '',
    home_service_cards: (props.home.home_service_cards ?? []).map((card) => ({
        nombre: card.nombre ?? '',
        descripcion: card.descripcion ?? '',
        icon: card.icon ?? '',
    })),
    home_about_checks: [...(props.home.home_about_checks ?? [])],
    home_why_left: (props.home.home_why_left ?? []).map((item) => ({
        title: item.title ?? '',
        text: item.text ?? '',
        icon: item.icon ?? '',
    })),
    home_why_right: (props.home.home_why_right ?? []).map((item) => ({
        title: item.title ?? '',
        text: item.text ?? '',
        icon: item.icon ?? '',
    })),
    home_feature_cards: (props.home.home_feature_cards ?? []).map((card) => ({
        title: card.title ?? '',
        icon: card.icon ?? '',
    })),
});

const closeEditor = () => {
    router.visit(route('admin.paginas.index'));
};

const addServiceCard = () => {
    form.home_service_cards.push({ nombre: '', descripcion: '', icon: '' });
};

const removeServiceCard = (index) => {
    form.home_service_cards.splice(index, 1);
};

const addAboutCheck = () => {
    form.home_about_checks.push('');
};

const removeAboutCheck = (index) => {
    form.home_about_checks.splice(index, 1);
};

const addWhyLeft = () => {
    form.home_why_left.push({ title: '', text: '', icon: '' });
};

const removeWhyLeft = (index) => {
    form.home_why_left.splice(index, 1);
};

const addWhyRight = () => {
    form.home_why_right.push({ title: '', text: '', icon: '' });
};

const removeWhyRight = (index) => {
    form.home_why_right.splice(index, 1);
};

const addFeatureCard = () => {
    form.home_feature_cards.push({ title: '', icon: '' });
};

const removeFeatureCard = (index) => {
    form.home_feature_cards.splice(index, 1);
};

const submit = () => {
    form.put(route('admin.paginas.home.update'), {
        onSuccess: () => router.visit(route('admin.paginas.index')),
    });
};
</script>

<template>
    <Head title="Página Inicio — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Página Inicio</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item"><Link :href="route('admin.paginas.index')">Páginas</Link></li>
            <li class="breadcrumb-item active">Editar</li>
        </template>

        <PaginasTabs active="home" />

        <AdminModal
            :show="true"
            title="Editar Inicio"
            size="xl"
            @close="closeEditor"
        >
            <form id="home-form" @submit.prevent="submit">
                <div class="card card-primary mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Textos generales</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Título servicios</label>
                                <input
                                    v-model="form.home_servicios_titulo"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Subtítulo servicios</label>
                                <input
                                    v-model="form.home_servicios_subtitulo"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Título CTA / video</label>
                                <input
                                    v-model="form.home_cta_titulo"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">URL de video</label>
                                <input
                                    v-model="form.home_video_url"
                                    type="url"
                                    class="form-control"
                                />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Años de experiencia</label>
                                <input
                                    v-model="form.anos_experiencia"
                                    type="text"
                                    class="form-control"
                                />
                            </div>
                            <div class="col-12 mb-0">
                                <label class="form-label">Texto CTA (oferta)</label>
                                <textarea
                                    v-model="form.home_cta_texto"
                                    class="form-control"
                                    rows="4"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Tarjetas de servicio</h3>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary"
                            @click="addServiceCard"
                        >
                            <i class="fa-solid fa-plus me-1"></i> Agregar
                        </button>
                    </div>
                    <div class="card-body">
                        <div
                            v-for="(card, index) in form.home_service_cards"
                            :key="`svc-${index}`"
                            class="border rounded p-3 mb-2"
                        >
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Nombre</label>
                                    <input
                                        v-model="card.nombre"
                                        type="text"
                                        class="form-control"
                                        required
                                    />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Icono (ruta)</label>
                                    <input
                                        v-model="card.icon"
                                        type="text"
                                        class="form-control"
                                        placeholder="/images/demo/icons/icon-01.png"
                                    />
                                </div>
                                <div class="col-md-4 d-flex align-items-end justify-content-end">
                                    <button
                                        type="button"
                                        class="btn btn-outline-danger btn-sm"
                                        @click="removeServiceCard(index)"
                                    >
                                        Quitar
                                    </button>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea
                                        v-model="card.descripcion"
                                        class="form-control"
                                        rows="2"
                                    />
                                </div>
                            </div>
                        </div>
                        <p v-if="!form.home_service_cards.length" class="text-muted mb-0">
                            Sin tarjetas de servicio
                        </p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Checks “Quiénes somos”</h3>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary"
                            @click="addAboutCheck"
                        >
                            <i class="fa-solid fa-plus me-1"></i> Agregar
                        </button>
                    </div>
                    <div class="card-body">
                        <div
                            v-for="(check, index) in form.home_about_checks"
                            :key="`check-${index}`"
                            class="row g-2 align-items-end mb-2"
                        >
                            <div class="col">
                                <label class="form-label">Texto</label>
                                <input
                                    v-model="form.home_about_checks[index]"
                                    type="text"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <div class="col-auto">
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    @click="removeAboutCheck(index)"
                                >
                                    Quitar
                                </button>
                            </div>
                        </div>
                        <p v-if="!form.home_about_checks.length" class="text-muted mb-0">
                            Sin checks
                        </p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Por qué elegirnos (izquierda)</h3>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary"
                            @click="addWhyLeft"
                        >
                            <i class="fa-solid fa-plus me-1"></i> Agregar
                        </button>
                    </div>
                    <div class="card-body">
                        <div
                            v-for="(item, index) in form.home_why_left"
                            :key="`why-l-${index}`"
                            class="border rounded p-3 mb-2"
                        >
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Título</label>
                                    <input
                                        v-model="item.title"
                                        type="text"
                                        class="form-control"
                                        required
                                    />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Icono (ruta)</label>
                                    <input
                                        v-model="item.icon"
                                        type="text"
                                        class="form-control"
                                    />
                                </div>
                                <div class="col-md-4 d-flex align-items-end justify-content-end">
                                    <button
                                        type="button"
                                        class="btn btn-outline-danger btn-sm"
                                        @click="removeWhyLeft(index)"
                                    >
                                        Quitar
                                    </button>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Texto</label>
                                    <textarea
                                        v-model="item.text"
                                        class="form-control"
                                        rows="2"
                                    />
                                </div>
                            </div>
                        </div>
                        <p v-if="!form.home_why_left.length" class="text-muted mb-0">Sin ítems</p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Por qué elegirnos (derecha)</h3>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary"
                            @click="addWhyRight"
                        >
                            <i class="fa-solid fa-plus me-1"></i> Agregar
                        </button>
                    </div>
                    <div class="card-body">
                        <div
                            v-for="(item, index) in form.home_why_right"
                            :key="`why-r-${index}`"
                            class="border rounded p-3 mb-2"
                        >
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Título</label>
                                    <input
                                        v-model="item.title"
                                        type="text"
                                        class="form-control"
                                        required
                                    />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Icono (ruta)</label>
                                    <input
                                        v-model="item.icon"
                                        type="text"
                                        class="form-control"
                                    />
                                </div>
                                <div class="col-md-4 d-flex align-items-end justify-content-end">
                                    <button
                                        type="button"
                                        class="btn btn-outline-danger btn-sm"
                                        @click="removeWhyRight(index)"
                                    >
                                        Quitar
                                    </button>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Texto</label>
                                    <textarea
                                        v-model="item.text"
                                        class="form-control"
                                        rows="2"
                                    />
                                </div>
                            </div>
                        </div>
                        <p v-if="!form.home_why_right.length" class="text-muted mb-0">Sin ítems</p>
                    </div>
                </div>

                <div class="card mb-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Tarjetas de características</h3>
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary"
                            @click="addFeatureCard"
                        >
                            <i class="fa-solid fa-plus me-1"></i> Agregar
                        </button>
                    </div>
                    <div class="card-body">
                        <div
                            v-for="(card, index) in form.home_feature_cards"
                            :key="`feat-${index}`"
                            class="row g-2 align-items-end mb-2"
                        >
                            <div class="col-md-5">
                                <label class="form-label">Título</label>
                                <input
                                    v-model="card.title"
                                    type="text"
                                    class="form-control"
                                    required
                                />
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Icono (clase Themify)</label>
                                <input
                                    v-model="card.icon"
                                    type="text"
                                    class="form-control"
                                    placeholder="ti-medall"
                                />
                            </div>
                            <div class="col-md-2">
                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm w-100"
                                    @click="removeFeatureCard(index)"
                                >
                                    Quitar
                                </button>
                            </div>
                        </div>
                        <p v-if="!form.home_feature_cards.length" class="text-muted mb-0">
                            Sin características
                        </p>
                    </div>
                </div>
            </form>
            <template #footer>
                <button type="button" class="btn btn-secondary" @click="closeEditor">Cancelar</button>
                <button
                    type="submit"
                    form="home-form"
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
