<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb"><ul class="navbar-nav flex-row"><li><div class="page-header"><nav class="breadcrumb-one"><ol class="breadcrumb"><li class="breadcrumb-item">Módulos</li><li class="breadcrumb-item active">Tecnología</li></ol></nav></div></li></ul></teleport>
    <div class="row layout-top-spacing"><div class="col-xl-12"><div class="panel p-3">
      <h4 class="mb-4">Página Tecnología</h4>
      <div v-if="loading && !page" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
      <form v-else @submit.prevent="submitForm">
        <div class="mb-3"><label class="form-label">Título *</label><input v-model="form.titulo" type="text" class="form-control" required /></div>
        <div class="mb-3"><label class="form-label">Contenido</label><textarea v-model="form.contenido" class="form-control" rows="5"></textarea></div>
        <div class="mb-3"><label class="form-label">Imagen destacada</label><input type="file" class="form-control" accept="image/*" @change="form.imagen_file = $event.target?.files?.[0] || null" /><img v-if="form.imagen_preview" :src="form.imagen_preview" style="max-height:100px" class="mt-2" /></div>
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Meta descripción</label><input v-model="form.meta_descripcion" type="text" class="form-control" /></div><div class="col-md-6 mb-3"><label class="form-label">Meta keywords</label><input v-model="form.meta_keywords" type="text" class="form-control" /></div></div>
        <div class="mb-3 form-check"><input v-model="form.estado" type="checkbox" class="form-check-input" id="tecEstado" /><label class="form-check-label" for="tecEstado">Activo</label></div>
        <h5 class="mt-4">Secciones</h5>
        <div v-for="(s, i) in form.secciones" :key="i" class="border rounded p-3 mb-2">
          <div class="mb-2"><input v-model="s.titulo" type="text" class="form-control form-control-sm" placeholder="Título sección" /></div><div class="mb-2"><textarea v-model="s.contenido" class="form-control form-control-sm" rows="2" placeholder="Contenido"></textarea></div><div class="d-flex justify-content-between"><input v-model.number="s.orden" type="number" class="form-control form-control-sm w-25" min="0" placeholder="Orden" /><button type="button" class="btn btn-sm btn-outline-danger" @click="form.secciones.splice(i,1)">Quitar</button></div>
        </div><button type="button" class="btn btn-sm btn-outline-secondary mb-3" @click="form.secciones.push({ titulo: '', contenido: '', orden: form.secciones.length })">+ Añadir sección</button>
        <div class="d-flex justify-content-end"><button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Guardando...' : 'Guardar' }}</button></div>
      </form>
    </div></div></div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import PaginaTecnologiaRepository from '@/repositories/PaginaTecnologiaRepository';
import { showSuccess, showError } from '@/utils/notifications';

const page = ref(null);
const loading = ref(true);
const saving = ref(false);
const form = reactive({ titulo: '', contenido: '', imagen_file: null, imagen_preview: null, meta_descripcion: '', meta_keywords: '', estado: true, secciones: [] });

async function load() {
  loading.value = true;
  try {
    const data = await PaginaTecnologiaRepository.getPage();
    page.value = data;
    if (data) {
      form.titulo = data.titulo ?? '';
      form.contenido = data.contenido ?? '';
      form.imagen_preview = data.imagen_destacada ? `${(import.meta.env.VITE_API_URL || '').replace(/\/api\/?$/, '') || ''}/storage/${data.imagen_destacada}` : null;
      form.meta_descripcion = data.meta_descripcion ?? '';
      form.meta_keywords = data.meta_keywords ?? '';
      form.estado = !!data.estado;
      form.secciones = (data.secciones || []).map(({ id, titulo, contenido, orden }) => ({ id, titulo: titulo ?? '', contenido: contenido ?? '', orden: orden ?? 0 }));
    }
    if (!form.secciones.length) form.secciones = [{ titulo: '', contenido: '', orden: 0 }];
  } catch (err) { showError(err?.response?.data?.message || 'Error'); } finally { loading.value = false; }
}

async function submitForm() {
  saving.value = true;
  try {
    const payload = { titulo: form.titulo, contenido: form.contenido || null, meta_descripcion: form.meta_descripcion || null, meta_keywords: form.meta_keywords || null, estado: form.estado, secciones: form.secciones };
    if (form.imagen_file) { const fd = new FormData(); Object.entries(payload).forEach(([k, v]) => { if (v !== undefined && v !== null) fd.append(k, typeof v === 'object' ? JSON.stringify(v) : v); }); fd.append('imagen_destacada', form.imagen_file); fd.append('_method', 'PUT'); await PaginaTecnologiaRepository.updatePage(fd); }
    else await PaginaTecnologiaRepository.updatePage(payload);
    showSuccess('Página Tecnología guardada.'); load();
  } catch (err) { showError(err?.response?.data?.message || 'Error'); } finally { saving.value = false; }
}

onMounted(load);
</script>
