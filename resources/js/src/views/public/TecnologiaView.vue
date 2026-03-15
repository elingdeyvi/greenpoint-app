<template>
  <div class="py-5">
    <div class="container">
      <div v-if="loading" class="text-center py-5"><div class="spinner-border gp-text-primary" role="status"></div></div>
      <div v-else-if="error" class="alert alert-danger">Error al cargar el contenido.</div>
      <template v-else-if="pagina">
        <div class="row mb-5">
          <div class="col-lg-4 mb-4 mb-lg-0">
            <img v-if="pagina.imagen_destacada" :src="storageUrl(pagina.imagen_destacada)" class="img-fluid rounded-3 shadow" alt="">
          </div>
          <div class="col-lg-8">
            <span class="text-uppercase small gp-text-primary fw-bold">Tecnología</span>
            <h1 class="h2 mb-3">{{ pagina.titulo }}</h1>
            <div v-if="pagina.contenido" class="mb-4" v-html="pagina.contenido"></div>
          </div>
        </div>
        <div v-if="pagina.secciones && pagina.secciones.length">
          <div v-for="sec in pagina.secciones" :key="sec.id" class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
            <div class="card-body p-4">
              <h3 class="h5 gp-text-primary mb-3">{{ sec.titulo }}</h3>
              <div v-html="sec.contenido"></div>
            </div>
          </div>
        </div>
      </template>
      <div v-else class="text-center py-5 text-muted">No hay contenido disponible.</div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { usePublicTecnologia } from '@/composables/usePublicTecnologia';
import { usePublicMeta } from '@/composables/use-public-meta';

const { pagina, loading, error, fetchPagina } = usePublicTecnologia();

usePublicMeta(computed(() =>
  pagina.value
    ? { title: pagina.value.titulo, meta_descripcion: pagina.value.meta_descripcion, meta_keywords: pagina.value.meta_keywords }
    : { title: 'Tecnología' }
));

const baseStorage = () => (import.meta.env.VITE_API_URL || '').replace(/\/api\/?$/, '') || window.location.origin;
const storageUrl = (path) => (path && !path.startsWith('http') ? `${baseStorage()}/storage/${path}` : path) || '';

onMounted(() => fetchPagina());
</script>
