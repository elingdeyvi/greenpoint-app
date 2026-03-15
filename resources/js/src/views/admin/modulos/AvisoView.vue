<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb"><ul class="navbar-nav flex-row"><li><div class="page-header"><nav class="breadcrumb-one"><ol class="breadcrumb"><li class="breadcrumb-item">Módulos</li><li class="breadcrumb-item active">Aviso de Privacidad</li></ol></nav></div></li></ul></teleport>
    <div class="row layout-top-spacing"><div class="col-xl-12"><div class="panel p-3">
      <h4 class="mb-4">Aviso de Privacidad</h4>
      <div v-if="loading && !page" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
      <form v-else @submit.prevent="submitForm">
        <div class="mb-3"><label class="form-label">Título *</label><input v-model="form.titulo" type="text" class="form-control" required /></div>
        <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Meta descripción</label><input v-model="form.meta_descripcion" type="text" class="form-control" /></div><div class="col-md-6 mb-3"><label class="form-label">Meta keywords</label><input v-model="form.meta_keywords" type="text" class="form-control" /></div></div>
        <div class="mb-3 form-check"><input v-model="form.estado" type="checkbox" class="form-check-input" id="avEstado" /><label class="form-check-label" for="avEstado">Activo</label></div>
        <h5 class="mt-4">Secciones</h5>
        <div v-for="(sec, si) in form.secciones" :key="si" class="border rounded p-3 mb-3">
          <div class="mb-2"><input v-model="sec.titulo" type="text" class="form-control form-control-sm" placeholder="Título sección" /></div><div class="mb-2"><textarea v-model="sec.contenido" class="form-control form-control-sm" rows="2" placeholder="Contenido"></textarea></div><div class="mb-2"><label class="small">Lista (viñetas)</label><div v-for="(item, li) in sec.listas" :key="li" class="input-group input-group-sm mb-1"><input v-model="item.texto" type="text" class="form-control" placeholder="Texto ítem" /><button type="button" class="btn btn-outline-danger" @click="sec.listas.splice(li,1)">×</button></div><button type="button" class="btn btn-sm btn-outline-secondary" @click="sec.listas.push({ texto: '', orden: sec.listas.length })">+ Ítem</button></div><div class="d-flex justify-content-between"><input v-model.number="sec.orden" type="number" class="form-control form-control-sm w-25" min="0" /><button type="button" class="btn btn-sm btn-outline-danger" @click="form.secciones.splice(si,1)">Quitar sección</button></div>
        </div><button type="button" class="btn btn-sm btn-outline-secondary mb-3" @click="form.secciones.push({ titulo: '', contenido: '', orden: form.secciones.length, listas: [] })">+ Añadir sección</button>
        <div class="d-flex justify-content-end"><button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Guardando...' : 'Guardar' }}</button></div>
      </form>
    </div></div></div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import PaginaAvisoRepository from '@/repositories/PaginaAvisoRepository';
import { showSuccess, showError } from '@/utils/notifications';

const page = ref(null);
const loading = ref(true);
const saving = ref(false);
const form = reactive({ titulo: '', meta_descripcion: '', meta_keywords: '', estado: true, secciones: [] });

function mapSeccion(s) {
  return { id: s.id, titulo: s.titulo ?? '', contenido: s.contenido ?? '', orden: (s.orden ?? 0), listas: (s.listas || []).map(({ id, texto, orden }) => ({ id, texto: texto ?? '', orden: orden ?? 0 })) };
}

async function load() {
  loading.value = true;
  try {
    const data = await PaginaAvisoRepository.getPage();
    page.value = data;
    if (data) {
      form.titulo = data.titulo ?? '';
      form.meta_descripcion = data.meta_descripcion ?? '';
      form.meta_keywords = data.meta_keywords ?? '';
      form.estado = !!data.estado;
      form.secciones = (data.secciones || []).map(mapSeccion);
    }
    if (!form.secciones.length) form.secciones = [{ titulo: '', contenido: '', orden: 0, listas: [] }];
  } catch (err) { showError(err?.response?.data?.message || 'Error'); } finally { loading.value = false; }
}

async function submitForm() {
  saving.value = true;
  try {
    await PaginaAvisoRepository.updatePage({ titulo: form.titulo, meta_descripcion: form.meta_descripcion || null, meta_keywords: form.meta_keywords || null, estado: form.estado, secciones: form.secciones });
    showSuccess('Aviso de Privacidad guardado.'); load();
  } catch (err) { showError(err?.response?.data?.message || 'Error'); } finally { saving.value = false; }
}

onMounted(load);
</script>
