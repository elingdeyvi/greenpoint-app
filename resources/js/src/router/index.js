import { createRouter, createWebHistory } from 'vue-router';

import store from '../store';

const routes = [
  { 
    path: '/', 
    name: 'Home', 
    component: () => import('../views/index.vue'),
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('../views/dashboard.vue'),
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
  {
    path: '/marcas/lista',
    name: 'marcas-lista-router',
    component: () => import('@/views/marcas/index.vue'),
  },
  // Módulo de Administración
  {
    path: '/configuracion-hardware/lista',
    name: 'configuracion-hardware-lista-router',
    component: () => import('@/views/configuracion-hardware/index.vue'),
    meta: { permission: 'administracion.configuracion_hardware' },
  },
  // Ventas y Entregas
  {
    path: '/ventas',
    name: 'ventas-router',
    component: () => import('@/views/ventas/index.vue'),
    meta: { permission: ['ventas.crear', 'ventas.ver'] },
  },
  {
    path: '/ventas/historial',
    name: 'ventas-historial-router',
    component: () => import('@/views/ventas/historial.vue'),
    meta: { permission: 'ventas.ver', perfilInterfaz: 'VENTA' },
  },
  {
    path: '/ventas/importar-pedido',
    name: 'ventas-importar-pedido-router',
    component: () => import('@/views/ventas/importar-pedido.vue'),
    meta: { permission: ['ventas.crear', 'ventas.ver'] },
  },
  {
    path: '/entregas',
    name: 'entregas-router',
    component: () => import('@/views/entregas/dashboard.vue'),
    meta: { permission: ['entregas.registrar', 'ventas.ver'] },
  },
  {
    path: '/entregas/vigilante',
    name: 'entregas-vigilante-router',
    component: () => import('@/views/entregas/VigilanteAcceso.vue'),
    meta: { permission: ['entregas.registrar', 'ventas.ver'] },
  },
  {
    path: '/productos/lista',
    name: 'productos-lista-router',
    component: () => import('@/views/productos/index.vue'),
    meta: { permission: 'productos.ver' },
  },
  {
    path: '/clientes/lista',
    name: 'clientes-lista-router',
    component: () => import('@/views/clientes/index.vue'),
    meta: { permission: 'clientes.ver' },
  },
  {
    path: '/inventario/alertas',
    name: 'inventario-alertas-router',
    component: () => import('@/views/inventario/alertas.vue'),
    meta: { permission: ['inventario.consultar', 'inventario.ajustar'] },
  },
  {
    path: '/inventario',
    name: 'inventario-index-router',
    component: () => import('@/views/inventario/index.vue'),
    meta: { permission: ['inventario.consultar', 'inventario.ajustar'] },
  },
  {
    path: '/cajas',
    name: 'cajas-router',
    component: () => import('@/views/cajas/index.vue'),
    meta: { permission: ['cajas.abrir_cerrar', 'gastos.registrar'] },
  },
  {
    path: '/cajas/validar-pedidos',
    name: 'cajas-validar-pedidos-router',
    component: () => import('@/views/cajas/validar-pedidos.vue'),
    meta: { permission: ['cajas.abrir_cerrar', 'ventas.ver'] },
  },
  // Módulo de Reportes - SCV
  {
    path: '/reportes/salidas',
    name: 'reportes-salidas-router',
    component: () => import('@/views/reportes/salidas.vue'),
    meta: { permission: ['reportes.consultar', 'reportes.consultar_propios'] },
  },
  {
    path: '/reportes/ventas',
    name: 'reportes-ventas-router',
    component: () => import('@/views/reportes/ventas.vue'),
    meta: { permission: ['reportes.consultar', 'reportes.consultar_propios'] },
  },
  // Módulo de Administración - Roles y Permisos
  {
    path: '/roles/lista',
    name: 'roles-lista-router',
    component: () => import('@/views/roles/index.vue'),
    meta: { permission: 'administracion.roles' },
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
  
  if (to.meta && to.meta.layout && to.meta.layout == 'auth') {
    store.commit('setLayout', 'auth');
  } else {
    store.commit('setLayout', 'app');
  }

  if (to.name !== 'login-route' && to.name !== 'registro-route' && !token) {
    next('/auth/login')
    return;
  } else if (token && to.name == 'login-route') {
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
        next({ name: 'Home', replace: true });
        return;
      }
    } catch (error) {
      console.error('Error checking permissions:', error);
      // En caso de error, permitir acceso (fallback)
    }
  }
  
  // Restringir rutas según perfil de interfaz (sucursal)
  const perfil = store.getters.perfilInterfaz || null;
  if (perfil && to.name) {
    const blockedByVenta = ['ventas-importar-pedido-router', 'entregas-router', 'entregas-vigilante-router', 'reportes-salidas-router'];
    const blockedByVentaAlmacen = ['ventas-router', 'ventas-historial-router'];

    if (perfil === 'VENTA' && blockedByVenta.includes(to.name)) {
      next({ name: 'Dashboard', replace: true });
      return;
    }
    if (perfil === 'VENTA_ALMACEN' && blockedByVentaAlmacen.includes(to.name)) {
      next({ name: 'Dashboard', replace: true });
      return;
    }
  }
  
  // Si intenta acceder a Home y tiene permiso de dashboard, redirigir al dashboard
  if (to.name === 'Home' && token) {
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
