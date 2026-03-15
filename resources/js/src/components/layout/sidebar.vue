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

                <!-- Catálogos -->
                <li class="menu" v-if="canCatalogos">
                    <a class="dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#menu-catalogos" aria-controls="menu-catalogos" aria-expanded="false">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-grid"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            <span>Catálogos</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </a>
                    <ul id="menu-catalogos" class="collapse submenu list-unstyled" data-bs-parent="#sidebar">
                        <li v-if="can('catalogos.servicios')"><router-link to="/catalogos/servicios" @click="toggleMobileMenu">Servicios</router-link></li>
                        <li v-if="can('catalogos.clientes')"><router-link to="/catalogos/clientes" @click="toggleMobileMenu">Clientes</router-link></li>
                        <li v-if="can('catalogos.galeria')"><router-link to="/catalogos/galeria" @click="toggleMobileMenu">Galería</router-link></li>
                        <li v-if="can('catalogos.banners')"><router-link to="/catalogos/banners" @click="toggleMobileMenu">Banners</router-link></li>
                        <li v-if="can('catalogos.contactos')"><router-link to="/catalogos/contactos" @click="toggleMobileMenu">Contactos</router-link></li>
                        <li v-if="can('catalogos.redes_sociales')"><router-link to="/catalogos/redes-sociales" @click="toggleMobileMenu">Redes sociales</router-link></li>
                    </ul>
                </li>

                <!-- Módulos -->
                <li class="menu" v-if="canModulos">
                    <a class="dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#menu-modulos" aria-controls="menu-modulos" aria-expanded="false">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                            <span>Módulos</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </a>
                    <ul id="menu-modulos" class="collapse submenu list-unstyled" data-bs-parent="#sidebar">
                        <li v-if="can('modulos.nosotros')"><router-link to="/modulos/nosotros" @click="toggleMobileMenu">Nosotros</router-link></li>
                        <li v-if="can('modulos.historia')"><router-link to="/modulos/historia" @click="toggleMobileMenu">Historia</router-link></li>
                        <li v-if="can('modulos.tecnologia')"><router-link to="/modulos/tecnologia" @click="toggleMobileMenu">Tecnología</router-link></li>
                        <li v-if="can('modulos.aviso')"><router-link to="/modulos/aviso" @click="toggleMobileMenu">Aviso de Privacidad</router-link></li>
                    </ul>
                </li>

                <!-- Configuración -->
                <li class="menu" v-if="can('administracion.configuracion_critica')">
                    <router-link to="/configuracion" class="dropdown-toggle" @click="toggleMobileMenu">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-sliders"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                            <span>Configuración</span>
                        </div>
                    </router-link>
                </li>

                <!-- Formularios de contacto -->
                <li class="menu" v-if="can('formularios_contacto.ver')">
                    <router-link to="/formularios-contacto" class="dropdown-toggle" @click="toggleMobileMenu">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-mail"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            <span>Formularios de contacto</span>
                        </div>
                    </router-link>
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
import { computed, onMounted, ref } from 'vue';
import { useStore } from 'vuex';
import { usePermissions } from '@/composables/use-permissions';

const store = useStore();
const { loadPermissions, hasPermission, hasAnyPermission } = usePermissions();

const menu_collapse = ref('menu-dashboard');
const permissionsLoaded = ref(false);

const can = (permission) => {
    if (!permissionsLoaded.value) return true;
    return hasPermission(permission);
};

const canAny = (permissions) => {
    if (!permissionsLoaded.value) return true;
    return hasAnyPermission(permissions);
};

const canAdministracion = computed(() => {
    if (!permissionsLoaded.value) return true;
    return can('administracion.usuarios') || can('administracion.roles') || can('administracion.configuracion_critica');
});

const canCatalogos = computed(() => {
    if (!permissionsLoaded.value) return true;
    return canAny(['catalogos.servicios', 'catalogos.clientes', 'catalogos.galeria', 'catalogos.banners', 'catalogos.contactos', 'catalogos.redes_sociales']);
});

const canModulos = computed(() => {
    if (!permissionsLoaded.value) return true;
    return canAny(['modulos.nosotros', 'modulos.historia', 'modulos.tecnologia', 'modulos.aviso']);
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
