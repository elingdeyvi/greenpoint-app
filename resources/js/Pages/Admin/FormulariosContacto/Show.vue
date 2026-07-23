<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    formulario: {
        type: Object,
        required: true,
    },
});

const formatDate = (value) => {
    if (!value) return '—';
    return new Date(value).toLocaleString('es-MX');
};

const markAsRead = () => {
    router.put(route('admin.formularios-contacto.update', props.formulario.id), {
        leido: true,
    });
};
</script>

<template>
    <Head title="Mensaje de contacto — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Detalle del mensaje</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item">
                <Link :href="route('admin.formularios-contacto.index')">Formularios</Link>
            </li>
            <li class="breadcrumb-item active">Detalle</li>
        </template>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">{{ formulario.nombre }}</h3>
                        <span
                            class="badge"
                            :class="formulario.leido ? 'text-bg-secondary' : 'text-bg-warning'"
                        >
                            {{ formulario.leido ? 'Leído' : 'No leído' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Email</dt>
                            <dd class="col-sm-9">{{ formulario.email }}</dd>
                            <dt class="col-sm-3">Teléfono</dt>
                            <dd class="col-sm-9">{{ formulario.telefono || '—' }}</dd>
                            <dt class="col-sm-3">Fecha</dt>
                            <dd class="col-sm-9">{{ formatDate(formulario.created_at) }}</dd>
                            <dt class="col-sm-3">Mensaje</dt>
                            <dd class="col-sm-9">
                                <p class="mb-0 white-space-pre-wrap" style="white-space: pre-wrap">
                                    {{ formulario.mensaje }}
                                </p>
                            </dd>
                        </dl>
                    </div>
                    <div class="card-footer">
                        <button
                            v-if="!formulario.leido"
                            type="button"
                            class="btn btn-primary"
                            @click="markAsRead"
                        >
                            <i class="fa-solid fa-check me-1"></i> Marcar como leído
                        </button>
                        <Link
                            :href="route('admin.formularios-contacto.index')"
                            class="btn btn-secondary"
                            :class="{ 'ms-2': !formulario.leido }"
                        >
                            Volver
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
