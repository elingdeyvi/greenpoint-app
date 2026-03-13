<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb">
      <ul class="navbar-nav flex-row">
        <li>
          <div class="page-header">
            <nav class="breadcrumb-one" aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:;">Inventario</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>Alertas de stock</span></li>
              </ol>
            </nav>
          </div>
        </li>
      </ul>
    </teleport>

    <div class="row layout-top-spacing">
      <div class="col-12 mb-4">
        <div class="d-flex flex-wrap align-items-center gap-3">
          <label class="mb-0">Umbral (≤)</label>
          <input v-model.number="filtros.umbral" type="number" min="0" step="0.01" class="form-control w-auto" style="max-width: 120px;" />
          <button type="button" class="btn btn-primary" @click="cargar">Consultar</button>
        </div>
      </div>

      <div class="col-12">
        <div class="widget widget-table-one">
          <div class="widget-heading">
            <h5>Productos con stock bajo o agotado</h5>
          </div>
          <div class="widget-content">
            <div v-if="loading" class="text-center py-5">Cargando...</div>
            <div v-else-if="!alertas.length" class="text-center text-muted py-5">
              No hay productos por debajo del umbral.
            </div>
            <div v-else class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Producto</th>
                    <th>Sucursal</th>
                    <th class="text-end">Stock actual</th>
                    <th>Unidad</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="a in alertas" :key="a.id">
                    <td>{{ a.nombre }}</td>
                    <td>{{ a.sucursal?.nombre || '—' }}</td>
                    <td class="text-end" :class="Number(a.stock_actual) === 0 ? 'text-danger fw-bold' : ''">
                      {{ Number(a.stock_actual) }}
                    </td>
                    <td>{{ a.unidad_medida || '—' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <loading v-model:active="loading" :can-cancel="false" :is-full-page="true" />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useMeta } from "@/composables/use-meta";
import * as InventarioRepository from "@/repositories/InventarioRepository";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";

useMeta({ title: "Alertas de stock" });

const loading = ref(false);
const alertas = ref([]);
const filtros = ref({ umbral: 0 });

async function cargar() {
  loading.value = true;
  try {
    const params = {
      umbral: filtros.value.umbral ?? 0,
    };
    const res = await InventarioRepository.getAlertas(params);
    alertas.value = res.data ?? res ?? [];
  } catch (e) {
    console.error(e);
    alertas.value = [];
  } finally {
    loading.value = false;
  }
}

onMounted(async () => {
  await cargar();
});
</script>
