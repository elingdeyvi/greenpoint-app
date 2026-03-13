<template>
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="panel p-5">
                    <div class="row justify-content-center">
                        <div class="col-md-8 text-center">
                            <div class="welcome-content">
                                <div class="welcome-icon mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck text-primary">
                                        <rect x="1" y="3" width="15" height="13"></rect>
                                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                    </svg>
                                </div>
                                <h2 class="mb-3">Bienvenido al Sistema de Control de Salidas</h2>
                                <p class="lead text-muted mb-4">
                                    Sistema de gestión para el control de salidas de volteos
                                </p>
                                
                                <div class="row mt-5">
                                    <div class="col-md-4 mb-4" v-if="can('operaciones.generar')">
                                        <div class="card h-100 text-center">
                                            <div class="card-body">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle text-primary mb-3">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <line x1="12" y1="8" x2="12" y2="16"></line>
                                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                                </svg>
                                                <h5 class="card-title">Generar Boleto</h5>
                                                <p class="card-text text-muted">Crea nuevos boletos de salida</p>
                                                <router-link to="/boletos/generar" class="btn btn-primary btn-sm">
                                                    Ir a Generar
                                                </router-link>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-4" v-if="can('operaciones.verificar')">
                                        <div class="card h-100 text-center">
                                            <div class="card-body">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle text-success mb-3">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                                </svg>
                                                <h5 class="card-title">Verificar Boleto</h5>
                                                <p class="card-text text-muted">Valida boletos en el punto de salida</p>
                                                <router-link to="/boletos/verificar" class="btn btn-success btn-sm">
                                                    Ir a Verificar
                                                </router-link>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4 mb-4" v-if="canAny(['reportes.consultar', 'reportes.consultar_propios'])">
                                        <div class="card h-100 text-center">
                                            <div class="card-body">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2 text-info mb-3">
                                                    <line x1="18" y1="20" x2="18" y2="10"></line>
                                                    <line x1="12" y1="20" x2="12" y2="4"></line>
                                                    <line x1="6" y1="20" x2="6" y2="14"></line>
                                                </svg>
                                                <h5 class="card-title">Reportes</h5>
                                                <p class="card-text text-muted">Consulta reportes y estadísticas</p>
                                                <router-link to="/reportes/salidas" class="btn btn-info btn-sm">
                                                    Ver Reportes
                                                </router-link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-5">
                                    <p class="text-muted">
                                        <small>Selecciona una opción del menú lateral para comenzar</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { onMounted, ref } from "vue";
    import { useMeta } from "../composables/use-meta";
    import { usePermissions } from '@/composables/use-permissions';
    
    useMeta({ title: "Bienvenido - SCV" });
    
    const { loadPermissions, hasPermission, hasAnyPermission } = usePermissions();
    const permissionsLoaded = ref(false);
    
    const can = (permission) => {
        if (!permissionsLoaded.value) return false;
        return hasPermission(permission);
    };
    
    const canAny = (permissions) => {
        if (!permissionsLoaded.value) return false;
        return hasAnyPermission(permissions);
    };
    
    onMounted(async () => {
        await loadPermissions();
        permissionsLoaded.value = true;
    });
</script>

<style scoped>
.welcome-content {
    padding: 2rem 0;
}

.welcome-icon {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid #e0e0e0;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>
