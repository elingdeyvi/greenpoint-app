<script setup>
import { computed, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { usePermissions } from '@/composables/usePermissions';

const page = usePage();
const { can } = usePermissions();
const user = computed(() => page.props.auth.user);
const flashSuccess = computed(() => page.props.flash?.success);
const avatarUrl = computed(
    () =>
        `https://ui-avatars.com/api/?name=${encodeURIComponent(user.value.name)}&background=198754&color=fff`,
);
const avatarLightUrl = computed(
    () =>
        `https://ui-avatars.com/api/?name=${encodeURIComponent(user.value.name)}&background=ffffff&color=198754`,
);
const sidebarOpen = ref(true);

const toggleSidebar = () => {
    document.body.classList.toggle('sidebar-collapse');
    sidebarOpen.value = !sidebarOpen.value;
};

onMounted(() => {
    document.body.classList.add('layout-fixed', 'sidebar-expand-lg', 'bg-body-tertiary');
});

const isActive = (names) => {
    const current = route().current();
    return names.some((name) => current === name || current?.startsWith(`${name}.`) || current?.startsWith(name));
};
</script>

<template>
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body border-bottom">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#" role="button" @click.prevent="toggleSidebar">
                            <i class="fa-solid fa-bars"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <Link :href="route('dashboard')" class="nav-link">Inicio</Link>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a
                            href="#"
                            class="nav-link dropdown-toggle"
                            data-bs-toggle="dropdown"
                        >
                            <img
                                :src="avatarUrl"
                                class="user-image rounded-circle shadow"
                                alt="User"
                                width="32"
                                height="32"
                            />
                            <span class="d-none d-md-inline">{{ user.name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-success">
                                <img
                                    :src="avatarLightUrl"
                                    class="rounded-circle shadow"
                                    alt="User"
                                />
                                <p>
                                    {{ user.name }}
                                    <small>{{ user.email }}</small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <Link :href="route('profile.edit')" class="btn btn-default btn-flat">
                                    Perfil
                                </Link>
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="btn btn-default btn-flat float-end"
                                >
                                    Salir
                                </Link>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <Link :href="route('dashboard')" class="brand-link">
                    <i class="fa-solid fa-leaf brand-image opacity-75 fs-4"></i>
                    <span class="brand-text app-brand-text">GreenPoint</span>
                </Link>
            </div>

            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul
                        class="nav sidebar-menu flex-column"
                        data-lte-toggle="treeview"
                        role="navigation"
                        data-accordion="false"
                    >
                        <li class="nav-header">PRINCIPAL</li>
                        <li class="nav-item">
                            <Link
                                :href="route('dashboard')"
                                class="nav-link"
                                :class="{ active: isActive(['dashboard']) }"
                            >
                                <i class="nav-icon fa-solid fa-gauge-high"></i>
                                <p>Dashboard</p>
                            </Link>
                        </li>

                        <template v-if="can('catalogos.servicios') || can('catalogos.clientes') || can('catalogos.galeria') || can('catalogos.banners') || can('catalogos.contactos') || can('catalogos.redes_sociales')">
                            <li class="nav-header">CATÁLOGOS</li>
                            <li v-if="can('catalogos.servicios')" class="nav-item">
                                <Link
                                    :href="route('admin.servicios.index')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.servicios']) }"
                                >
                                    <i class="nav-icon fa-solid fa-recycle"></i>
                                    <p>Servicios</p>
                                </Link>
                            </li>
                            <li v-if="can('catalogos.clientes')" class="nav-item">
                                <Link
                                    :href="route('admin.clientes.index')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.clientes']) }"
                                >
                                    <i class="nav-icon fa-solid fa-building"></i>
                                    <p>Clientes</p>
                                </Link>
                            </li>
                            <li v-if="can('catalogos.galeria')" class="nav-item">
                                <Link
                                    :href="route('admin.galeria.index')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.galeria']) }"
                                >
                                    <i class="nav-icon fa-solid fa-images"></i>
                                    <p>Galería</p>
                                </Link>
                            </li>
                            <li v-if="can('catalogos.banners')" class="nav-item">
                                <Link
                                    :href="route('admin.banners.index')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.banners']) }"
                                >
                                    <i class="nav-icon fa-solid fa-panorama"></i>
                                    <p>Banners</p>
                                </Link>
                            </li>
                            <li v-if="can('catalogos.contactos')" class="nav-item">
                                <Link
                                    :href="route('admin.contactos.index')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.contactos']) }"
                                >
                                    <i class="nav-icon fa-solid fa-address-book"></i>
                                    <p>Contactos</p>
                                </Link>
                            </li>
                            <li v-if="can('catalogos.redes_sociales')" class="nav-item">
                                <Link
                                    :href="route('admin.redes-sociales.index')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.redes-sociales']) }"
                                >
                                    <i class="nav-icon fa-solid fa-share-nodes"></i>
                                    <p>Redes sociales</p>
                                </Link>
                            </li>
                        </template>

                        <template v-if="can('modulos.nosotros') || can('modulos.historia') || can('modulos.tecnologia') || can('modulos.aviso')">
                            <li class="nav-header">PÁGINAS</li>
                            <li v-if="can('modulos.nosotros')" class="nav-item">
                                <Link
                                    :href="route('admin.paginas.nosotros.edit')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.paginas.nosotros']) }"
                                >
                                    <i class="nav-icon fa-solid fa-users"></i>
                                    <p>Nosotros</p>
                                </Link>
                            </li>
                            <li v-if="can('modulos.historia')" class="nav-item">
                                <Link
                                    :href="route('admin.paginas.historia.edit')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.paginas.historia']) }"
                                >
                                    <i class="nav-icon fa-solid fa-clock-rotate-left"></i>
                                    <p>Historia</p>
                                </Link>
                            </li>
                            <li v-if="can('modulos.tecnologia')" class="nav-item">
                                <Link
                                    :href="route('admin.paginas.tecnologia.edit')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.paginas.tecnologia']) }"
                                >
                                    <i class="nav-icon fa-solid fa-microchip"></i>
                                    <p>Tecnología</p>
                                </Link>
                            </li>
                            <li v-if="can('modulos.aviso')" class="nav-item">
                                <Link
                                    :href="route('admin.paginas.aviso.edit')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.paginas.aviso']) }"
                                >
                                    <i class="nav-icon fa-solid fa-file-shield"></i>
                                    <p>Aviso de privacidad</p>
                                </Link>
                            </li>
                        </template>

                        <template v-if="can('formularios_contacto.ver') || can('administracion.configuracion_critica')">
                            <li class="nav-header">GESTIÓN</li>
                            <li v-if="can('formularios_contacto.ver')" class="nav-item">
                                <Link
                                    :href="route('admin.formularios-contacto.index')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.formularios-contacto']) }"
                                >
                                    <i class="nav-icon fa-solid fa-envelope"></i>
                                    <p>Mensajes</p>
                                </Link>
                            </li>
                            <li v-if="can('administracion.configuracion_critica')" class="nav-item">
                                <Link
                                    :href="route('admin.configuracion.index')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.configuracion']) }"
                                >
                                    <i class="nav-icon fa-solid fa-sliders"></i>
                                    <p>Configuración</p>
                                </Link>
                            </li>
                        </template>

                        <template v-if="can('administracion.usuarios') || can('administracion.roles')">
                            <li class="nav-header">ADMINISTRACIÓN</li>
                            <li v-if="can('administracion.usuarios')" class="nav-item">
                                <Link
                                    :href="route('admin.users.index')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.users']) }"
                                >
                                    <i class="nav-icon fa-solid fa-user-gear"></i>
                                    <p>Usuarios</p>
                                </Link>
                            </li>
                            <li v-if="can('administracion.roles')" class="nav-item">
                                <Link
                                    :href="route('admin.roles.index')"
                                    class="nav-link"
                                    :class="{ active: isActive(['admin.roles']) }"
                                >
                                    <i class="nav-icon fa-solid fa-shield-halved"></i>
                                    <p>Roles</p>
                                </Link>
                            </li>
                        </template>

                        <li class="nav-header">CUENTA</li>
                        <li class="nav-item">
                            <Link
                                :href="route('profile.edit')"
                                class="nav-link"
                                :class="{ active: isActive(['profile.edit']) }"
                            >
                                <i class="nav-icon fa-solid fa-id-card"></i>
                                <p>Perfil</p>
                            </Link>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <main class="app-main">
            <div class="app-content-header" v-if="$slots.header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <slot name="header" />
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end" v-if="$slots.breadcrumb">
                                <slot name="breadcrumb" />
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div v-if="flashSuccess" class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ flashSuccess }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <slot />
                </div>
            </div>
        </main>

        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">Laravel 12 · Vue 3 · AdminLTE 4</div>
            <strong>
                Copyright &copy; {{ new Date().getFullYear() }}
                <a href="#">GreenPoint</a>.
            </strong>
            Todos los derechos reservados.
        </footer>
    </div>
</template>
