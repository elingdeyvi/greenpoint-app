<template>
    <!--  BEGIN SIDEBAR  -->
    <div class="sidebar-wrapper sidebar-theme">
        <nav ref="menu" id="sidebar">
            <div class="shadow-bottom"></div>

            <perfect-scrollbar class="list-unstyled menu-categories" tag="ul" :options="{ wheelSpeed: 0.5, swipeEasing: !0, minScrollbarLength: 40, maxScrollbarLength: 300, suppressScrollX: true }">

                <!-- 1. Inicio -->
                <li class="menu" v-if="can('dashboard.ver')">
                    <a class="dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#menu-dashboard" aria-controls="menu-dashboard" aria-expanded="false">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <span>Inicio</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </a>
                    <ul id="menu-dashboard" class="collapse submenu list-unstyled" data-bs-parent="#sidebar">
                        <li>
                            <router-link to="/dashboard" @click="toggleMobileMenu">Dashboard</router-link>
                        </li>
                    </ul>
                </li>

                <!-- Administración -->
                <li class="menu" v-if="canAdministracion">
                    <a class="dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#menu-administracion" aria-controls="menu-administracion" aria-expanded="false">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-settings">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                            <span>Administración</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </a>
                    <ul id="menu-administracion" class="collapse submenu list-unstyled" data-bs-parent="#sidebar">
                        <li v-if="can('administracion.usuarios')">
                            <router-link to="/users/lista" @click="toggleMobileMenu">Usuarios</router-link>
                        </li>
                        <li v-if="can('administracion.roles')">
                            <router-link to="/roles/lista" @click="toggleMobileMenu">Roles y permisos</router-link>
                        </li>
                    </ul>
                </li>

                <!-- Mi perfil -->
                <li class="menu">
                    <router-link to="/users/profile" class="dropdown-toggle" @click="toggleMobileMenu">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-user">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>Mi perfil</span>
                        </div>
                    </router-link>
                </li>
            </perfect-scrollbar>
        </nav>
    </div>
    <!--  END SIDEBAR  -->
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useStore } from 'vuex';
import { usePermissions } from '@/composables/use-permissions';

const store = useStore();
const { loadPermissions, hasPermission, hasAnyPermission } = usePermissions();

const menu_collapse = ref('menu-dashboard');
const permissionsLoaded = ref(false);

const can = (permission) => {
    // Mientras no se cargan permisos, no ocultar el menú.
    if (!permissionsLoaded.value) return true;
    return hasPermission(permission);
};

const canAny = (permissions) => {
    if (!permissionsLoaded.value) return true;
    return hasAnyPermission(permissions);
};

const canAdministracion = computed(() => {
    if (!permissionsLoaded.value) return true;
    return can('administracion.usuarios') || can('administracion.roles') || can('administracion.configuracion_hardware') || can('administracion.catalogos');
});

onMounted(async () => {
    await loadPermissions();
    permissionsLoaded.value = true;

    const path = window.location.pathname;
    const selector = document.querySelector('#sidebar a[href="' + path + '"]');
    if (selector) {
        const ul = selector.closest('ul.collapse');
        if (ul) {
            const toggle = ul.closest('li.menu')?.querySelector('.dropdown-toggle');
            if (toggle) setTimeout(() => toggle.click());
            const targetLink = document.querySelector('#sidebar a[data-bs-target="#' + ul.id + '"]');
            if (targetLink) targetLink.click();
        } else {
            selector.click();
        }
    }
});

const toggleMobileMenu = () => {
    if (window.innerWidth < 991) {
        store.commit('toggleSideBar', !store.state.is_show_sidebar);
    }
};
</script>
