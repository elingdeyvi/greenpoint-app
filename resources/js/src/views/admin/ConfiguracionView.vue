<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb">
      <ul class="navbar-nav flex-row"><li><div class="page-header"><nav class="breadcrumb-one"><ol class="breadcrumb"><li class="breadcrumb-item active">Configuración general</li></ol></nav></div></li></ul>
    </teleport>
    <div class="row layout-top-spacing">
      <div class="col-xl-12">
        <div class="panel p-3">
          <h4 class="mb-4">Configuración general</h4>
          <p class="text-muted">Edite los valores y pulse Guardar. Los cambios se aplican al sitio público.</p>
          <div v-if="loading && !items.length" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
          <form v-else @submit.prevent="submitForm">
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead><tr><th>Clave</th><th>Valor</th></tr></thead>
                <tbody>
                  <tr v-for="(item, index) in items" :key="item.clave || index">
                    <td class="align-middle"><code>{{ item.clave }}</code></td>
                    <td><input v-model="item.valor" type="text" class="form-control" :placeholder="item.clave" /></td>
                  </tr>
                  <tr v-if="!items.length"><td colspan="2" class="text-center text-muted py-4">No hay claves de configuración.</td></tr>
                </tbody>
              </table>
            </div>
            <div class="d-flex justify-content-end"><button type="submit" class="btn btn-primary" :disabled="saving">{{ saving ? 'Guardando...' : 'Guardar cambios' }}</button></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useConfiguracion } from '@/composables/useConfiguracion';
import { showSuccess, showError } from '@/utils/notifications';

const { items, loading, fetchConfig, updateConfig } = useConfiguracion();
const saving = ref(false);

async function submitForm() {
  saving.value = true;
  try {
    const list = Array.isArray(items.value) ? items.value : [];
    await updateConfig(list.map(({ clave, valor }) => ({ clave, valor: valor ?? '' })));
    showSuccess('Configuración guardada.');
  } catch (err) {
    showError(err?.response?.data?.message || 'Error al guardar');
  } finally {
    saving.value = false;
  }
}

onMounted(() => fetchConfig());
</script>
