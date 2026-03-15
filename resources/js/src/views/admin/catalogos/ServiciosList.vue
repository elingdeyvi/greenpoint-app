<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb">
      <ul class="navbar-nav flex-row">
        <li>
          <div class="page-header">
            <nav class="breadcrumb-one" aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:;">Catálogos</a></li>
                <li class="breadcrumb-item active">Servicios</li>
              </ol>
            </nav>
          </div>
        </li>
      </ul>
    </teleport>

    <div class="row layout-top-spacing">
      <div class="col-xl-12">
        <div class="panel p-3">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Servicios</h4>
            <div>
              <button class="btn btn-primary me-2" @click="refrescar" :disabled="loading">Refrescar</button>
              <button class="btn btn-success" @click="openCreate">Nuevo</button>
            </div>
          </div>

          <div v-if="loading && !listData.length" class="text-center py-5">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>
          </div>
          <div v-else>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Orden</th>
                    <th>Activo</th>
                    <th width="120">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="row in listData" :key="row.id">
                    <td>{{ row.id }}</td>
                    <td>{{ row.nombre }}</td>
                    <td>{{ row.orden }}</td>
                    <td><span :class="row.activo ? 'badge bg-success' : 'badge bg-secondary'">{{ row.activo ? 'Sí' : 'No' }}</span></td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary me-1" @click="openEdit(row.id)" title="Editar">Editar</button>
                      <button class="btn btn-sm btn-outline-danger" @click="confirmDelete(row)" title="Eliminar">Eliminar</button>
                    </td>
                  </tr>
                  <tr v-if="!listData.length">
                    <td colspan="5" class="text-center text-muted py-4">No hay registros.</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <nav v-if="pagination.last_page > 1" class="mt-3">
              <ul class="pagination pagination-sm mb-0">
                <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
                  <a class="page-link" href="javascript:;" @click.prevent="goPage(pagination.current_page - 1)">Anterior</a>
                </li>
                <li class="page-item disabled"><span class="page-link">{{ pagination.current_page }} / {{ pagination.last_page }}</span></li>
                <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
                  <a class="page-link" href="javascript:;" @click.prevent="goPage(pagination.current_page + 1)">Siguiente</a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Crear/Editar -->
    <div class="modal fade" id="modalServicio" tabindex="-1" ref="modalRef">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEdit ? 'Editar servicio' : 'Nuevo servicio' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="submitForm">
              <div class="mb-3">
                <label class="form-label">Nombre *</label>
                <input v-model="form.nombre" type="text" class="form-control" required maxlength="255" />
              </div>
              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea v-model="form.descripcion" class="form-control" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Imagen</label>
                <input type="file" class="form-control" accept="image/*" @change="onFileChange" />
                <small class="text-muted">Opcional. Solo subir para cambiar.</small>
                <div v-if="form.imagenPreview" class="mt-2"><img :src="form.imagenPreview" alt="Preview" style="max-height:80px" /></div>
              </div>
              <div class="row">
                <div class="col-6 mb-3">
                  <label class="form-label">Orden</label>
                  <input v-model.number="form.orden" type="number" class="form-control" min="0" />
                </div>
                <div class="col-6 mb-3 d-flex align-items-end">
                  <div class="form-check">
                    <input v-model="form.activo" type="checkbox" class="form-check-input" id="chkActivo" />
                    <label class="form-check-label" for="chkActivo">Activo</label>
                  </div>
                </div>
              </div>
              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Guardando...' : 'Guardar' }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Modal } from 'bootstrap';
import { useServicio } from '@/composables/useServicio';
import { showSuccess, showError, showConfirm } from '@/utils/notifications';

const { items, currentItem, loading, fetchList, fetchById, create, update, deleteItem } = useServicio();
const modalRef = ref(null);
let modalInstance = null;
const isEdit = ref(false);
const saving = ref(false);
const form = ref({
  nombre: '',
  descripcion: '',
  imagen: null,
  imagenPreview: null,
  orden: 0,
  activo: true,
});

const listData = computed(() => {
  const d = items.value;
  if (Array.isArray(d)) return d;
  return d?.data ?? [];
});

const pagination = computed(() => ({
  current_page: items.value?.current_page ?? 1,
  last_page: items.value?.last_page ?? 1,
  per_page: items.value?.per_page ?? 15,
}));

function openCreate() {
  isEdit.value = false;
  form.value = { nombre: '', descripcion: '', imagen: null, imagenPreview: null, orden: 0, activo: true };
  modalInstance?.show();
}

async function openEdit(id) {
  isEdit.value = true;
  await fetchById(id);
  const cur = currentItem.value;
  if (!cur) return;
  const baseUrl = (import.meta.env.VITE_API_URL || '').replace(/\/api\/?$/, '') || window.location.origin;
  form.value = {
    id: cur.id,
    nombre: cur.nombre ?? '',
    descripcion: cur.descripcion ?? '',
    imagen: null,
    imagenPreview: cur.imagen ? `${baseUrl}/storage/${cur.imagen}` : null,
    orden: cur.orden ?? 0,
    activo: !!cur.activo,
  };
  modalInstance?.show();
}

function onFileChange(e) {
  const file = e.target?.files?.[0];
  form.value.imagen = file || null;
  form.value.imagenPreview = file ? URL.createObjectURL(file) : null;
}

async function submitForm() {
  saving.value = true;
  const payload = {
    nombre: form.value.nombre,
    descripcion: form.value.descripcion || null,
    orden: form.value.orden,
    activo: form.value.activo,
  };
  if (form.value.imagen) payload.imagen = form.value.imagen;
  try {
    if (isEdit.value) {
      await update(form.value.id, payload);
      showSuccess('Servicio actualizado.');
    } else {
      await create(payload);
      showSuccess('Servicio creado.');
    }
    modalInstance?.hide();
    refrescar();
  } catch (err) {
    const msg = err?.response?.data?.message || err?.message || 'Error al guardar';
    showError(msg);
  } finally {
    saving.value = false;
  }
}

async function confirmDelete(row) {
  const ok = await showConfirm(`¿Eliminar el servicio "${row.nombre}"?`, 'Confirmar');
  if (!ok) return;
  try {
    await deleteItem(row.id);
    showSuccess('Servicio eliminado.');
    refrescar();
  } catch (err) {
    showError(err?.response?.data?.message || 'Error al eliminar');
  }
}

function refrescar() {
  fetchList({ page: pagination.value.current_page, per_page: pagination.value.per_page });
}

function goPage(page) {
  if (page < 1 || page > pagination.value.last_page) return;
  fetchList({ page, per_page: pagination.value.per_page });
}

onMounted(() => {
  fetchList({ per_page: 15 });
  if (modalRef.value) modalInstance = new Modal(modalRef.value);
});
</script>
