<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb">
      <ul class="navbar-nav flex-row">
        <li>
          <div class="page-header">
            <nav class="breadcrumb-one" aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:;">Caja</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>Caja y gastos</span></li>
              </ol>
            </nav>
          </div>
        </li>
      </ul>
    </teleport>

    <div class="row layout-top-spacing">
      <!-- Caja abierta -->
      <div class="col-12 mb-4">
        <div class="widget widget-card-one">
          <div class="widget-heading">
            <h5>Caja actual</h5>
          </div>
          <div class="widget-content">
            <div v-if="loadingCaja">Cargando...</div>
            <div v-else-if="!cajaAbierta" class="d-flex flex-wrap align-items-end gap-3">
              <div>
                <label class="form-label">Sucursal</label>
                <input type="text" class="form-control" :value="sucursalActual?.nombre || 'Sin sucursal configurada'" readonly />
              </div>
              <div>
                <label class="form-label">Monto inicial</label>
                <input v-model.number="apertura.monto_inicial" type="number" min="0" step="0.01" class="form-control" placeholder="0" />
              </div>
              <button type="button" class="btn btn-primary" :disabled="!sucursalId || apertura.monto_inicial < 0 || guardando" @click="abrirCaja">
                {{ guardando ? 'Abriendo...' : 'Abrir caja' }}
              </button>
            </div>
            <div v-else class="d-flex flex-wrap justify-content-between align-items-center">
              <div>
                <p class="mb-1">
                  <strong>{{ cajaAbierta.sucursal?.nombre || 'Sucursal no disponible' }}</strong>
                  — Abierta por {{ cajaAbierta.usuario?.name || 'Usuario no disponible' }}
                </p>
                <p class="mb-0 text-muted small">
                  Monto inicial: $
                  {{ Number(cajaAbierta.monto_inicial ?? 0).toFixed(2) }}
                  — {{ formatDate(cajaAbierta.fecha_apertura) }}
                </p>
              </div>
              <div class="d-flex gap-2 align-items-center">
                <button type="button" class="btn btn-success btn-sm" @click="mostrarModalGasto = true">Registrar gasto</button>
                <button type="button" class="btn btn-warning btn-sm" @click="mostrarCierre = true">Cerrar caja</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gastos de la caja abierta -->
      <div v-if="cajaAbierta" class="col-12 mb-4">
        <div class="widget widget-table-one">
          <div class="widget-heading d-flex justify-content-between align-items-center">
            <h5>Gastos de esta caja</h5>
            <button type="button" class="btn btn-sm btn-success" @click="mostrarModalGasto = true">Nuevo gasto</button>
          </div>
          <div class="widget-content">
            <div v-if="loadingGastos">Cargando gastos...</div>
            <div v-else-if="!gastos.length" class="text-muted small">Sin gastos registrados.</div>
            <div v-else class="table-responsive">
              <table class="table table-bordered table-sm">
                <thead>
                  <tr>
                    <th>Descripción</th>
                    <th class="text-end">Monto</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="g in gastos" :key="g.id">
                    <td>{{ g.descripcion }}</td>
                    <td class="text-end">$ {{ Number(g.monto).toFixed(2) }}</td>
                  </tr>
                </tbody>
              </table>
              <p class="mb-0 small"><strong>Total gastos:</strong> $ {{ totalGastos.toFixed(2) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal nuevo gasto -->
      <div v-if="mostrarModalGasto" class="modal d-block bg-dark bg-opacity-50" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Registrar gasto</h5>
              <button type="button" class="btn-close" @click="mostrarModalGasto = false"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Descripción</label>
                <input v-model="nuevoGasto.descripcion" type="text" class="form-control" placeholder="Concepto del gasto" />
              </div>
              <div class="mb-3">
                <label class="form-label">Monto</label>
                <input v-model.number="nuevoGasto.monto" type="number" min="0" step="0.01" class="form-control" />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="mostrarModalGasto = false">Cancelar</button>
              <button type="button" class="btn btn-primary" :disabled="!nuevoGasto.descripcion || nuevoGasto.monto <= 0 || guardando" @click="registrarGasto">
                Guardar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Cerrar caja -->
      <div v-if="mostrarCierre" class="modal d-block bg-dark bg-opacity-50" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Cerrar caja</h5>
              <button type="button" class="btn-close" @click="mostrarCierre = false"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Monto final en caja (obligatorio)</label>
                <input v-model.number="cierre.monto_final" type="number" min="0" step="0.01" class="form-control" placeholder="Efectivo al cierre" />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="mostrarCierre = false">Cancelar</button>
              <button type="button" class="btn btn-warning" :disabled="guardando || cierre.monto_final == null || cierre.monto_final < 0" @click="cerrarCaja">Cerrar caja</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <loading v-model:active="loadingCaja" :can-cancel="false" :is-full-page="true" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useMeta } from "@/composables/use-meta";
import { useCaja } from "@/composables/useCaja";
import * as CajaRepository from "@/repositories/CajaRepository";
import * as ConfiguracionEmpresaRepository from "@/repositories/ConfiguracionEmpresaRepository";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";

useMeta({ title: "Caja y gastos" });
const { setCajaAbierta, clearCajaAbierta } = useCaja();

const loadingCaja = ref(false);
const loadingGastos = ref(false);
const guardando = ref(false);
const sucursalActual = ref(null);
const cajaAbierta = ref(null);
const gastos = ref([]);
const apertura = ref({ sucursal_id: "", monto_inicial: 0 });
const cierre = ref({ monto_final: null });
const nuevoGasto = ref({ descripcion: "", monto: 0 });
const mostrarModalGasto = ref(false);
const mostrarCierre = ref(false);

const sucursalId = computed(() => sucursalActual.value?.id ?? "");
const totalGastos = computed(() => gastos.value.reduce((s, g) => s + Number(g.monto), 0));

function formatDate(d) {
  if (!d) return "—";
  return new Date(d).toLocaleString("es-MX");
}

async function cargarCajaAbierta() {
  loadingCaja.value = true;
  try {
    // El backend usa siempre la sucursal configurada en ConfiguracionEmpresa;
    // no es necesario enviar sucursal_id desde el front.
    const res = await CajaRepository.getCajaAbierta();
    cajaAbierta.value = res.data ?? res ?? null;
    if (cajaAbierta.value?.id) await cargarGastos(cajaAbierta.value.id);
  } catch (_) {
    cajaAbierta.value = null;
  } finally {
    loadingCaja.value = false;
  }
}

async function cargarGastos(cajaId) {
  loadingGastos.value = true;
  try {
    const res = await CajaRepository.getGastos(cajaId);
    gastos.value = res.data ?? res ?? [];
  } catch (_) {
    gastos.value = [];
  } finally {
    loadingGastos.value = false;
  }
}

async function abrirCaja() {
  guardando.value = true;
  try {
    await CajaRepository.abrirCaja({
      sucursal_id: sucursalId.value,
      monto_inicial: apertura.value.monto_inicial ?? 0,
    });
    window.Swal?.fire?.({ icon: "success", title: "Caja abierta" });
    apertura.value = { sucursal_id: "", monto_inicial: 0 };
    await cargarCajaAbierta();
    setCajaAbierta(cajaAbierta.value);
  } catch (e) {
    const msg = e.response?.data?.errors?.caja?.[0] || e.message;
    window.Swal?.fire?.({ icon: "error", title: "Error", text: msg });
  } finally {
    guardando.value = false;
  }
}

async function cerrarCaja() {
  guardando.value = true;
  try {
    await CajaRepository.cerrarCaja({
      caja_id: cajaAbierta.value.id,
      monto_final: cierre.value.monto_final ?? undefined,
    });
    window.Swal?.fire?.({ icon: "success", title: "Caja cerrada" });
    mostrarCierre.value = false;
    cajaAbierta.value = null;
    gastos.value = [];
    clearCajaAbierta();
    await cargarCajaAbierta();
  } catch (e) {
    const msg = e.response?.data?.errors?.caja?.[0] || e.message;
    window.Swal?.fire?.({ icon: "error", title: "Error", text: msg });
  } finally {
    guardando.value = false;
  }
}

async function registrarGasto() {
  if (!cajaAbierta.value?.id) return;
  guardando.value = true;
  try {
    await CajaRepository.crearGasto(cajaAbierta.value.id, {
      descripcion: nuevoGasto.value.descripcion,
      monto: nuevoGasto.value.monto,
    });
    window.Swal?.fire?.({ icon: "success", title: "Gasto registrado" });
    nuevoGasto.value = { descripcion: "", monto: 0 };
    mostrarModalGasto.value = false;
    await cargarGastos(cajaAbierta.value.id);
  } catch (e) {
    const msg = e.response?.data?.errors?.caja?.[0] || e.message;
    window.Swal?.fire?.({ icon: "error", title: "Error", text: msg });
  } finally {
    guardando.value = false;
  }
}

onMounted(async () => {
  try {
    const res = await ConfiguracionEmpresaRepository.getSucursal();
    const suc = res?.data ?? res;
    if (suc?.id) {
      sucursalActual.value = suc;
    }
  } catch (_) {
    sucursalActual.value = null;
  }
  await cargarCajaAbierta();
});
</script>
