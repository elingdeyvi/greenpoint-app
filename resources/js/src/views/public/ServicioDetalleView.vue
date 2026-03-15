<template>
  <div class="py-5">
    <div class="container">
      <div v-if="loading" class="text-center py-5"><div class="spinner-border gp-text-primary" role="status"></div></div>
      <div v-else-if="error || !servicio" class="text-center py-5">
        <p class="text-muted">Servicio no encontrado.</p>
        <router-link to="/servicios" class="btn gp-btn-primary">Volver a servicios</router-link>
      </div>
      <template v-else>
        <nav aria-label="breadcrumb" class="mb-4">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><router-link to="/" class="gp-link">Inicio</router-link></li>
            <li class="breadcrumb-item"><router-link to="/servicios" class="gp-link">Servicios</router-link></li>
            <li class="breadcrumb-item active" aria-current="page">{{ servicio.nombre }}</li>
          </ol>
        </nav>
        <div class="row">
          <div class="col-lg-5 mb-4 mb-lg-0">
            <img v-if="servicio.imagen" :src="storageUrl(servicio.imagen)" class="img-fluid rounded-3 shadow" :alt="servicio.nombre">
          </div>
          <div class="col-lg-7">
            <h1 class="h2 mb-3">{{ servicio.nombre }}</h1>
            <div v-if="servicio.descripcion" class="text-muted" v-html="servicio.descripcion"></div>
            <router-link to="/contacto/tabasco" class="btn gp-btn-primary mt-4">Solicitar información</router-link>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { usePublicServicios } from '@/composables/usePublicServicios';

const baseStorage = () => (import.meta.env.VITE_API_URL || '').replace(/\/api\/?$/, '') || window.location.origin;
const storageUrl = (path) => (path && !path.startsWith('http') ? `${baseStorage()}/storage/${path}` : path) || '';

const route = useRoute();
const { servicio, loading, error, fetchServicio, fetchServicioBySlug } = usePublicServicios();

const loadServicio = () => {
  const idOrSlug = route.params.idOrSlug;
  if (!idOrSlug) return;
  const numeric = /^\d+$/.test(idOrSlug);
  if (numeric) fetchServicio(Number(idOrSlug));
  else fetchServicioBySlug(idOrSlug);
};

onMounted(loadServicio);
watch(() => route.params.idOrSlug, loadServicio);
</script>
