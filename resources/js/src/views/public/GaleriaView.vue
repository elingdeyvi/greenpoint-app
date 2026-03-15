<template>
  <div class="py-5">
    <div class="container">
      <div class="text-center mb-5">
        <span class="text-uppercase small gp-text-primary fw-bold">Galería</span>
        <h1 class="h2">Galería de imágenes</h1>
      </div>
      <div v-if="loading" class="text-center py-5"><div class="spinner-border gp-text-primary" role="status"></div></div>
      <div v-else class="row g-3">
        <div v-for="(item, idx) in items" :key="item.id" class="col-6 col-md-4 col-lg-3">
          <a href="#" class="d-block rounded-3 overflow-hidden shadow-sm" @click.prevent="openLightbox(idx)">
            <img :src="storageUrl(item.imagen)" class="img-fluid w-100 object-fit-cover" style="height: 220px;" :alt="item.titulo || 'Imagen'">
          </a>
          <p v-if="item.titulo" class="small text-muted mt-1 mb-0 text-center">{{ item.titulo }}</p>
        </div>
      </div>
      <div v-if="!loading && !items.length" class="text-center py-5 text-muted">No hay imágenes en la galería.</div>

      <!-- Lightbox con modal Bootstrap -->
      <div class="modal fade" id="gpGaleriaModal" tabindex="-1" aria-hidden="true" @hidden="lightboxIndex = null">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content border-0 bg-transparent">
            <div class="modal-body p-0 position-relative">
              <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              <img v-if="currentImage" :src="storageUrl(currentImage.imagen)" class="img-fluid w-100 rounded" :alt="currentImage.titulo">
              <div class="d-flex justify-content-between align-items-center mt-2">
                <button type="button" class="btn btn-outline-light btn-sm" :disabled="!canPrev" @click="prevImage">← Anterior</button>
                <span class="text-white small">{{ (lightboxIndex ?? 0) + 1 }} / {{ items.length }}</span>
                <button type="button" class="btn btn-outline-light btn-sm" :disabled="!canNext" @click="nextImage">Siguiente →</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { usePublicGaleria } from '@/composables/usePublicGaleria';
import { usePublicMeta } from '@/composables/use-public-meta';

usePublicMeta({
  title: 'Galería',
  description: 'Galería de imágenes de GreenPoint - Proyectos y comunicaciones para el sector petrolero.',
  keywords: 'galería, GreenPoint, proyectos, comunicaciones',
});

const baseStorage = () => (import.meta.env.VITE_API_URL || '').replace(/\/api\/?$/, '') || window.location.origin;
const storageUrl = (path) => (path && !path.startsWith('http') ? `${baseStorage()}/storage/${path}` : path) || '';

const { items, loading, fetchGaleria } = usePublicGaleria();
const lightboxIndex = ref(null);
let modalInstance = null;

const currentImage = computed(() => (lightboxIndex.value != null && items.value[lightboxIndex.value]) ? items.value[lightboxIndex.value] : null);
const canPrev = computed(() => lightboxIndex.value != null && lightboxIndex.value > 0);
const canNext = computed(() => lightboxIndex.value != null && lightboxIndex.value < items.value.length - 1);

const openLightbox = (idx) => {
  lightboxIndex.value = idx;
  const el = document.getElementById('gpGaleriaModal');
  if (el && typeof bootstrap !== 'undefined') {
    modalInstance = bootstrap.Modal.getOrCreateInstance(el);
    modalInstance.show();
  }
};

const prevImage = () => { if (canPrev.value) lightboxIndex.value--; };
const nextImage = () => { if (canNext.value) lightboxIndex.value++; };

onMounted(() => fetchGaleria());
</script>
