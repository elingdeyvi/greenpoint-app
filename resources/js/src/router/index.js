import { createRouter, createWebHistory } from 'vue-router';

import store from '../store';

const routes = [
  // Sitio público (sin auth) - rutas directas, el layout público se resuelve vía Vuex (App.vue)
  { path: '/', name: 'PublicHome', meta: { public: true }, component: () => import('@/views/public/HomeView.vue') },
  { path: '/nosotros', name: 'PublicNosotros', meta: { public: true }, component: () => import('@/views/public/NosotrosView.vue') },
  { path: '/historia', name: 'PublicHistoria', meta: { public: true }, component: () => import('@/views/public/HistoriaView.vue') },
  { path: '/aviso', name: 'PublicAviso', meta: { public: true }, component: () => import('@/views/public/AvisoView.vue') },
  { path: '/servicios', name: 'PublicServicios', meta: { public: true }, component: () => import('@/views/public/ServiciosList.vue') },
  { path: '/servicios/:idOrSlug', name: 'PublicServicioDetalle', meta: { public: true }, component: () => import('@/views/public/ServicioDetalleView.vue') },
  { path: '/clientes', name: 'PublicClientes', meta: { public: true }, component: () => import('@/views/public/ClientesView.vue') },
  { path: '/galeria', name: 'PublicGaleria', meta: { public: true }, component: () => import('@/views/public/GaleriaView.vue') },
  { path: '/tecnologia', name: 'PublicTecnologia', meta: { public: true }, component: () => import('@/views/public/TecnologiaView.vue') },
  { path: '/contacto/tabasco', name: 'PublicContactoTabasco', meta: { public: true, contactoSlug: 'tabasco' }, component: () => import('@/views/public/ContactoView.vue') },
  { path: '/contacto/veracruz', name: 'PublicContactoVeracruz', meta: { public: true, contactoSlug: 'veracruz' }, component: () => import('@/views/public/ContactoView.vue') },
  { path: '/contacto/carmen', name: 'PublicContactoCarmen', meta: { public: true, contactoSlug: 'carmen' }, component: () => import('@/views/public/ContactoView.vue') },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/views/admin/DashboardGreenPoint.vue'),
    meta: { permission: 'dashboard.ver' },
  },

  {
    path: "/auth/login",
    component: () => import("@/layouts/auth-layout.vue"),
    children: [
      {
        path: "",
        name: "login-route",
        component: () => import("@/views/auth/login.vue"),
        meta: { layout: 'auth' },
      },
      {
        path: '/auth/pass-recovery-boxed',
        name: 'pass-recovery-boxed',
        component: () => import('@/views/auth/pass_recovery_boxed.vue'),
        meta: { layout: 'auth' },
      },
      {
        path: '/auth/changepw/:uuid',
        name: 'router-changepw-externo',
        component: () => import('@/views/users/changepw.vue'),
        meta: { layout: 'auth' },
      },
    ],
  },
  // Usuarios
  {
    path: '/users/profile',
    name: 'users-profile-router',
    component: () => import('@/views/users/profile.vue'),
  },
  {
    path: '/users/lista',
    name: 'users-lista-router',
    component: () => import('@/views/users/index.vue'),
    meta: { permission: 'administracion.usuarios' },
  },
  // Módulo de Administración - Roles y Permisos
  {
    path: '/roles/lista',
    name: 'roles-lista-router',
    component: () => import('@/views/roles/index.vue'),
    meta: { permission: 'administracion.roles' },
  },
  // Catálogos
  {
    path: '/catalogos/servicios',
    name: 'catalogos-servicios',
    component: () => import('@/views/admin/catalogos/ServiciosList.vue'),
    meta: { permission: 'catalogos.servicios' },
  },
  {
    path: '/catalogos/clientes',
    name: 'catalogos-clientes',
    component: () => import('@/views/admin/catalogos/ClientesList.vue'),
    meta: { permission: 'catalogos.clientes' },
  },
  {
    path: '/catalogos/galeria',
    name: 'catalogos-galeria',
    component: () => import('@/views/admin/catalogos/GaleriaList.vue'),
    meta: { permission: 'catalogos.galeria' },
  },
  {
    path: '/catalogos/banners',
    name: 'catalogos-banners',
    component: () => import('@/views/admin/catalogos/BannersList.vue'),
    meta: { permission: 'catalogos.banners' },
  },
  {
    path: '/catalogos/contactos',
    name: 'catalogos-contactos',
    component: () => import('@/views/admin/catalogos/ContactosList.vue'),
    meta: { permission: 'catalogos.contactos' },
  },
  {
    path: '/catalogos/redes-sociales',
    name: 'catalogos-redes-sociales',
    component: () => import('@/views/admin/catalogos/RedesSocialesList.vue'),
    meta: { permission: 'catalogos.redes_sociales' },
  },
  // Configuración general
  {
    path: '/configuracion',
    name: 'configuracion',
    component: () => import('@/views/admin/ConfiguracionView.vue'),
    meta: { permission: 'administracion.configuracion_critica' },
  },
  // Formularios de contacto
  {
    path: '/formularios-contacto',
    name: 'formularios-contacto',
    component: () => import('@/views/admin/FormulariosContactoList.vue'),
    meta: { permission: 'formularios_contacto.ver' },
  },
  // Módulos administrables
  {
    path: '/modulos/nosotros',
    name: 'modulos-nosotros',
    component: () => import('@/views/admin/modulos/NosotrosView.vue'),
    meta: { permission: 'modulos.nosotros' },
  },
  {
    path: '/modulos/historia',
    name: 'modulos-historia',
    component: () => import('@/views/admin/modulos/HistoriaView.vue'),
    meta: { permission: 'modulos.historia' },
  },
  {
    path: '/modulos/tecnologia',
    name: 'modulos-tecnologia',
    component: () => import('@/views/admin/modulos/TecnologiaView.vue'),
    meta: { permission: 'modulos.tecnologia' },
  },
  {
    path: '/modulos/aviso',
    name: 'modulos-aviso',
    component: () => import('@/views/admin/modulos/AvisoView.vue'),
    meta: { permission: 'modulos.aviso' },
  },
];

