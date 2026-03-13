<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb">
      <ul class="navbar-nav flex-row">
        <li>
          <div class="page-header">
            <nav class="breadcrumb-one" aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:;">Entregas</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>Registrar entrega</span></li>
              </ol>
            </nav>
          </div>
        </li>
      </ul>
    </teleport>

    <div class="row layout-top-spacing">
      <div class="col-xl-12 layout-spacing">
        <div class="panel p-3">
          <h3 class="titulo-dorado mb-4">Dashboard de Entrega</h3>

          <!-- Buscar venta por folio o QR -->
          <div class="row mb-4">
            <div class="col-md-6">
              <label>Folio o payload del ticket</label>
              <div class="d-flex gap-2">
                <input
                  v-model="folioBuscar"
                  type="text"
                  class="form-control"
                  placeholder="Ingrese folio o escanee QR"
                  @keyup.enter="buscarVenta"
                />
                <button type="button" class="btn btn-primary" :disabled="!folioBuscar.trim() || buscando" @click="buscarVenta">
                  {{ buscando ? 'Buscando...' : 'Buscar' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Detalle de venta y líneas con progreso -->
          <div v-if="venta" class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span><strong>Venta:</strong> {{ venta.folio }} — {{ venta.sucursal?.nombre }} — Estatus: {{ venta.estatus }}</span>
            </div>
            <div class="card-body">
              <div v-for="d in venta.detalles" :key="d.id" class="mb-4 p-3 border rounded">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <strong>{{ d.producto?.nombre }} ({{ d.producto?.unidad_medida }})</strong>
                  <span class="text-muted">{{ Number(d.cantidad_entregada) }} / {{ Number(d.cantidad_pedida) }} entregado</span>
                </div>
                <div class="progress mb-2" style="height: 24px;">
                  <div
                    class="progress-bar"
                    :class="d.cantidad_entregada >= d.cantidad_pedida ? 'bg-success' : 'bg-primary'"
                    role="progressbar"
                    :style="{ width: porcentaje(d) + '%' }"
                    :aria-valuenow="porcentaje(d)"
                    aria-valuemin="0"
                    aria-valuemax="100"
                  >
                    {{ porcentaje(d).toFixed(0) }}%
                  </div>
                </div>
                <div v-if="restante(d) > 0" class="row align-items-end">
                  <div class="col-md-3">
                    <label class="form-label small">Cantidad a entregar ahora</label>
                    <input
                      v-model.number="cantidadEntregar[d.id]"
                      type="number"
                      class="form-control form-control-sm"
                      :min="0"
                      :max="restante(d)"
                      step="0.01"
                      placeholder="0"
                    />
                  </div>
                  <div class="col-md-3">
                    <label class="form-label small">Foto (opcional por entrega)</label>
                    <input
                      type="file"
                      class="form-control form-control-sm"
                      accept="image/*"
                      @change="onFotoChange($event, d.id)"
                    />
                  </div>
                  <div class="col-md-2">
                    <button
                      type="button"
                      class="btn btn-sm btn-success"
                      :disabled="!cantidadEntregar[d.id] || cantidadEntregar[d.id] <= 0 || enviando"
                      @click="registrarEntrega(d)"
                    >
                      Registrar entrega
                    </button>
                  </div>
                </div>
                <div v-else class="text-success small">Línea completada</div>
              </div>
            </div>
          </div>

          <div v-else-if="buscoYNoHay" class="alert alert-info">
            No se encontró ninguna venta con ese folio o payload.
          </div>
        </div>
      </div>
    </div>
    <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" />
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useMeta } from "@/composables/use-meta";
import * as VentaRepository from "@/repositories/VentaRepository";
import * as EntregaRepository from "@/repositories/EntregaRepository";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";

useMeta({ title: "Dashboard de Entrega" });

const folioBuscar = ref("");
const venta = ref(null);
const buscando = ref(false);
const buscoYNoHay = ref(false);
const isLoading = ref(false);
const enviando = ref(false);
const cantidadEntregar = ref({});
const fotosPorDetalle = ref({});

function porcentaje(d) {
  const ped = Number(d.cantidad_pedida);
  const ent = Number(d.cantidad_entregada);
  if (ped <= 0) return 100;
  return Math.min(100, (ent / ped) * 100);
}

function restante(d) {
  return Math.max(0, Number(d.cantidad_pedida) - Number(d.cantidad_entregada));
}

function onFotoChange(ev, detalleId) {
  const file = ev.target?.files?.[0];
  if (file) fotosPorDetalle.value[detalleId] = file;
}

async function buscarVenta() {
  const folio = folioBuscar.value?.trim();
  if (!folio) return;
  buscando.value = true;
  buscoYNoHay.value = false;
  venta.value = null;
  cantidadEntregar.value = {};
  fotosPorDetalle.value = {};
  try {
    const res = await VentaRepository.getVentas({ folio, per_page: 1 });
    const paginator = res.data || res;
    const arr = Array.isArray(paginator) ? paginator : (paginator.data || []);
    if (arr.length > 0) {
      const v = arr[0];
      const full = await VentaRepository.getVenta(v.id);
      venta.value = full.data || full;
    } else {
      buscoYNoHay.value = true;
    }
  } catch (_) {
    buscoYNoHay.value = true;
  } finally {
    buscando.value = false;
  }
}

async function registrarEntrega(detalle) {
  const cant = cantidadEntregar.value[detalle.id];
  if (!cant || cant <= 0) return;
  const rest = restante(detalle);
  if (cant > rest) {
    window.Swal?.fire?.({ icon: "warning", title: "La cantidad no puede exceder el restante (" + rest + ")" });
    return;
  }
  const esMacuspana = venta.value?.sucursal?.nombre?.toLowerCase().includes("macuspana");
  const foto = fotosPorDetalle.value[detalle.id];
  if (esMacuspana && !foto) {
    window.Swal?.fire?.({ icon: "warning", title: "La foto es obligatoria para entregas en Macuspana" });
    return;
  }
  try {
    enviando.value = true;
    const formData = new FormData();
    formData.append("venta_id", venta.value.id);
    formData.append("venta_detalle_id", detalle.id);
    formData.append("cantidad_despachada", cant);
    if (foto) formData.append("foto", foto);
    await EntregaRepository.registrarEntrega(formData);
    window.Swal?.fire?.({ icon: "success", title: "Entrega registrada" });
    cantidadEntregar.value[detalle.id] = null;
    fotosPorDetalle.value[detalle.id] = null;
    const full = await VentaRepository.getVenta(venta.value.id);
    venta.value = full.data || full;
  } catch (e) {
    const msg = e.response?.data?.errors ? Object.values(e.response.data.errors).flat().join(" ") : e.message;
    window.Swal?.fire?.({ icon: "error", title: "Error", text: msg });
  } finally {
    enviando.value = false;
  }
}
</script>
