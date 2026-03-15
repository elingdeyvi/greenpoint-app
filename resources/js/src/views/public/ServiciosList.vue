<template>
  <div class="py-5">
    <div class="container">
      <div class="text-center mb-5">
        <span class="text-uppercase small gp-text-primary fw-bold">Servicios</span>
        <h1 class="h2">Nuestros servicios</h1>
      </div>
      <div v-if="loading" class="text-center py-5"><div class="spinner-border gp-text-primary" role="status"></div></div>
      <div v-else class="row g-4">
        <div v-for="s in servicios" :key="s.id" class="col-md-6 col-lg-4">
          <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden">
            <img v-if="s.imagen" :src="storageUrl(s.imagen)" class="card-img-top object-fit-cover" style="height: 220px;" :alt="s.nombre">
            <div class="card-body">
              <h5 class="card-title">
                <router-link :to="servicioLink(s)" class="text-dark text-decoration-none">{{ s.nombre }}</router-link>
              </h5>
              <p class="card-text text-muted">{{ (s.descripcion || '').slice(0, 160) }}{{ (s.descripcion || '').length > 160 ? '…' : '' }}</p>
              <router-link :to="servicioLink(s)" class="gp-link fw-semibold">Ver detalle →</router-link>
            </div>
          </div>
        </div>
      </div>
      <div v-if="!loading && !servicios.length" class="text-center py-5 text-muted">No hay servicios disponibles.</div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { usePublicServicios } from '@/composables/usePublicServicios';

const baseStorage = () => (import.meta.env.VITE_API_URL || '').replace(/\/api\/?$/, '') || window.location.origin;
const storageUrl = (path) => (path && !path.startsWith('http') ? `${baseStorage()}/storage/${path}` : path) || '';

const { servicios, loading, fetchServicios } = usePublicServicios();
const servicioLink = (s) => (s.slug ? { name: 'PublicServicioDetalle', params: { idOrSlug: s.slug } } : { name: 'PublicServicioDetalle', params: { idOrSlug: String(s.id) } });

onMounted(() => fetchServicios());
</script>
