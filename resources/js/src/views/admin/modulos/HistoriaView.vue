<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb"><ul class="navbar-nav flex-row"><li><div class="page-header"><nav class="breadcrumb-one"><ol class="breadcrumb"><li class="breadcrumb-item">Módulos</li><li class="breadcrumb-item active">Historia</li></ol></nav></div></li></ul></teleport>
    <div class="row layout-top-spacing"><div class="col-xl-12"><div class="panel p-3">
      <h4 class="mb-4">Página Historia</h4>
      <div v-if="loading && !page" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
      <form v-else @submit.prevent="submitForm">
        <div class="mb-3"><label class="form-label">Título *</label><input v-model="form.titulo" type="text" class="form-control" required /></div>
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Meta descripción</label><input v-model="form.meta_descripcion" type="text" class="form-control" /></div><div class="col-md-6 mb-3"><label class="form-label">Meta keywords</label><input v-model="form.meta_keywords" type="text" class="form-control" /></div></div>
        <div class="mb-3 form-check"><input v-model="form.estado" type="checkbox" class="form-check-input" id="histEstado" /><label class="form-check-label" for="histEstado">Activo</label></div>
        <h5 class="mt-4">Eventos (timeline)</h5>
        <div v-for="(e, i) in form.eventos" :key="i" class="border rounded p-3 mb-2">
          <div class="row"><div class="col-md-2 mb-2"><input v-model.number="e.anio" type="number" class="form-control form-control-sm" placeholder="Año" min="1900" max="2100" /></div><div class="col-md-4 mb-2"><input v-model="e.titulo" type="text" class="form-control form-control-sm" placeholder="Título" /></div><div class="col-md-2 mb-2"><input v-model.number="e.orden" type="number" class="form-control form-control-sm" min="0" /></div><div class="col-md-2 mb-2"><button type="button" class="btn btn-sm btn-outline-danger" @click="form.eventos.splice(i,1)">Quitar</button></div></div><div class="mb-2"><textarea v-model="e.descripcion" class="form-control form-control-sm" rows="2" placeholder="Descripción"></textarea></div>
        </div><button type="button" class="btn btn-sm btn-outline-secondary mb-3" @click="form.eventos.push({ anio: new Date().getFullYear(), titulo: '', descripcion: null, orden: form.eventos.length })">+ Añadir evento</button>
        <div class="d-flex justify-content-end"><button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Guardando...' : 'Guardar' }}</button></div>
      </form>
    </div></div></div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import PaginaHistoriaRepository from '@/repositories/PaginaHistoriaRepository';
import { showSuccess, showError } from '@/utils/notifications';

const page = ref(null);
const loading = ref(true);
const saving = ref(false);
const form = reactive({ titulo: '', meta_descripcion: '', meta_keywords: '', estado: true, eventos: [] });

async function load() {
  loading.value = true;
  try {
    const data = await PaginaHistoriaRepository.getPage();
    page.value = data;
    if (data) {
      form.titulo = data.titulo ?? '';
      form.meta_descripcion = data.meta_descripcion ?? '';
      form.meta_keywords = data.meta_keywords ?? '';
      form.estado = !!data.estado;
      form.eventos = (data.eventos || []).map(({ id, anio, titulo, descripcion, orden }) => ({ id, anio: anio ?? 0, titulo: titulo ?? '', descripcion: descripcion ?? null, orden: orden ?? 0 }));
    }
    if (!form.eventos.length) form.eventos = [{ anio: new Date().getFullYear(), titulo: '', descripcion: null, orden: 0 }];
  } catch (err) { showError(err?.response?.data?.message || 'Error al cargar'); } finally { loading.value = false; }
}

async function submitForm() {
  saving.value = true;
  try {
    const payload = { titulo: form.titulo, meta_descripcion: form.meta_descripcion || null, meta_keywords: form.meta_keywords || null, estado: form.estado, eventos: form.eventos, imagenes: page.value?.imagenes ?? [] };
    await PaginaHistoriaRepository.updatePage(payload);
    showSuccess('Página Historia guardada.'); load();
  } catch (err) { showError(err?.response?.data?.message || 'Error'); } finally { saving.value = false; }
}

onMounted(load);
</script>
