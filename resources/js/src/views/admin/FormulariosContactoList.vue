<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb"><ul class="navbar-nav flex-row"><li><div class="page-header"><nav class="breadcrumb-one"><ol class="breadcrumb"><li class="breadcrumb-item active">Formularios de contacto</li></ol></nav></div></li></ul></teleport>
    <div class="row layout-top-spacing"><div class="col-xl-12"><div class="panel p-3">
      <div class="d-flex justify-content-between align-items-center mb-4"><h4 class="mb-0">Mensajes recibidos</h4><button class="btn btn-primary" @click="refrescar" :disabled="loading">Refrescar</button></div>
      <div v-if="loading && !listData.length" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
      <div v-else>
        <div class="table-responsive"><table class="table table-bordered"><thead><tr><th>#</th><th>Nombre</th><th>Email</th><th>Fecha</th><th>Leído</th><th width="140">Acciones</th></tr></thead><tbody>
          <tr v-for="row in listData" :key="row.id" :class="{ 'table-light': !row.leido }">
            <td>{{ row.id }}</td><td>{{ row.nombre }}</td><td>{{ row.email }}</td><td>{{ formatDate(row.created_at) }}</td>
            <td><span :class="row.leido ? 'badge bg-secondary' : 'badge bg-warning'">{{ row.leido ? 'Sí' : 'No' }}</span></td>
            <td><button class="btn btn-sm btn-outline-primary me-1" @click="openDetail(row.id)">Ver</button><button v-if="!row.leido" class="btn btn-sm btn-outline-success" @click="marcarLeido(row)">Marcar leído</button></td>
          </tr>
          <tr v-if="!listData.length"><td colspan="6" class="text-center text-muted py-4">No hay mensajes.</td></tr>
        </tbody></table></div>
        <nav v-if="pagination.last_page > 1" class="mt-3"><ul class="pagination pagination-sm mb-0"><li class="page-item" :class="{ disabled: pagination.current_page <= 1 }"><a class="page-link" href="javascript:;" @click.prevent="goPage(pagination.current_page - 1)">Anterior</a></li><li class="page-item disabled"><span class="page-link">{{ pagination.current_page }} / {{ pagination.last_page }}</span></li><li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }"><a class="page-link" href="javascript:;" @click.prevent="goPage(pagination.current_page + 1)">Siguiente</a></li></ul></nav>
      </div>
    </div></div></div>

    <div class="modal fade" id="modalDetail" tabindex="-1" ref="modalRef"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Mensaje de {{ currentItem?.nombre }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body" v-if="currentItem">
      <p><strong>Email:</strong> {{ currentItem.email }}</p><p v-if="currentItem.telefono"><strong>Teléfono:</strong> {{ currentItem.telefono }}</p><p><strong>Fecha:</strong> {{ formatDate(currentItem.created_at) }}</p><hr /><div class="mb-3"><strong>Mensaje:</strong><pre class="bg-light p-3 mt-2 rounded">{{ currentItem.mensaje }}</pre></div><div v-if="!currentItem.leido"><button class="btn btn-success" @click="marcarLeido(currentItem); modalInstance?.hide();">Marcar como leído</button></div>
    </div></div></div></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Modal } from 'bootstrap';
import { useFormularioContacto } from '@/composables/useFormularioContacto';
import { showSuccess, showError } from '@/utils/notifications';

const { items, currentItem, loading, fetchList, fetchById, markAsRead } = useFormularioContacto();
const modalRef = ref(null);
let modalInstance = null;

const listData = computed(() => (Array.isArray(items.value) ? items.value : items.value?.data ?? []));
const pagination = computed(() => ({ current_page: items.value?.current_page ?? 1, last_page: items.value?.last_page ?? 1, per_page: items.value?.per_page ?? 15 }));

function formatDate(val) { if (!val) return '—'; return new Date(val).toLocaleString('es-MX', { dateStyle: 'short', timeStyle: 'short' }); }

function openDetail(id) { fetchById(id); modalInstance?.show(); }

async function marcarLeido(row) {
  const id = row?.id ?? currentItem.value?.id;
  if (!id) return;
  try { await markAsRead(id, true); showSuccess('Marcado como leído.'); refrescar(); } catch (err) { showError(err?.response?.data?.message || 'Error'); }
}

function refrescar() { fetchList({ page: pagination.value.current_page, per_page: pagination.value.per_page }); }
function goPage(p) { if (p >= 1 && p <= pagination.value.last_page) fetchList({ page: p, per_page: pagination.value.per_page }); }

onMounted(() => { fetchList({ per_page: 15 }); if (modalRef.value) modalInstance = new Modal(modalRef.value); });
</script>
