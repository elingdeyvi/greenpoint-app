import { defineStore } from 'pinia';
import * as ConfiguracionEmpresaRepository from '@/repositories/ConfiguracionEmpresaRepository';
import defaultLogo from '@/assets/images/logo-entradas.svg';

export const useConfiguracionEmpresaStore = defineStore('configuracionEmpresa', {
    state: () => ({
        configuracion: {},
        logoUrl: defaultLogo,
        isLoading: false,
        isLoaded: false
    }),

    getters: {
        getLogoUrl: (state) => state.logoUrl,
        getConfiguracion: (state) => state.configuracion,
        getNombreCorto: (state) => state.configuracion.nombre_corto || 'FA',
        getBienvenida: (state) => state.configuracion.bienvenida || '¡Bienvenido a Fundación Angélica!',
        getTituloMensaje: (state) => state.configuracion.titulo_mensaje || 'Transformando Vidas, Construyendo Esperanza',
        getMensajeDescripcion: (state) => state.configuracion.mensaje_descripcion || 'Cada apoyo que otorgamos es una oportunidad de cambiar vidas.',
        getMensajeAlerta: (state) => state.configuracion.mensaje_alerta || '¡Gracias por tu labor!',
        getIconoMensajeUrl: (state) => state.configuracion.icono_mensaje_url || '',
        getFaviconUrl: (state) => state.configuracion.favicon_url || ''
    },

    actions: {
        async cargarConfiguracion() {
            if (this.isLoaded) return; // Evitar cargar múltiples veces

            this.isLoading = true;
            try {
                const response = await ConfiguracionEmpresaRepository.getConfiguracion();
                this.configuracion = response.data;

                // Actualizar logo si existe
                if (response.data.logo_url) {
                    this.logoUrl = response.data.logo_url;
                }

                this.isLoaded = true;
            } catch (error) {
                console.error('Error cargando configuración de empresa:', error);
                // Mantener valores por defecto
            } finally {
                this.isLoading = false;
            }
        },

        async cargarConfiguracionPublica() {
            this.isLoading = true;
            try {
                const response = await ConfiguracionEmpresaRepository.getPublicConfiguracion();
                this.configuracion = response.data;

                // Actualizar logo si existe
                if (response.data.logo_url) {
                    this.logoUrl = response.data.logo_url;
                }

                this.isLoaded = true;
            } catch (error) {
                console.error('Error cargando configuración pública de empresa:', error);
                // Mantener valores por defecto
            } finally {
                this.isLoading = false;
            }
        },

        async actualizarConfiguracion(datos) {
            try {
                const response = await ConfiguracionEmpresaRepository.updateConfiguracion(datos);
                this.configuracion = response.data;

                // Actualizar logo si cambió
                if (response.data.logo_url) {
                    this.logoUrl = response.data.logo_url;
                }

                // Marcar como cargado para evitar recargar
                this.isLoaded = true;

                return response;
            } catch (error) {
                console.error('Error actualizando configuración:', error);
                throw error;
            }
        },

        async subirLogo(formData) {
            try {
                const response = await ConfiguracionEmpresaRepository.uploadLogo(formData);
                this.configuracion = response.data;
                this.logoUrl = response.data.logo_url;
                return response;
            } catch (error) {
                console.error('Error subiendo logo:', error);
                throw error;
            }
        },

        async subirFavicon(formData) {
            try {
                const response = await ConfiguracionEmpresaRepository.uploadFavicon(formData);
                this.configuracion = response.data;
                return response;
            } catch (error) {
                console.error('Error subiendo favicon:', error);
                throw error;
            }
        },

        async subirIconoMensaje(formData) {
            try {
                const response = await ConfiguracionEmpresaRepository.uploadIconoMensaje(formData);
                this.configuracion = response.data;
                return response;
            } catch (error) {
                console.error('Error subiendo icono del mensaje:', error);
                throw error;
            }
        },

        async eliminarLogo() {
            try {
                const response = await ConfiguracionEmpresaRepository.deleteLogo();
                this.configuracion = response.data;
                this.logoUrl = defaultLogo; // Volver al logo por defecto
                return response;
            } catch (error) {
                console.error('Error eliminando logo:', error);
                throw error;
            }
        },

        async eliminarFavicon() {
            try {
                const response = await ConfiguracionEmpresaRepository.deleteFavicon();
                this.configuracion = response.data;
                return response;
            } catch (error) {
                console.error('Error eliminando favicon:', error);
                throw error;
            }
        },

        async eliminarIconoMensaje() {
            try {
                const response = await ConfiguracionEmpresaRepository.deleteIconoMensaje();
                this.configuracion = response.data;
                return response;
            } catch (error) {
                console.error('Error eliminando icono del mensaje:', error);
                throw error;
            }
        },

        resetearConfiguracion() {
            this.configuracion = {};
            this.logoUrl = defaultLogo;
            this.isLoaded = false;
        }
    }
});
