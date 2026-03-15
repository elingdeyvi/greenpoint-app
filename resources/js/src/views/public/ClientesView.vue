<template>
  <div class="py-5">
    <div class="container">
      <div class="text-center mb-5">
        <span class="text-uppercase small gp-text-primary fw-bold">Clientes</span>
        <h1 class="h2">Nuestros clientes</h1>
      </div>
      <div v-if="loading" class="text-center py-5"><div class="spinner-border gp-text-primary" role="status"></div></div>
      <div v-else class="row g-4 align-items-center justify-content-center">
        <div v-for="c in clientes" :key="c.id" class="col-6 col-md-4 col-lg-3">
          <div class="card border-0 shadow-sm h-100 rounded-3 p-3 text-center d-flex align-items-center justify-content-center" style="min-height: 140px;">
            <img v-if="c.logo" :src="storageUrl(c.logo)" class="img-fluid object-fit-contain" style="max-height: 100px;" :alt="c.nombre || 'Cliente'">
            <span v-else class="text-muted fw-semibold">{{ c.nombre }}</span>
          </div>
        </div>
      </div>
      <div v-if="!loading && !clientes.length" class="text-center py-5 text-muted">No hay clientes para mostrar.</div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { usePublicClientes } from '@/composables/usePublicClientes';

const baseStorage = () => (import.meta.env.VITE_API_URL || '').replace(/\/api\/?$/, '') || window.location.origin;
const storageUrl = (path) => (path && !path.startsWith('http') ? `${baseStorage()}/storage/${path}` : path) || '';

const { clientes, loading, fetchClientes } = usePublicClientes();
onMounted(() => fetchClientes());
</script>
