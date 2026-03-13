<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb">
      <ul class="navbar-nav flex-row">
        <li>
          <div class="page-header">
            <nav class="breadcrumb-one" aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link to="/ventas">Ventas</router-link></li>
                <li class="breadcrumb-item active" aria-current="page"><span>Importar Pedido (QR)</span></li>
              </ol>
            </nav>
          </div>
        </li>
      </ul>
    </teleport>

    <div class="row layout-top-spacing">
      <div class="col-xl-8 offset-xl-2 layout-spacing">
        <div class="panel p-4">
          <h3 class="titulo-dorado mb-4">Importar Pedido — Modo Macuspana</h3>
          <p class="text-muted mb-4">
            Escanee el código QR del ticket de Villahermosa. La venta local se creará automáticamente y se generará el ticket de salida de esta sucursal (relacionado al ticket origen).
          </p>

          <div v-if="sucursalActual" class="mb-3">
            <span class="badge bg-primary fs-6">Sucursal: {{ sucursalActual.nombre }}</span>
          </div>
          <div v-else-if="!cargandoSucursal" class="alert alert-warning mb-3">
            Configure la sucursal en <strong>Configuración de la empresa</strong>.
          </div>
          <div v-if="sucursalActual && !esSucursalImportar" class="alert alert-info mb-3">
            Importar pedido (QR) solo está disponible en sucursales tipo almacén (ej. Macuspana).
          </div>

          <!-- Zona oculta de escaneo: sin inputs visibles que manipulen el contenido -->
          <div
            v-if="esSucursalImportar && sucursalActual"
            class="border rounded p-4 text-center mb-4"
            style="min-height: 120px; background: var(--bs-light, #f8f9fa); cursor: pointer;"
            @click="activarEscaneo"
          >
            <input
              ref="inputScanRef"
              type="text"
              class="form-control"
              autocomplete="off"
              aria-label="Captura de QR (oculto)"
              style="position: absolute; opacity: 0; width: 1px; height: 1px; pointer-events: none;"
              @keydown.enter.prevent="onScanEnter"
            />
            <template v-if="!ventaCreada">
              <p class="mb-2 text-muted">
                <span v-if="escaneoActivo">Listo — escanee el código QR ahora.</span>
                <span v-else>Haga clic aquí y luego escanee el código QR.</span>
              </p>
              <small class="text-muted">El lector enviará el contenido automáticamente; no se muestra ni edita el texto.</small>
            </template>
          </div>

          <div v-if="enviando" class="text-center py-3">
            <span class="text-primary">Importando pedido...</span>
          </div>

          <div class="d-flex gap-2 mb-4">
            <router-link to="/ventas" class="btn btn-outline-secondary">Volver a Ventas</router-link>
          </div>

          <!-- Ticket / pedido a entregar (como venta, relacionado al ticket origen) -->
          <div v-if="ventaCreada" class="mt-4 p-4 border rounded bg-white shadow-sm">
            <h5 class="text-success mb-3">Pedido importado — Ticket de salida</h5>
            <div class="row mb-3">
              <div class="col-md-6">
                <p class="mb-1"><strong>Folio:</strong> {{ ventaCreada.folio }}</p>
                <p class="mb-1"><strong>Total:</strong> $ {{ Number(ventaCreada.total).toFixed(2) }}</p>
                <p class="mb-1"><strong>Sucursal (esta):</strong> {{ ventaCreada.sucursal?.nombre }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1"><strong>Ticket origen (sucursal de origen):</strong></p>
                <p class="mb-0 small text-muted font-monospace">{{ ventaCreada.ticket_origen_uuid || '—' }}</p>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-sm table-bordered mb-0">
                <thead>
                  <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="d in (ventaCreada.detalles || [])" :key="d.id">
                    <td>{{ d.producto?.nombre ?? d.producto_id }}</td>
                    <td>{{ d.cantidad_pedida }}</td>
                    <td>m³</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p class="mt-3 mb-0 text-muted small">
              QR de salida generado — listo para impresión. Esta venta está relacionada al ticket origen (Villahermosa).
            </p>
            <button type="button" class="btn btn-outline-primary mt-3" @click="limpiarYEscaneo">
              Importar otro pedido
            </button>
          </div>
        </div>
      </div>
    </div>
    <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useMeta } from "@/composables/use-meta";
import * as ConfiguracionEmpresaRepository from "@/repositories/ConfiguracionEmpresaRepository";
import * as VentaRepository from "@/repositories/VentaRepository";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";

useMeta({ title: "Importar Pedido" });

const sucursalActual = ref(null);
const sucursalId = computed(() => sucursalActual.value?.id ?? "");
const esSucursalImportar = computed(() => sucursalActual.value?.tipo_sucursal === "venta_almacen");
const isLoading = ref(false);
const cargandoSucursal = ref(true);
const enviando = ref(false);
const ventaCreada = ref(null);
const inputScanRef = ref(null);
const escaneoActivo = ref(false);

function activarEscaneo() {
  escaneoActivo.value = true;
  requestAnimationFrame(() => inputScanRef.value?.focus());
}

function onScanEnter(event) {
  const input = event.target;
  const payload = (input?.value || "").trim();
  if (payload) {
    event.preventDefault();
    importar(payload);
    input.value = "";
  }
}

async function importar(qrPayload) {
  const sid = sucursalId.value;
  if (!sid || !qrPayload) return;
  try {
    enviando.value = true;
    ventaCreada.value = null;
    const result = await VentaRepository.importarPedido({
      sucursal_id: parseInt(sid),
      qr_payload: qrPayload,
    });
    ventaCreada.value = result.data;
    window.Swal?.fire?.({ icon: "success", title: result.message || "Pedido importado" });
    requestAnimationFrame(() => inputScanRef.value?.blur());
  } catch (e) {
    const msg = e.response?.data?.errors ? Object.values(e.response.data.errors).flat().join(" ") : e.message;
    window.Swal?.fire?.({ icon: "error", title: "Error", text: msg });
    requestAnimationFrame(() => inputScanRef.value?.focus());
  } finally {
    enviando.value = false;
  }
}

function limpiarYEscaneo() {
  ventaCreada.value = null;
  escaneoActivo.value = true;
  requestAnimationFrame(() => inputScanRef.value?.focus());
}

onMounted(async () => {
  isLoading.value = true;
  cargandoSucursal.value = true;
  try {
    const res = await ConfiguracionEmpresaRepository.getSucursal();
    const suc = res?.data ?? res;
    if (suc?.id) sucursalActual.value = suc;
  } catch (_) {
    sucursalActual.value = null;
  } finally {
    cargandoSucursal.value = false;
    isLoading.value = false;
  }
});
</script>
