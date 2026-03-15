<template>
  <div class="py-5">
    <div class="container">
      <div v-if="loading" class="text-center py-5"><div class="spinner-border gp-text-primary" role="status"></div></div>
      <div v-else-if="error" class="alert alert-danger">Error al cargar el contenido.</div>
      <template v-else-if="pagina">
        <div class="row align-items-center mb-5">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <img v-if="pagina.imagen_destacada" :src="storageUrl(pagina.imagen_destacada)" class="img-fluid rounded-3 shadow" alt="">
            <div v-if="pagina.imagenes && pagina.imagenes.length" class="mt-3 row g-2">
              <div v-for="img in pagina.imagenes" :key="img.id" class="col-4">
                <img :src="storageUrl(img.ruta_imagen)" class="img-fluid rounded" :alt="pagina.titulo">
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <span class="text-uppercase small gp-text-primary fw-bold">Nosotros</span>
            <h1 class="h2 mb-3">{{ pagina.titulo }}</h1>
            <p v-if="pagina.subtitulo" class="lead text-muted">{{ pagina.subtitulo }}</p>
            <div v-if="pagina.texto_descriptivo" class="mb-4" v-html="pagina.texto_descriptivo"></div>
            <div v-if="pagina.texto_adicional" v-html="pagina.texto_adicional"></div>
            <div v-if="pagina.progreso && pagina.progreso.length" class="mt-4">
              <div v-for="p in pagina.progreso" :key="p.id" class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <span class="fw-semibold">{{ p.titulo }}</span>
                  <span class="text-muted">{{ p.porcentaje }}%</span>
                </div>
                <div class="progress" style="height: 8px;">
                  <div class="progress-bar gp-bg-primary" role="progressbar" :style="{ width: p.porcentaje + '%' }"></div>
                </div>
                <p v-if="p.descripcion" class="small text-muted mt-1 mb-0">{{ p.descripcion }}</p>
              </div>
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
import { usePublicNosotros } from '@/composables/usePublicNosotros';
import { usePublicMeta } from '@/composables/use-public-meta';

const { pagina, loading, error, fetchPagina } = usePublicNosotros();

usePublicMeta(computed(() =>
  pagina.value
    ? { title: pagina.value.titulo, meta_descripcion: pagina.value.meta_descripcion, meta_keywords: pagina.value.meta_keywords }
    : { title: 'Nosotros' }
));

const baseStorage = () => (import.meta.env.VITE_API_URL || '').replace(/\/api\/?$/, '') || window.location.origin;
const storageUrl = (path) => (path && !path.startsWith('http') ? `${baseStorage()}/storage/${path}` : path) || '';

onMounted(() => fetchPagina());
</script>
