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
