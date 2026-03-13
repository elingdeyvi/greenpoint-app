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

                <!-- 2. Ventas -->
                <li class="menu" v-if="canVentas">
                    <a class="dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#menu-ventas" aria-controls="menu-ventas" aria-expanded="false">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-shopping-cart">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span>Ventas</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </a>
                    <ul id="menu-ventas" class="collapse submenu list-unstyled" data-bs-parent="#sidebar">
                        <!-- Sucursal tipo Venta (Villahermosa): Nueva venta. Si no hay caja abierta, redirige a Caja y avisa que debe abrirla. -->
                        <li v-if="esPerfilVenta && (can('ventas.crear') || can('ventas.ver'))">
                            <router-link
                                v-if="cajaAbierta"
                                to="/ventas"
                                @click="toggleMobileMenu"
                            >
                                Nueva venta
                            </router-link>
                            <router-link
                                v-else
                                to="/cajas"
                                @click="toggleMobileMenu"
                                class="text-warning"
                                title="Debe abrir la caja para realizar ventas"
                            >
                                Nueva venta (abrir caja)
                            </router-link>
                        </li>
                        <li v-if="esPerfilVenta && can('ventas.ver')">
                            <router-link to="/ventas/historial" @click="toggleMobileMenu">
                                Historial de ventas
                            </router-link>
                        </li>

                        <!-- Sucursal tipo Venta+Almacén (Macuspana): Entregas. Sin "Importar" manual (se importa al escanear en Vigilante). -->
                        <li v-if="esPerfilVentaAlmacen && (can('entregas.registrar') || can('ventas.ver'))">
                            <router-link to="/entregas" @click="toggleMobileMenu">
                                Entregas
                            </router-link>
                        </li>

                        <!-- Catálogos disponibles en ambos perfiles -->
                        <li v-if="can('productos.ver')">
                            <router-link to="/productos/lista" @click="toggleMobileMenu">
                                Productos
                            </router-link>
                        </li>
                        <li v-if="can('clientes.ver')">
                            <router-link to="/clientes/lista" @click="toggleMobileMenu">
                                Clientes
                            </router-link>
                        </li>
                    </ul>
                </li>

                <!-- 3. Módulo de Vigilancia (solo Macuspana: venta_almacen) -->
                <li class="menu" v-if="esPerfilVentaAlmacen && (can('entregas.registrar') || can('ventas.ver'))">
                    <a class="dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#menu-vigilancia" aria-controls="menu-vigilancia" aria-expanded="false">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-eye">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <span>Módulo de Vigilancia</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-right">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </div>
                    </a>
                    <ul id="menu-vigilancia" class="collapse submenu list-unstyled" data-bs-parent="#sidebar">
                        <li>
                            <router-link to="/entregas/vigilante" @click="toggleMobileMenu">
                                Registrar entrada (Escanear)
                            </router-link>
                        </li>
                        <li>
                            <router-link to="/entregas/vigilante" @click="toggleMobileMenu">
                                Salida local (Generar QR)
                            </router-link>
                        </li>
                    </ul>
                </li>

                <!-- 3. Inventario (solo sucursal tipo venta_almacen / Macuspana) -->
                <li class="menu" v-if="esPerfilVentaAlmacen && (can('inventario.consultar') || can('inventario.ajustar'))">
                    <a class="dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#menu-inventario" aria-controls="menu-inventario" aria-expanded="false">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-package">
                                <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line>
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                                <line x1="12" y1="22.08" x2="12" y2="12"></line>
                            </svg>
                            <span>Inventario</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </a>
                    <ul id="menu-inventario" class="collapse submenu list-unstyled" data-bs-parent="#sidebar">
                        <li>
                            <router-link to="/inventario" @click="toggleMobileMenu">Gestión de inventario</router-link>
                        </li>
                        <li>
                            <router-link to="/inventario/alertas" @click="toggleMobileMenu">Alertas de stock</router-link>
                        </li>
                    </ul>
                </li>

                <!-- 4. Caja -->
                <li class="menu" v-if="can('cajas.abrir_cerrar') || can('gastos.registrar')">
                    <a class="dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#menu-caja" aria-controls="menu-caja" aria-expanded="false">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-briefcase">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                            <span>Caja</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </a>
                    <ul id="menu-caja" class="collapse submenu list-unstyled" data-bs-parent="#sidebar">
                        <li>
                            <router-link to="/cajas" @click="toggleMobileMenu">Caja y gastos</router-link>
                        </li>
                        <li v-if="esPerfilVentaAlmacen">
                            <router-link to="/cajas/validar-pedidos" @click="toggleMobileMenu">Validación de pedidos</router-link>
                        </li>
                    </ul>
                </li>

                <!-- 5. Reportes -->
                <li class="menu" v-if="canReportes">
                    <a class="dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#menu-reportes" aria-controls="menu-reportes" aria-expanded="false">
                        <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-bar-chart-2">
                                <line x1="18" y1="20" x2="18" y2="10"></line>
                                <line x1="12" y1="20" x2="12" y2="4"></line>
                                <line x1="6" y1="20" x2="6" y2="14"></line>
                            </svg>
                            <span>Reportes</span>
                        </div>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </div>
                    </a>
                    <ul id="menu-reportes" class="collapse submenu list-unstyled" data-bs-parent="#sidebar">
                        <!-- Reporte de salidas (boletos) solo para sucursal tipo venta_almacen -->
                        <li v-if="canReportes && esPerfilVentaAlmacen">
                            <router-link to="/reportes/salidas" @click="toggleMobileMenu">Salidas (boletos)</router-link>
                        </li>
                        <!-- Reporte de ventas disponible en ambos perfiles -->
                        <li v-if="canReportes">
                            <router-link to="/reportes/ventas" @click="toggleMobileMenu">Ventas</router-link>
                        </li>
                    </ul>
                </li>

                <!-- 6. Administración -->
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
                        <li v-if="can('administracion.configuracion_hardware')">
                            <router-link to="/configuracion-hardware/lista" @click="toggleMobileMenu">Config. hardware</router-link>
                        </li>
                    </ul>
                </li>

                <!-- 7. Mi perfil -->
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
import { onMounted, ref, computed } from 'vue';
import { useStore } from 'vuex';
import { usePermissions } from '@/composables/use-permissions';
import { useCaja } from '@/composables/useCaja';

const store = useStore();
const { loadPermissions, hasPermission, hasAnyPermission } = usePermissions();
const { cajaAbierta, loadCajaAbierta } = useCaja();

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

const perfilInterfaz = computed(() => store.getters.perfilInterfaz || null);
// Si no hay perfil definido aún, asumimos perfil VENTA para no dejar el menú vacío.
const esPerfilVenta = computed(() => !perfilInterfaz.value || perfilInterfaz.value === 'VENTA');
const esPerfilVentaAlmacen = computed(() => perfilInterfaz.value === 'VENTA_ALMACEN');

const canVentas = computed(() => {
    if (!permissionsLoaded.value) return true;
    return can('ventas.crear') || can('ventas.ver') || can('entregas.registrar') || can('productos.ver') || can('clientes.ver');
});

const canReportes = computed(() => {
    if (!permissionsLoaded.value) return true;
    return canAny(['reportes.consultar', 'reportes.consultar_propios']);
});

const canAdministracion = computed(() => {
    if (!permissionsLoaded.value) return true;
    return can('administracion.usuarios') || can('administracion.roles') || can('administracion.configuracion_hardware') || can('administracion.catalogos');
});

onMounted(async () => {
    await loadPermissions();
    permissionsLoaded.value = true;
    await loadCajaAbierta();

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
