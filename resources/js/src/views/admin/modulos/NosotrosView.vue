<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb"><ul class="navbar-nav flex-row"><li><div class="page-header"><nav class="breadcrumb-one"><ol class="breadcrumb"><li class="breadcrumb-item">Módulos</li><li class="breadcrumb-item active">Nosotros</li></ol></nav></div></li></ul></teleport>
    <div class="row layout-top-spacing"><div class="col-xl-12"><div class="panel p-3">
      <h4 class="mb-4">Página Nosotros</h4>
      <div v-if="loading && !page" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
      <form v-else @submit.prevent="submitForm">
        <div class="mb-3"><label class="form-label">Título *</label><input v-model="form.titulo" type="text" class="form-control" required /></div>
        <div class="mb-3"><label class="form-label">Subtítulo</label><input v-model="form.subtitulo" type="text" class="form-control" /></div>
        <div class="mb-3"><label class="form-label">Texto descriptivo</label><textarea v-model="form.texto_descriptivo" class="form-control" rows="4"></textarea></div>
        <div class="mb-3"><label class="form-label">Texto adicional</label><textarea v-model="form.texto_adicional" class="form-control" rows="3"></textarea></div>
        <div class="mb-3"><label class="form-label">URL video</label><input v-model="form.url_video" type="url" class="form-control" /></div>
        <div class="mb-3"><label class="form-label">Imagen destacada</label><input type="file" class="form-control" accept="image/*" @change="form.imagen_destacada_file = $event.target?.files?.[0] || null" /><img v-if="form.imagen_destacada_preview" :src="form.imagen_destacada_preview" style="max-height:100px" class="mt-2" /></div>
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Meta descripción</label><input v-model="form.meta_descripcion" type="text" class="form-control" /></div><div class="col-md-6 mb-3"><label class="form-label">Meta keywords</label><input v-model="form.meta_keywords" type="text" class="form-control" /></div></div>
        <div class="mb-3 form-check"><input v-model="form.estado" type="checkbox" class="form-check-input" id="nosEstado" /><label class="form-check-label" for="nosEstado">Activo (visible en sitio)</label></div>
        <h5 class="mt-4">Barras de progreso</h5><p class="text-muted small">Título, porcentaje (0-100), descripción y orden.</p>
        <div v-for="(p, i) in form.progreso" :key="i" class="border rounded p-3 mb-2">
          <div class="row"><div class="col-md-4 mb-2"><input v-model="p.titulo" type="text" class="form-control form-control-sm" placeholder="Título" /></div><div class="col-md-2 mb-2"><input v-model.number="p.porcentaje" type="number" class="form-control form-control-sm" min="0" max="100" placeholder="%" /></div><div class="col-md-2 mb-2"><input v-model.number="p.orden" type="number" class="form-control form-control-sm" min="0" placeholder="Orden" /></div><div class="col-md-2 mb-2"><button type="button" class="btn btn-sm btn-outline-danger" @click="form.progreso.splice(i,1)">Quitar</button></div></div><div class="mb-2"><input v-model="p.descripcion" type="text" class="form-control form-control-sm" placeholder="Descripción" /></div>
        </div><button type="button" class="btn btn-sm btn-outline-secondary mb-3" @click="form.progreso.push({ titulo: '', porcentaje: 0, descripcion: null, orden: form.progreso.length })">+ Añadir barra</button>
        <div class="d-flex justify-content-end"><button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Guardando...' : 'Guardar' }}</button></div>
      </form>
    </div></div></div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import PaginaNosotrosRepository from '@/repositories/PaginaNosotrosRepository';
import { showSuccess, showError } from '@/utils/notifications';

const page = ref(null);
const loading = ref(true);
const saving = ref(false);
const form = reactive({ titulo: '', subtitulo: '', texto_descriptivo: '', texto_adicional: '', url_video: '', imagen_destacada_file: null, imagen_destacada_preview: null, meta_descripcion: '', meta_keywords: '', estado: true, progreso: [] });

async function load() {
  loading.value = true;
  try {
    const data = await PaginaNosotrosRepository.getPage();
    page.value = data;
    if (data) {
      form.titulo = data.titulo ?? '';
      form.subtitulo = data.subtitulo ?? '';
      form.texto_descriptivo = data.texto_descriptivo ?? '';
      form.texto_adicional = data.texto_adicional ?? '';
      form.url_video = data.url_video ?? '';
      form.imagen_destacada_preview = data.imagen_destacada ? `${(import.meta.env.VITE_API_URL || '').replace(/\/api\/?$/, '') || ''}/storage/${data.imagen_destacada}` : null;
      form.meta_descripcion = data.meta_descripcion ?? '';
      form.meta_keywords = data.meta_keywords ?? '';
      form.estado = !!data.estado;
      form.progreso = (data.progreso || []).map(({ id, titulo, porcentaje, descripcion, orden }) => ({ id, titulo: titulo ?? '', porcentaje: porcentaje ?? 0, descripcion: descripcion ?? null, orden: orden ?? 0 }));
    }
    if (!form.progreso.length) form.progreso = [{ titulo: '', porcentaje: 0, descripcion: null, orden: 0 }];
  } catch (err) { showError(err?.response?.data?.message || 'Error al cargar'); } finally { loading.value = false; }
}

async function submitForm() {
  saving.value = true;
  try {
    const payload = { titulo: form.titulo, subtitulo: form.subtitulo || null, texto_descriptivo: form.texto_descriptivo || null, texto_adicional: form.texto_adicional || null, url_video: form.url_video || null, meta_descripcion: form.meta_descripcion || null, meta_keywords: form.meta_keywords || null, estado: form.estado, progreso: form.progreso, imagenes: page.value?.imagenes ?? [] };
    if (form.imagen_destacada_file) { const fd = new FormData(); Object.entries(payload).forEach(([k, v]) => { if (v !== undefined && v !== null) { if (typeof v === 'object' && !(v instanceof File)) fd.append(k, JSON.stringify(v)); else fd.append(k, v); } }); fd.append('imagen_destacada', form.imagen_destacada_file); fd.append('_method', 'PUT'); await PaginaNosotrosRepository.updatePage(fd); }
    else await PaginaNosotrosRepository.updatePage(payload);
    showSuccess('Página Nosotros guardada.'); load();
  } catch (err) { showError(err?.response?.data?.message || 'Error al guardar'); } finally { saving.value = false; }
}

onMounted(load);
</script>
