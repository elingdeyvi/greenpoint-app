<template>
    <div>
        <!--  BEGIN NAVBAR  -->
        <div class="header-container fixed-top">
            <header class="header navbar navbar-expand-sm">
                <ul class="navbar-item theme-brand flex-row text-center">
                    <li class="nav-item theme-logo">
                        <router-link to="/">
                            <img src="/assets/images/logo.png" class="navbar-logo" alt="GreenPoint" />
                        </router-link>
                    </li>
                    <li class="nav-item theme-text">
                        <router-link to="/" class="nav-link"> GreenPoint </router-link>
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
                            <img src="/assets/images/user-avtar.svg" alt="avatar" />
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
        <!--  BEGIN NAVBAR  -->
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
        <!--  END NAVBAR  -->
        <!--  BEGIN TOPBAR  -->
                <div class="topbar-nav header navbar" role="banner">
            <nav class="topbar">
                <ul class="list-unstyled menu-categories" id="topAccordion">
                    <li class="menu single-menu" v-if="can('dashboard.ver')">
                        <a href="javascript:;" class="dropdown-toggle autodroprown">
                            <div class=""><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg><span>Inicio</span></div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a>
                        <ul class="collapse submenu list-unstyled">
                            <li><router-link to="/dashboard">Dashboard</router-link></li>
                        </ul>
                    </li>

                    <li class="menu single-menu" v-if="canHeaderVentas">
                        <a href="javascript:;" class="dropdown-toggle">
                            <div class=""><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-shopping-cart"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg><span>Ventas</span></div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a>
                        <ul class="collapse submenu list-unstyled">
                            <!-- Perfil VENTA (Villahermosa): Nueva venta. Si no hay caja abierta, redirige a Caja y avisa que debe abrirla. -->
                            <li v-if="esPerfilVenta && (can('ventas.crear') || can('ventas.ver'))">
                                <router-link v-if="cajaAbierta" to="/ventas">Nueva venta</router-link>
                                <router-link
                                    v-else
                                    to="/cajas"
                                    class="text-warning"
                                    title="Debe abrir la caja para realizar ventas"
                                >
                                    Nueva venta (abrir caja)
                                </router-link>
                            </li>

                            <!-- Perfil VENTA_ALMACEN (Macuspana): Entregas y Vigilante. Sin \"Importar pedido\" manual. -->
                            <li v-if="esPerfilVentaAlmacen && (can('entregas.registrar') || can('ventas.ver'))"><router-link to="/entregas">Entregas</router-link></li>
                            <li v-if="esPerfilVentaAlmacen && (can('entregas.registrar') || can('ventas.ver'))"><router-link to="/entregas/vigilante">Control de acceso (Vigilante)</router-link></li>

                            <!-- Catálogos disponibles en ambos perfiles -->
                            <li v-if="can('productos.ver')"><router-link to="/productos/lista">Productos</router-link></li>
                            <li v-if="can('clientes.ver')"><router-link to="/clientes/lista">Clientes</router-link></li>
                        </ul>
                    </li>

                    <li class="menu single-menu" v-if="esPerfilVentaAlmacen && (can('inventario.consultar') || can('inventario.ajustar'))">
                        <a href="javascript:;" class="dropdown-toggle">
                            <div class=""><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-package"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg><span>Inventario</span></div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a>
                        <ul class="collapse submenu list-unstyled">
                            <li><router-link to="/inventario">Gestión de inventario</router-link></li>
                            <li><router-link to="/inventario/alertas">Alertas de stock</router-link></li>
                        </ul>
                    </li>

                    <li class="menu single-menu" v-if="can('cajas.abrir_cerrar') || can('gastos.registrar')">
                        <router-link to="/cajas">
                            <div class=""><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-briefcase"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg><span>Caja</span></div>
                        </router-link>
                    </li>

                    <li class="menu single-menu" v-if="canAny(['reportes.consultar', 'reportes.consultar_propios'])">
                        <a href="javascript:;" class="dropdown-toggle">
                            <div class=""><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-bar-chart-2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg><span>Reportes</span></div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a>
                        <ul class="collapse submenu list-unstyled">
                            <li v-if="esPerfilVentaAlmacen"><router-link to="/reportes/salidas">Salidas (boletos)</router-link></li>
                            <li><router-link to="/reportes/ventas">Ventas</router-link></li>
                        </ul>
                    </li>

                    <li class="menu single-menu" v-if="canHeaderAdministracion">
                        <a href="javascript:;" class="dropdown-toggle">
                            <div class=""><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg><span>Administración</span></div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a>
                        <ul class="collapse submenu list-unstyled">
                            <li v-if="can('administracion.usuarios')"><router-link to="/users/lista">Usuarios</router-link></li>
                            <li v-if="can('administracion.roles')"><router-link to="/roles/lista">Roles y permisos</router-link></li>
                            <li v-if="can('administracion.configuracion_hardware')"><router-link to="/configuracion-hardware/lista">Config. hardware</router-link></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
        <loading
            v-model:active="isLoading"
            :can-cancel="false"
            :is-full-page="true"
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
    import { useCaja } from '@/composables/useCaja';
    import Loading from "vue-loading-overlay";
    import "vue-loading-overlay/dist/css/index.css";
    const store = useStore();

    const isLoading = ref(false);
    const currentUser = ref(null);
    const { loadPermissions, hasPermission, hasAnyPermission } = usePermissions();
    const { cajaAbierta, loadCajaAbierta } = useCaja();
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

    const perfilInterfaz = computed(() => store.getters.perfilInterfaz || null);
    // Si no hay perfil definido aún, asumimos perfil VENTA para no dejar el menú vacío.
    const esPerfilVenta = computed(() => !perfilInterfaz.value || perfilInterfaz.value === 'VENTA');
    const esPerfilVentaAlmacen = computed(() => perfilInterfaz.value === 'VENTA_ALMACEN');

    const canHeaderVentas = computed(() => {
        if (!permissionsLoaded.value) return true;
        return can('ventas.crear') || can('ventas.ver') || can('entregas.registrar') || can('productos.ver') || can('clientes.ver');
    });

    const canHeaderAdministracion = computed(() => {
        if (!permissionsLoaded.value) return true;
        return can('administracion.usuarios') || can('administracion.roles') || can('administracion.configuracion_hardware') || can('administracion.catalogos');
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
            await loadCajaAbierta();
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
