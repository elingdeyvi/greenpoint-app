<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb">
      <ul class="navbar-nav flex-row"><li><div class="page-header"><nav class="breadcrumb-one"><ol class="breadcrumb"><li class="breadcrumb-item active">Dashboard</li></ol></nav></div></li></ul>
    </teleport>
    <div class="row layout-top-spacing">
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded p-3 me-3"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
            <div><h6 class="text-muted mb-1">Servicios activos</h6><p class="mb-0 h4">{{ stats.servicios }}</p></div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="flex-shrink-0 bg-success bg-opacity-10 rounded p-3 me-3"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <div><h6 class="text-muted mb-1">Clientes</h6><p class="mb-0 h4">{{ stats.clientes }}</p></div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded p-3 me-3"><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
            <div><h6 class="text-muted mb-1">Mensajes no leídos</h6><p class="mb-0 h4">{{ stats.mensajesNoLeidos }}</p></div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <router-link to="/formularios-contacto" class="btn btn-outline-primary btn-sm">Ver formularios</router-link>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xl-12">
        <div class="card border-0 shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">Últimos mensajes de contacto</h5><router-link to="/formularios-contacto" class="btn btn-sm btn-primary">Ver todos</router-link></div>
          <div class="card-body p-0">
            <div v-if="loadingMessages" class="text-center py-4"><div class="spinner-border spinner-border-sm"></div></div>
            <table v-else class="table table-hover mb-0"><thead><tr><th>Nombre</th><th>Email</th><th>Fecha</th><th>Leído</th><th></th></tr></thead><tbody>
              <tr v-for="m in ultimosMensajes" :key="m.id"><td>{{ m.nombre }}</td><td>{{ m.email }}</td><td>{{ formatDate(m.created_at) }}</td><td><span :class="m.leido ? 'badge bg-secondary' : 'badge bg-warning'">{{ m.leido ? 'Sí' : 'No' }}</span></td><td><router-link :to="'/formularios-contacto'" class="btn btn-sm btn-outline-primary">Ver</router-link></td></tr>
              <tr v-if="!ultimosMensajes.length"><td colspan="5" class="text-center text-muted py-4">No hay mensajes.</td></tr>
            </tbody></table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import ServicioRepository from '@/repositories/ServicioRepository';
import ClienteCatalogoRepository from '@/repositories/ClienteCatalogoRepository';
import FormularioContactoRepository from '@/repositories/FormularioContactoRepository';

const stats = reactive({ servicios: 0, clientes: 0, mensajesNoLeidos: 0 });
const ultimosMensajes = ref([]);
const loadingMessages = ref(false);

function formatDate(val) { if (!val) return '—'; return new Date(val).toLocaleString('es-MX', { dateStyle: 'short', timeStyle: 'short' }); }

async function loadStats() {
  try {
    const [servRes, cliRes] = await Promise.all([ServicioRepository.getAll({ per_page: 1 }), ClienteCatalogoRepository.getAll({ per_page: 1 })]);
    stats.servicios = servRes?.total ?? (Array.isArray(servRes?.data) ? servRes.data.length : 0);
    stats.clientes = cliRes?.total ?? (Array.isArray(cliRes?.data) ? cliRes.data.length : 0);
  } catch (_) {
    stats.servicios = 0;
    stats.clientes = 0;
  }
}

async function loadMessages() {
  loadingMessages.value = true;
  try {
    const res = await FormularioContactoRepository.getAll({ per_page: 10 });
    const data = res?.data ?? (Array.isArray(res) ? res : []);
    ultimosMensajes.value = Array.isArray(data) ? data.slice(0, 5) : [];
    stats.mensajesNoLeidos = Array.isArray(data) ? data.filter((m) => !m.leido).length : 0;
  } catch (_) { ultimosMensajes.value = []; } finally { loadingMessages.value = false; }
}

onMounted(() => { loadStats(); loadMessages(); });
</script>
