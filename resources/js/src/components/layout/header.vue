<template>
    <div>
        <!--  BEGIN NAVBAR  -->
        <div class="header-container fixed-top">
            <header class="header navbar navbar-expand-sm">
                <ul class="navbar-item theme-brand flex-row text-center">
                    <li class="nav-item theme-logo">
                        <router-link to="/">
                            <img
                                :src="logoInner"
                                class="navbar-logo"
                                alt="GreenPoint"
                                style="height: 40px; width: auto; object-fit: contain;"
                            />
                        </router-link>
                    </li>
                </ul>
                <div class="d-none horizontal-menu">
                    <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom" @click="$store.commit('toggleSideBar', !$store.state.is_show_sidebar)">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="feather feather-menu"
                        >
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </a>
                </div>

                <div class="navbar-item flex-row ms-md-auto">
                    <div class="nav-item user-name me-3 d-flex align-items-center" v-if="currentUser">
                        <span class="text-muted me-2">Bienvenido,</span>
                        <strong>{{ currentUser.name }}</strong>
                    </div>
                    <div class="dropdown nav-item user-profile-dropdown btn-group">
                        <a href="javascript:;" id="ddluser" data-bs-toggle="dropdown" aria-expanded="false" class="btn dropdown-toggle btn-icon-only user nav-link">
                            <img :src="userAvatar" alt="avatar" />
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right m-0" aria-labelledby="ddluser">
                            <li role="presentation" class="dropdown-header" v-if="currentUser">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ currentUser.name }}</span>
                                    <small class="text-muted">{{ currentUser.email }}</small>
                                    <small class="text-muted" v-if="currentUser.roles && currentUser.roles.length > 0">
                                        Rol: {{ currentUser.roles[0].name }}
                                    </small>
                                </div>
                            </li>
                            <li role="presentation"><hr class="dropdown-divider" /></li>
                            <li role="presentation">
                                <router-link to="/users/profile" class="dropdown-item">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="feather feather-user"
                                    >
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                    Perfil
                                </router-link>
                            </li>
                            <li role="presentation">
                                <a
                                    v-on:click.stop="logout"
                                    class="dropdown-item center-login"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="24"
                                        height="24"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="feather feather-log-out"
                                    >
                                        <path
                                            d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"
                                        ></path>
                                        <polyline
                                            points="16 17 21 12 16 7"
                                        ></polyline>
                                        <line
                                            x1="21"
                                            y1="12"
                                            x2="9"
                                            y2="12"
                                        ></line>
                                    </svg>
                                    Cerrar
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
        </div>
        <!--  END NAVBAR  -->
        <!--  BEGIN SUB-HEADER (breadcrumb + sidebar toggle) -->
        <div class="sub-header-container">
            <header class="header navbar navbar-expand-sm">
                <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom" @click="$store.commit('toggleSideBar', !$store.state.is_show_sidebar)">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-menu"
                    >
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </a>

                <div id="breadcrumb" class="vue-portal-target"></div>
            </header>
        </div>
        <!--  END SUB-HEADER  -->
        <!--  BEGIN TOPBAR (navegación de alto nivel para GreenPoint) -->
        <div class="topbar-nav header navbar" role="banner">
            <nav class="topbar w-100">
                <ul class="list-unstyled menu-categories mb-0 d-flex align-items-center" id="topAccordion">
                    <!-- Dashboard -->
                    <li class="menu single-menu" v-if="can('dashboard.ver')">
                        <router-link to="/dashboard">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-home">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                                <span>Dashboard</span>
                            </div>
                        </router-link>
                    </li>

                    <!-- Contenido (Catálogos + Módulos) -->
                    <li class="menu single-menu" v-if="canCatalogos || canModulos">
                        <a href="javascript:;" class="dropdown-toggle">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-layers">
                                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                    <polyline points="2 17 12 22 22 17"></polyline>
                                    <polyline points="2 12 12 17 22 12"></polyline>
                                </svg>
                                <span>Contenido</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-down">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </a>
                        <ul class="collapse submenu list-unstyled">
                            <li v-if="canCatalogos">
                                <router-link to="/catalogos/servicios">Catálogos</router-link>
                            </li>
                            <li v-if="canModulos">
                                <router-link to="/modulos/nosotros">Módulos administrables</router-link>
                            </li>
                        </ul>
                    </li>

                    <!-- Formularios de contacto -->
                    <li class="menu single-menu" v-if="can('formularios_contacto.ver')">
                        <router-link to="/formularios-contacto">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-mail">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <span>Mensajes</span>
                            </div>
                        </router-link>
                    </li>

                    <!-- Configuración -->
                    <li class="menu single-menu" v-if="can('administracion.configuracion_critica')">
                        <router-link to="/configuracion">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-sliders">
                                    <line x1="4" y1="21" x2="4" y2="14"></line>
                                    <line x1="4" y1="10" x2="4" y2="3"></line>
                                    <line x1="12" y1="21" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12" y2="3"></line>
                                    <line x1="20" y1="21" x2="20" y2="16"></line>
                                    <line x1="20" y1="12" x2="20" y2="3"></line>
                                    <line x1="1" y1="14" x2="7" y2="14"></line>
                                    <line x1="9" y1="8" x2="15" y2="8"></line>
                                    <line x1="17" y1="16" x2="23" y2="16"></line>
                                </svg>
                                <span>Configuración</span>
                            </div>
                        </router-link>
                    </li>

                    <!-- Administración (usuarios / roles) -->
                    <li class="menu single-menu" v-if="canHeaderAdministracion">
                        <a href="javascript:;" class="dropdown-toggle">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-shield">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                                <span>Administración</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-down">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </a>
                        <ul class="collapse submenu list-unstyled">
                            <li v-if="can('administracion.usuarios')">
                                <router-link to="/users/lista">Usuarios</router-link>
                            </li>
                            <li v-if="can('administracion.roles')">
                                <router-link to="/roles/lista">Roles y permisos</router-link>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
        <loading
            :active="isLoading"
            :can-cancel="false"
            :is-full-page="true"
            @update:active="isLoading = $event"
        />
        <!--  END TOPBAR  -->
    </div>
