<template>
  <div class="py-5">
    <div class="container">
      <div v-if="loading" class="text-center py-5"><div class="spinner-border gp-text-primary" role="status"></div></div>
      <div v-else-if="error" class="alert alert-danger">Error al cargar el contenido.</div>
      <template v-else-if="pagina">
        <div class="text-center mb-5">
          <span class="text-uppercase small gp-text-primary fw-bold">Historia</span>
          <h1 class="h2">{{ pagina.titulo }}</h1>
        </div>
        <div v-if="pagina.imagenes && pagina.imagenes.length" class="row g-3 mb-5">
          <div v-for="img in pagina.imagenes" :key="img.id" class="col-md-4">
            <img :src="storageUrl(img.ruta_imagen)" class="img-fluid rounded shadow" :alt="pagina.titulo">
          </div>
        </div>
        <div v-if="pagina.eventos && pagina.eventos.length" class="timeline-style">
          <div v-for="(ev, i) in pagina.eventos" :key="ev.id" class="d-flex mb-4">
            <div class="flex-shrink-0 me-3 text-center" style="width: 80px;">
              <span class="d-block rounded-3 gp-bg-primary text-white fw-bold py-2 px-2">{{ ev.anio }}</span>
            </div>
            <div class="flex-grow-1 border-start border-3 border-primary ps-4 pb-4">
              <h5 class="mb-2">{{ ev.titulo }}</h5>
              <p class="text-muted mb-0" v-html="ev.descripcion"></p>
            </div>
          </div>
        </div>
      </template>
      <div v-else class="text-center py-5 text-muted">No hay contenido disponible.</div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { usePublicHistoria } from '@/composables/usePublicHistoria';

const baseStorage = () => (import.meta.env.VITE_API_URL || '').replace(/\/api\/?$/, '') || window.location.origin;
const storageUrl = (path) => (path && !path.startsWith('http') ? `${baseStorage()}/storage/${path}` : path) || '';

const { pagina, loading, error, fetchPagina } = usePublicHistoria();
onMounted(() => fetchPagina());
</script>
