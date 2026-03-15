<template>
  <div class="py-5">
    <div class="container">
      <div v-if="loading" class="text-center py-5"><div class="spinner-border gp-text-primary" role="status"></div></div>
      <div v-else-if="error" class="alert alert-danger">Error al cargar el contenido.</div>
      <template v-else-if="pagina">
        <div class="text-center mb-5">
          <span class="text-uppercase small gp-text-primary fw-bold">Aviso de Privacidad</span>
          <h1 class="h2">{{ pagina.titulo }}</h1>
        </div>
        <div v-if="pagina.secciones && pagina.secciones.length">
          <div v-for="sec in pagina.secciones" :key="sec.id" class="mb-5">
            <h2 class="h5 gp-text-primary mb-3">{{ sec.titulo }}</h2>
            <div v-if="sec.contenido" v-html="sec.contenido" class="mb-3"></div>
            <ul v-if="sec.listas && sec.listas.length" class="list-unstyled ms-3">
              <li v-for="item in sec.listas" :key="item.id" class="mb-1">• {{ item.texto }}</li>
            </ul>
          </div>
        </div>
      </template>
      <div v-else class="text-center py-5 text-muted">No hay contenido disponible.</div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { usePublicAviso } from '@/composables/usePublicAviso';
import { usePublicMeta } from '@/composables/use-public-meta';

const { pagina, loading, error, fetchPagina } = usePublicAviso();

usePublicMeta(computed(() =>
  pagina.value
    ? { title: pagina.value.titulo, meta_descripcion: pagina.value.meta_descripcion, meta_keywords: pagina.value.meta_keywords }
    : { title: 'Aviso de Privacidad' }
));

onMounted(() => fetchPagina());
</script>
