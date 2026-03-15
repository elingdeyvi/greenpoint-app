<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb"><ul class="navbar-nav flex-row"><li><div class="page-header"><nav class="breadcrumb-one"><ol class="breadcrumb"><li class="breadcrumb-item">Catálogos</li><li class="breadcrumb-item active">Redes sociales</li></ol></nav></div></li></ul></teleport>
    <div class="row layout-top-spacing"><div class="col-xl-12"><div class="panel p-3">
      <div class="d-flex justify-content-between align-items-center mb-4"><h4 class="mb-0">Redes sociales</h4><div><button class="btn btn-primary me-2" @click="refrescar" :disabled="loading">Refrescar</button><button class="btn btn-success" @click="openCreate">Nuevo</button></div></div>
      <div v-if="loading && !listData.length" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
      <div v-else>
        <div class="table-responsive"><table class="table table-bordered"><thead><tr><th>#</th><th>Nombre</th><th>URL</th><th>Icono</th><th>Orden</th><th width="120">Acciones</th></tr></thead><tbody>
          <tr v-for="row in listData" :key="row.id"><td>{{ row.id }}</td><td>{{ row.nombre }}</td><td><a :href="row.url" target="_blank" rel="noopener">{{ (row.url || '').slice(0, 50) }}…</a></td><td><code>{{ row.icono || '—' }}</code></td><td>{{ row.orden }}</td><td><button class="btn btn-sm btn-outline-primary me-1" @click="openEdit(row.id)">Editar</button><button class="btn btn-sm btn-outline-danger" @click="confirmDelete(row)">Eliminar</button></td></tr>
          <tr v-if="!listData.length"><td colspan="6" class="text-center text-muted py-4">No hay registros.</td></tr>
        </tbody></table></div>
        <nav v-if="pagination.last_page > 1" class="mt-3"><ul class="pagination pagination-sm mb-0"><li class="page-item" :class="{ disabled: pagination.current_page <= 1 }"><a class="page-link" href="javascript:;" @click.prevent="goPage(pagination.current_page - 1)">Anterior</a></li><li class="page-item disabled"><span class="page-link">{{ pagination.current_page }} / {{ pagination.last_page }}</span></li><li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }"><a class="page-link" href="javascript:;" @click.prevent="goPage(pagination.current_page + 1)">Siguiente</a></li></ul></nav>
      </div>
    </div></div></div>
    <div class="modal fade" id="modalRedSocial" tabindex="-1" ref="modalRef"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">{{ isEdit ? 'Editar' : 'Nuevo' }} red social</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
      <form @submit.prevent="submitForm"><div class="mb-3"><label class="form-label">Nombre *</label><input v-model="form.nombre" type="text" class="form-control" required placeholder="WhatsApp, Facebook..." /></div><div class="mb-3"><label class="form-label">URL *</label><input v-model="form.url" type="url" class="form-control" required placeholder="https://" /></div><div class="mb-3"><label class="form-label">Icono (clase CSS)</label><input v-model="form.icono" type="text" class="form-control" placeholder="fab fa-whatsapp" /></div><div class="mb-3"><label class="form-label">Orden</label><input v-model.number="form.orden" type="number" class="form-control" min="0" /></div><div class="d-flex justify-content-end gap-2"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Guardando...' : 'Guardar' }}</button></div></form>
    </div></div></div></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Modal } from 'bootstrap';
import { useRedSocial } from '@/composables/useRedSocial';
import { showSuccess, showError, showConfirm } from '@/utils/notifications';

const { items, currentItem, loading, fetchList, fetchById, create, update, deleteItem } = useRedSocial();
const modalRef = ref(null);
let modalInstance = null;
const isEdit = ref(false);
const saving = ref(false);
const form = ref({ nombre: '', url: '', icono: '', orden: 0 });

const listData = computed(() => (Array.isArray(items.value) ? items.value : items.value?.data ?? []));
const pagination = computed(() => ({ current_page: items.value?.current_page ?? 1, last_page: items.value?.last_page ?? 1, per_page: items.value?.per_page ?? 15 }));

function openCreate() { isEdit.value = false; form.value = { nombre: '', url: '', icono: '', orden: 0 }; modalInstance?.show(); }

async function openEdit(id) { isEdit.value = true; await fetchById(id); const c = currentItem.value; if (!c) return; form.value = { id: c.id, nombre: c.nombre ?? '', url: c.url ?? '', icono: c.icono ?? '', orden: c.orden ?? 0 }; modalInstance?.show(); }

async function submitForm() { saving.value = true; const payload = { nombre: form.value.nombre, url: form.value.url, icono: form.value.icono || null, orden: form.value.orden }; try { if (isEdit.value) { await update(form.value.id, payload); showSuccess('Red social actualizada.'); } else { await create(payload); showSuccess('Red social creada.'); } modalInstance?.hide(); refrescar(); } catch (err) { showError(err?.response?.data?.message || 'Error'); } finally { saving.value = false; } }

async function confirmDelete(row) { if (!(await showConfirm(`¿Eliminar "${row.nombre}"?`, 'Confirmar'))) return; try { await deleteItem(row.id); showSuccess('Eliminado.'); refrescar(); } catch (err) { showError(err?.response?.data?.message); } }
function refrescar() { fetchList({ page: pagination.value.current_page, per_page: pagination.value.per_page }); }
function goPage(p) { if (p >= 1 && p <= pagination.value.last_page) fetchList({ page: p, per_page: pagination.value.per_page }); }

onMounted(() => { fetchList({ per_page: 15 }); if (modalRef.value) modalInstance = new Modal(modalRef.value); });
</script>