const router = new createRouter({
  // mode: 'history',
  history: createWebHistory(),
  linkExactActiveClass: 'active',
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { left: 0, top: 0 };
    }
  },
});

router.beforeEach(async (to, from, next) => {
  const token = window.localStorage.getItem("token");

  // Rutas públicas: sin auth, layout public
  if (to.meta && to.meta.public) {
    store.commit('setLayout', 'public');
    if (to.name === 'PublicHome' && token) {
      try {
        const { usePermissions } = await import('@/composables/use-permissions');
        const { loadPermissions, hasPermission } = usePermissions();
        await loadPermissions();
        if (hasPermission('dashboard.ver')) {
          next({ name: 'Dashboard', replace: true });
          return;
        }
      } catch (e) { /* ignore */ }
    }
    next();
    return;
  }

  if (to.meta && to.meta.layout && to.meta.layout === 'auth') {
    store.commit('setLayout', 'auth');
  } else {
    store.commit('setLayout', 'app');
  }

  if (to.name !== 'login-route' && to.name !== 'registro-route' && !token) {
    next('/auth/login');
    return;
  } else if (token && to.name === 'login-route') {
    store.commit('setLayout', 'app');
    next("/");
    return;
  }

  // Verificar permisos si la ruta requiere uno
  if (to.meta && to.meta.permission && token) {
    try {
      const { usePermissions } = await import('@/composables/use-permissions');
      const { loadPermissions, hasPermission, hasAnyPermission } = usePermissions();
      
      await loadPermissions();
      
      const requiredPermission = to.meta.permission;
      let hasAccess = false;
      
      if (Array.isArray(requiredPermission)) {
        hasAccess = hasAnyPermission(requiredPermission);
      } else {
        hasAccess = hasPermission(requiredPermission);
      }
      
      if (!hasAccess) {
        // Redirigir a home si no tiene permisos
        next({ name: 'Dashboard', replace: true });
        return;
      }
    } catch (error) {
      console.error('Error checking permissions:', error);
      // En caso de error, permitir acceso (fallback)
    }
  }
  
  // Si intenta acceder al home público y tiene permiso de dashboard, redirigir al dashboard
  if (to.name === 'PublicHome' && token) {
    try {
      const { usePermissions } = await import('@/composables/use-permissions');
      const { loadPermissions, hasPermission } = usePermissions();
      
      await loadPermissions();
      
      if (hasPermission('dashboard.ver')) {
        next({ name: 'Dashboard', replace: true });
        return;
      }
    } catch (error) {
      console.error('Error checking dashboard permission:', error);
    }
  }
  
  next();
});

export default router;
