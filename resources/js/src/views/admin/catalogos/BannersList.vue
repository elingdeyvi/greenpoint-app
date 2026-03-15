<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb"><ul class="navbar-nav flex-row"><li><div class="page-header"><nav class="breadcrumb-one"><ol class="breadcrumb"><li class="breadcrumb-item">Catálogos</li><li class="breadcrumb-item active">Banners</li></ol></nav></div></li></ul></teleport>
    <div class="row layout-top-spacing"><div class="col-xl-12"><div class="panel p-3">
      <div class="d-flex justify-content-between align-items-center mb-4"><h4 class="mb-0">Banners</h4><div><button class="btn btn-primary me-2" @click="refrescar" :disabled="loading">Refrescar</button><button class="btn btn-success" @click="openCreate">Nuevo</button></div></div>
      <div v-if="loading && !listData.length" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
      <div v-else>
        <div class="table-responsive"><table class="table table-bordered"><thead><tr><th>#</th><th>Título</th><th>Imagen</th><th>Enlace</th><th>Orden</th><th>Activo</th><th width="120">Acciones</th></tr></thead><tbody>
          <tr v-for="row in listData" :key="row.id"><td>{{ row.id }}</td><td>{{ row.titulo }}</td><td><img v-if="row.imagen" :src="storageUrl(row.imagen)" style="max-height:40px" alt="" /></td><td>{{ row.enlace || '—' }}</td><td>{{ row.orden }}</td><td><span :class="row.activo ? 'badge bg-success' : 'badge bg-secondary'">{{ row.activo ? 'Sí' : 'No' }}</span></td><td><button class="btn btn-sm btn-outline-primary me-1" @click="openEdit(row.id)">Editar</button><button class="btn btn-sm btn-outline-danger" @click="confirmDelete(row)">Eliminar</button></td></tr>
          <tr v-if="!listData.length"><td colspan="7" class="text-center text-muted py-4">No hay registros.</td></tr>
        </tbody></table></div>
        <nav v-if="pagination.last_page > 1" class="mt-3"><ul class="pagination pagination-sm mb-0"><li class="page-item" :class="{ disabled: pagination.current_page <= 1 }"><a class="page-link" href="javascript:;" @click.prevent="goPage(pagination.current_page - 1)">Anterior</a></li><li class="page-item disabled"><span class="page-link">{{ pagination.current_page }} / {{ pagination.last_page }}</span></li><li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }"><a class="page-link" href="javascript:;" @click.prevent="goPage(pagination.current_page + 1)">Siguiente</a></li></ul></nav>
      </div>
    </div></div></div>
    <div class="modal fade" id="modalBanner" tabindex="-1" ref="modalRef"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">{{ isEdit ? 'Editar' : 'Nuevo' }} banner</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
      <form @submit.prevent="submitForm"><div class="mb-3"><label class="form-label">Título *</label><input v-model="form.titulo" type="text" class="form-control" required /></div><div class="mb-3"><label class="form-label">Imagen</label><input type="file" class="form-control" accept="image/*" @change="form.imagenFile = $event.target?.files?.[0] || null" /><img v-if="form.imagenPreview" :src="form.imagenPreview" style="max-height:80px" class="mt-2" /></div><div class="mb-3"><label class="form-label">Enlace</label><input v-model="form.enlace" type="text" class="form-control" placeholder="/nosotros" /></div><div class="mb-3"><label class="form-label">Orden</label><input v-model.number="form.orden" type="number" class="form-control" min="0" /></div><div class="mb-3 form-check"><input v-model="form.activo" type="checkbox" class="form-check-input" id="ba" /><label class="form-check-label" for="ba">Activo</label></div><div class="d-flex justify-content-end gap-2"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Guardando...' : 'Guardar' }}</button></div></form>
    </div></div></div></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Modal } from 'bootstrap';
import { useBanner } from '@/composables/useBanner';
import { showSuccess, showError, showConfirm } from '@/utils/notifications';

const { items, currentItem, loading, fetchList, fetchById, create, update, deleteItem } = useBanner();
const modalRef = ref(null);
let modalInstance = null;
const isEdit = ref(false);
const saving = ref(false);
const form = ref({ titulo: '', imagenFile: null, imagenPreview: null, enlace: '', orden: 0, activo: true });

const listData = computed(() => (Array.isArray(items.value) ? items.value : items.value?.data ?? []));
const pagination = computed(() => ({ current_page: items.value?.current_page ?? 1, last_page: items.value?.last_page ?? 1, per_page: items.value?.per_page ?? 15 }));
const storageUrl = (path) => path?.startsWith('http') ? path : `${(import.meta.env.VITE_API_URL || '').replace(/\/api\/?$/, '') || window.location.origin}/storage/${path}`;

function openCreate() { isEdit.value = false; form.value = { titulo: '', imagenFile: null, imagenPreview: null, enlace: '', orden: 0, activo: true }; modalInstance?.show(); }

async function openEdit(id) { isEdit.value = true; await fetchById(id); const c = currentItem.value; if (!c) return; form.value = { id: c.id, titulo: c.titulo ?? '', imagenFile: null, imagenPreview: c.imagen ? storageUrl(c.imagen) : null, enlace: c.enlace ?? '', orden: c.orden ?? 0, activo: !!c.activo }; modalInstance?.show(); }

async function submitForm() { saving.value = true; const payload = { titulo: form.value.titulo, enlace: form.value.enlace || null, orden: form.value.orden, activo: form.value.activo }; if (form.value.imagenFile) payload.imagen = form.value.imagenFile; try { if (isEdit.value) { await update(form.value.id, payload); showSuccess('Banner actualizado.'); } else { await create(payload); showSuccess('Banner creado.'); } modalInstance?.hide(); refrescar(); } catch (err) { showError(err?.response?.data?.message || 'Error'); } finally { saving.value = false; } }

async function confirmDelete(row) { if (!(await showConfirm(`¿Eliminar el banner "${row.titulo}"?`, 'Confirmar'))) return; try { await deleteItem(row.id); showSuccess('Eliminado.'); refrescar(); } catch (err) { showError(err?.response?.data?.message); } }
function refrescar() { fetchList({ page: pagination.value.current_page, per_page: pagination.value.per_page }); }
function goPage(p) { if (p >= 1 && p <= pagination.value.last_page) fetchList({ page: p, per_page: pagination.value.per_page }); }

onMounted(() => { fetchList({ per_page: 15 }); if (modalRef.value) modalInstance = new Modal(modalRef.value); });
</script>
