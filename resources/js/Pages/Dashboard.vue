<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { usePermissions } from '@/composables/usePermissions';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            servicios: 0,
            usuarios: 0,
            mensajes: 0,
            visitors: 0,
        }),
    },
});

const { can } = usePermissions();
</script>

<template>
    <Head title="Dashboard — GreenPoint" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Dashboard</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </template>

        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-success">
                    <div class="inner">
                        <h3>{{ stats.servicios }}</h3>
                        <p>Servicios</p>
                    </div>
                    <div class="icon"><i class="fa-solid fa-recycle"></i></div>
                    <Link
                        v-if="can('catalogos.servicios')"
                        :href="route('admin.servicios.index')"
                        class="small-box-footer link-light link-underline-opacity-0"
                    >
                        Ver catálogo <i class="fa-solid fa-arrow-circle-right"></i>
                    </Link>
                    <span v-else class="small-box-footer">&nbsp;</span>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-primary">
                    <div class="inner">
                        <h3>{{ stats.usuarios }}</h3>
                        <p>Usuarios activos</p>
                    </div>
                    <div class="icon"><i class="fa-solid fa-users"></i></div>
                    <Link
                        v-if="can('administracion.usuarios')"
                        :href="route('admin.users.index')"
                        class="small-box-footer link-light link-underline-opacity-0"
                    >
                        Ver usuarios <i class="fa-solid fa-arrow-circle-right"></i>
                    </Link>
                    <span v-else class="small-box-footer">&nbsp;</span>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-warning">
                    <div class="inner">
                        <h3>{{ stats.mensajes }}</h3>
                        <p>Mensajes sin leer</p>
                    </div>
                    <div class="icon"><i class="fa-solid fa-envelope"></i></div>
                    <Link
                        v-if="can('formularios_contacto.ver')"
                        :href="route('admin.formularios-contacto.index')"
                        class="small-box-footer link-dark link-underline-opacity-0"
                    >
                        Ver bandeja <i class="fa-solid fa-arrow-circle-right"></i>
                    </Link>
                    <span v-else class="small-box-footer">&nbsp;</span>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box text-bg-secondary">
                    <div class="inner">
                        <h3>CMS</h3>
                        <p>Sitio GreenPoint</p>
                    </div>
                    <div class="icon"><i class="fa-solid fa-leaf"></i></div>
                    <span class="small-box-footer link-light">Panel administrativo</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Accesos rápidos</h3>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <Link
                        v-if="can('catalogos.banners')"
                        :href="route('admin.banners.index')"
                        class="btn btn-outline-primary"
                    >
                        <i class="fa-solid fa-panorama me-1"></i> Banners
                    </Link>
                    <Link
                        v-if="can('modulos.nosotros')"
                        :href="route('admin.paginas.nosotros.edit')"
                        class="btn btn-outline-success"
                    >
                        <i class="fa-solid fa-users me-1"></i> Nosotros
                    </Link>
                    <Link
                        v-if="can('catalogos.galeria')"
                        :href="route('admin.galeria.index')"
                        class="btn btn-outline-info"
                    >
                        <i class="fa-solid fa-images me-1"></i> Galería
                    </Link>
                    <Link
                        v-if="can('administracion.configuracion_critica')"
                        :href="route('admin.configuracion.index')"
                        class="btn btn-outline-secondary"
                    >
                        <i class="fa-solid fa-sliders me-1"></i> Configuración
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