</template>

<script setup>
    import { onMounted, ref, reactive, computed } from 'vue';
    import { useI18n } from 'vue-i18n';
    import { useStore } from 'vuex';
    import * as AuthRepository from "@/repositories/AuthRepository";
    import * as UserRepository from "@/repositories/UserRepository";
    import { usePermissions } from '@/composables/use-permissions';
    import logoInner from '@/assets/images/logo-inner.png';
    import userAvatar from '@/assets/images/user-avtar.svg';
    import Loading from "vue-loading-overlay";
    import "vue-loading-overlay/dist/css/index.css";
    const store = useStore();

    const isLoading = ref(false);
    const currentUser = ref(null);
    const { loadPermissions, hasPermission, hasAnyPermission } = usePermissions();
    const permissionsLoaded = ref(false);

    const selectedLang = ref(null);
    const countryList = ref(store.state.countryList);

    const i18n = reactive(useI18n());

    const can = (permission) => {
        // Mientras no se cargan permisos, no ocultar el menú.
        if (!permissionsLoaded.value) return true;
        return hasPermission(permission);
    };

    const canAny = (permissions) => {
        if (!permissionsLoaded.value) return true;
        return hasAnyPermission(permissions);
    };

    const canCatalogos = computed(() => {
        if (!permissionsLoaded.value) return true;
        return hasAnyPermission(['catalogos.servicios', 'catalogos.clientes', 'catalogos.galeria', 'catalogos.banners', 'catalogos.contactos', 'catalogos.redes_sociales']);
    });

    const canHeaderAdministracion = computed(() => {
        if (!permissionsLoaded.value) return true;
        return can('administracion.usuarios') || can('administracion.roles');
    });

    const loadCurrentUser = async () => {
        try {
            const token = window.localStorage.getItem("token");
            if (token) {
                const userData = await UserRepository.getuser();
                currentUser.value = userData;
            }
        } catch (error) {
            console.error('Error loading current user:', error);
        }
    };

    onMounted(async () => {
        try {
            selectedLang.value = window.$appSetting.toggleLanguage();
            toggleMode();
            await loadCurrentUser();
            await loadPermissions();
            permissionsLoaded.value = true;
        } finally {
            isLoading.value = false;
        }
    });

    const toggleMode = (mode) => {
        window.$appSetting.toggleMode(mode);
    };

    const changeLanguage = (item) => {
        selectedLang.value = item;
        i18n.locale = item.code;
        window.$appSetting.toggleLanguage(item);
    };
    const logout = async () => {
        isLoading.value = true;
        try {
            const token = window.localStorage.getItem("token");
            if (token) {
                const idToken = token.includes("|") ? token.split("|")[0] : token;
                await AuthRepository.expireTokens(idToken);
            }
            localStorage.removeItem("token");
            location.reload("/");
        } catch (error) {
            console.log(error);
        } finally {
            isLoading.value = false;
        }
    };
</script>
