import { ref } from 'vue';
import * as AuthRepository from '@/repositories/AuthRepository';

const userPermissions = ref(null);
const isLoading = ref(false);

export const usePermissions = () => {
    const loadPermissions = async () => {
        if (userPermissions.value !== null) {
            return userPermissions.value;
        }

        try {
            isLoading.value = true;
            const response = await AuthRepository.permissions();
            
            // Manejar diferentes formatos de respuesta
            if (response && typeof response === 'object') {
                // Si la respuesta ya tiene el formato correcto
                if (response.all !== undefined && response.permissions !== undefined) {
                    userPermissions.value = response;
                    return response;
                }
                // Si viene en formato antiguo (roles y permissions)
                if (response.roles && response.permissions) {
                    const isAdmin = response.roles.includes('Administrador');
                    userPermissions.value = {
                        all: isAdmin,
                        permissions: response.permissions || [],
                        roles: response.roles
                    };
                    return userPermissions.value;
                }
            }
            
            // Si la respuesta es 0 o falsy, establecer valores por defecto
            userPermissions.value = {
                all: false,
                permissions: []
            };
            return userPermissions.value;
        } catch (error) {
            console.error('Error loading permissions:', error);
            userPermissions.value = {
                all: false,
                permissions: []
            };
            return userPermissions.value;
        } finally {
            isLoading.value = false;
        }
    };

    const hasPermission = (permission) => {
        if (!userPermissions.value) {
            return false;
        }
        
        // Si el usuario tiene todos los permisos (Administrador)
        if (userPermissions.value.all === true) {
            return true;
        }
        
        // Verificar si tiene el permiso específico
        return userPermissions.value.permissions?.includes(permission) || false;
    };

    const hasAnyPermission = (permissions) => {
        if (!Array.isArray(permissions)) {
            return hasPermission(permissions);
        }
        
        return permissions.some(permission => hasPermission(permission));
    };

    const can = (permission) => {
        return hasPermission(permission);
    };

    const resetPermissions = () => {
        userPermissions.value = null;
    };

    return {
        userPermissions,
        isLoading,
        loadPermissions,
        hasPermission,
        hasAnyPermission,
        can,
        resetPermissions
    };
};
