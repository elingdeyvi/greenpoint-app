<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb"><ul class="navbar-nav flex-row"><li><div class="page-header"><nav class="breadcrumb-one"><ol class="breadcrumb"><li class="breadcrumb-item">Catálogos</li><li class="breadcrumb-item active">Contactos</li></ol></nav></div></li></ul></teleport>
    <div class="row layout-top-spacing"><div class="col-xl-12"><div class="panel p-3">
      <div class="d-flex justify-content-between align-items-center mb-4"><h4 class="mb-0">Contactos</h4><div><button class="btn btn-primary me-2" @click="refrescar" :disabled="loading">Refrescar</button><button class="btn btn-success" @click="openCreate">Nuevo</button></div></div>
      <div v-if="loading && !listData.length" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
      <div v-else>
        <div class="table-responsive"><table class="table table-bordered"><thead><tr><th>#</th><th>Ubicación</th><th>Dirección</th><th>Teléfono</th><th>Email</th><th>Orden</th><th width="120">Acciones</th></tr></thead><tbody>
          <tr v-for="row in listData" :key="row.id"><td>{{ row.id }}</td><td>{{ row.ubicacion }}</td><td>{{ (row.direccion || '').slice(0, 40) }}{{ (row.direccion || '').length > 40 ? '…' : '' }}</td><td>{{ row.telefono }}</td><td>{{ row.email }}</td><td>{{ row.orden }}</td><td><button class="btn btn-sm btn-outline-primary me-1" @click="openEdit(row.id)">Editar</button><button class="btn btn-sm btn-outline-danger" @click="confirmDelete(row)">Eliminar</button></td></tr>
          <tr v-if="!listData.length"><td colspan="7" class="text-center text-muted py-4">No hay registros.</td></tr>
        </tbody></table></div>
        <nav v-if="pagination.last_page > 1" class="mt-3"><ul class="pagination pagination-sm mb-0"><li class="page-item" :class="{ disabled: pagination.current_page <= 1 }"><a class="page-link" href="javascript:;" @click.prevent="goPage(pagination.current_page - 1)">Anterior</a></li><li class="page-item disabled"><span class="page-link">{{ pagination.current_page }} / {{ pagination.last_page }}</span></li><li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }"><a class="page-link" href="javascript:;" @click.prevent="goPage(pagination.current_page + 1)">Siguiente</a></li></ul></nav>
      </div>
    </div></div></div>
    <div class="modal fade" id="modalContacto" tabindex="-1" ref="modalRef"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">{{ isEdit ? 'Editar' : 'Nuevo' }} contacto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
      <form @submit.prevent="submitForm"><div class="row"><div class="col-md-6 mb-3"><label class="form-label">Ubicación *</label><input v-model="form.ubicacion" type="text" class="form-control" required /></div><div class="col-md-6 mb-3"><label class="form-label">Orden</label><input v-model.number="form.orden" type="number" class="form-control" min="0" /></div></div><div class="mb-3"><label class="form-label">Dirección</label><input v-model="form.direccion" type="text" class="form-control" /></div><div class="row"><div class="col-md-6 mb-3"><label class="form-label">Teléfono *</label><input v-model="form.telefono" type="text" class="form-control" required /></div><div class="col-md-6 mb-3"><label class="form-label">Email</label><input v-model="form.email" type="email" class="form-control" /></div></div><div class="mb-3"><label class="form-label">URL del mapa (embed)</label><textarea v-model="form.mapa_url" class="form-control" rows="2" placeholder="https://www.google.com/maps/embed?pb=..."></textarea></div><div class="d-flex justify-content-end gap-2"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Guardando...' : 'Guardar' }}</button></div></form>
    </div></div></div></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Modal } from 'bootstrap';
import { useContacto } from '@/composables/useContacto';
import { showSuccess, showError, showConfirm } from '@/utils/notifications';

const { items, currentItem, loading, fetchList, fetchById, create, update, deleteItem } = useContacto();
const modalRef = ref(null);
let modalInstance = null;
const isEdit = ref(false);
const saving = ref(false);
const form = ref({ ubicacion: '', direccion: '', telefono: '', email: '', mapa_url: '', orden: 0 });

const listData = computed(() => (Array.isArray(items.value) ? items.value : items.value?.data ?? []));
const pagination = computed(() => ({ current_page: items.value?.current_page ?? 1, last_page: items.value?.last_page ?? 1, per_page: items.value?.per_page ?? 15 }));

function openCreate() { isEdit.value = false; form.value = { ubicacion: '', direccion: '', telefono: '', email: '', mapa_url: '', orden: 0 }; modalInstance?.show(); }

async function openEdit(id) { isEdit.value = true; await fetchById(id); const c = currentItem.value; if (!c) return; form.value = { id: c.id, ubicacion: c.ubicacion ?? '', direccion: c.direccion ?? '', telefono: c.telefono ?? '', email: c.email ?? '', mapa_url: c.mapa_url ?? '', orden: c.orden ?? 0 }; modalInstance?.show(); }

async function submitForm() { saving.value = true; const payload = { ubicacion: form.value.ubicacion, direccion: form.value.direccion || null, telefono: form.value.telefono, email: form.value.email || null, mapa_url: form.value.mapa_url || null, orden: form.value.orden }; try { if (isEdit.value) { await update(form.value.id, payload); showSuccess('Contacto actualizado.'); } else { await create(payload); showSuccess('Contacto creado.'); } modalInstance?.hide(); refrescar(); } catch (err) { showError(err?.response?.data?.message || 'Error'); } finally { saving.value = false; } }

async function confirmDelete(row) { if (!(await showConfirm(`¿Eliminar contacto "${row.ubicacion}"?`, 'Confirmar'))) return; try { await deleteItem(row.id); showSuccess('Eliminado.'); refrescar(); } catch (err) { showError(err?.response?.data?.message); } }
function refrescar() { fetchList({ page: pagination.value.current_page, per_page: pagination.value.per_page }); }
function goPage(p) { if (p >= 1 && p <= pagination.value.last_page) fetchList({ page: p, per_page: pagination.value.per_page }); }

onMounted(() => { fetchList({ per_page: 15 }); if (modalRef.value) modalInstance = new Modal(modalRef.value); });
</script>
