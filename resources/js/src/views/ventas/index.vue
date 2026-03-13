<template>
  <div class="layout-px-spacing pos-ventas">
    <teleport to="#breadcrumb">
      <ul class="navbar-nav flex-row">
        <li>
          <div class="page-header">
            <nav class="breadcrumb-one" aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:;">{{ $t('sales') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>{{ $t('pos_nueva_venta') }}</span></li>
              </ol>
            </nav>
          </div>
        </li>
      </ul>
    </teleport>

    <div class="row layout-top-spacing g-3">
      <!-- Sucursal y título -->
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
          <h3 class="titulo-dorado mb-0">{{ $t('pos_nueva_venta') }}</h3>
          <div class="d-flex align-items-center gap-2">
            <span v-if="sucursalActiva" class="badge bg-primary fs-6">Venta en: {{ sucursalActiva.nombre }}</span>
            <button type="button" class="btn btn-outline-secondary btn-sm" @click="cargarVentas">Refrescar ventas</button>
          </div>
        </div>
        <p v-if="sucursalActiva" class="text-muted small mb-0 mt-1">
          {{ perfilInterfaz === 'VENTA_ALMACEN' ? $t('pos_solo_macuspana') : $t('pos_catalogo_completo') }}
        </p>
        <div v-else-if="!cargandoSucursal" class="alert alert-warning mt-2 mb-0">
          Configure la sucursal en <strong>Configuración de la empresa</strong>.
        </div>
        <div v-if="esPerfilVenta && !cajaAbierta && sucursalId" class="alert alert-danger mt-2 mb-0">
          Debe realizar la apertura de caja antes de procesar ventas. <router-link to="/cajas">Abrir caja</router-link>
        </div>
      </div>

      <!-- Grid de productos (cards) -->
      <div class="col-lg-8 col-xl-9">
        <div v-if="sucursalId" class="pos-productos">
          <h5 class="mb-3">{{ $t('pos_productos') }}</h5>
          <div class="row g-3">
            <div
              v-for="p in productosFiltrados"
              :key="p.id"
              class="col-6 col-md-4 col-lg-4"
            >
              <div
                class="card pos-product-card h-100 shadow-sm border-0"
                :class="{ 'pos-product-card-bump': lastUpdatedProductId === p.id }"
              >
                <div class="card-body d-flex flex-column">
                  <h6 class="card-title text-dark mb-2 text-truncate" :title="p.nombre">{{ p.nombre }}</h6>
                  <p class="card-text text-primary fw-bold mb-2 fs-5">$ {{ Number(p.precio_unitario || 0).toFixed(2) }}</p>
                  <div class="mt-auto">
                    <div class="input-group input-group-sm mb-2">
                      <input
                        v-model.number="cantidades[p.id]"
                        type="number"
                        class="form-control"
                        min="0.01"
                        step="0.01"
                        placeholder="0"
                        @keydown.enter.prevent="agregarDetalle(p)"
                      />
                      <span class="input-group-text">m³</span>
                    </div>
                    <button
                      type="button"
                      class="btn btn-primary w-100 btn-sm"
                      :disabled="!cantidades[p.id] || cantidades[p.id] <= 0"
                      @click="agregarDetalle(p)"
                    >
                      {{ $t('pos_agregar_al_carrito') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="productosFiltrados.length === 0" class="col-12 text-center text-muted py-5">
              {{ perfilInterfaz === 'VENTA_ALMACEN' ? 'No hay productos Polvo, Rezaga o Balastre.' : 'No hay productos cargados.' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Panel lateral: Carrito -->
      <div class="col-lg-4 col-xl-3">
        <div class="card pos-cart-card shadow-sm sticky-top">
          <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $t('pos_carrito') }}</h5>
            <button
              v-if="detalles.length > 0"
              type="button"
              class="btn btn-outline-danger btn-sm"
              @click="vaciarCarrito"
            >
              {{ $t('pos_vaciar_carrito') }}
            </button>
          </div>
          <div class="card-body p-0">
            <ul v-if="detalles.length > 0" class="list-group list-group-flush">
              <li
                v-for="(d, idx) in detalles"
                :key="idx"
                class="list-group-item d-flex justify-content-between align-items-start"
              >
                <div class="ms-2 me-auto">
                  <div class="fw-semibold">{{ d.nombre }}</div>
                  <small class="text-muted">{{ d.cantidad_pedida }} m³ × $ <span class="pos-precio-solo-lectura">{{ Number(d.precio_unitario).toFixed(2) }}</span></small>
                </div>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge bg-primary rounded-pill">$ {{ d.subtotal.toFixed(2) }}</span>
                  <button type="button" class="btn btn-sm btn-outline-danger p-1" :title="$t('pos_quitar')" @click="quitarDetalle(idx)">×</button>
                </div>
              </li>
            </ul>
            <div v-else class="text-center text-muted py-5 px-3">
              <p class="mb-0">El carrito está vacío</p>
              <small>Agregue productos desde el catálogo</small>
            </div>
          </div>
          <div v-if="detalles.length > 0" class="card-footer bg-white border-top">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="fw-bold">{{ $t('pos_total') }}:</span>
              <span class="fs-4 text-primary fw-bold">$ {{ totalVenta.toFixed(2) }}</span>
            </div>
            <button
              type="button"
              class="btn btn-success w-100 py-2"
              @click="abrirModalFinalizar"
            >
              {{ $t('pos_finalizar_venta') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Lista de ventas recientes (debajo) -->
      <div class="col-12 mt-4">
        <h5 class="mb-3">Ventas recientes</h5>
        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th>Folio</th>
                <th>Sucursal</th>
                <th>Total</th>
                <th v-if="perfilInterfaz !== 'VENTA'">Estatus</th>
                <th>Fecha</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="v in ventasRecientes" :key="v.id">
                <td>{{ v.folio }}</td>
                <td>{{ v.sucursal?.nombre }}</td>
                <td>$ {{ Number(v.total).toFixed(2) }}</td>
                <td v-if="perfilInterfaz !== 'VENTA'"><span :class="badgeEstatus(v.estatus)">{{ v.estatus }}</span></td>
                <td>{{ formatearFecha(v.created_at) }}</td>
              </tr>
              <tr v-if="ventasRecientes.length === 0">
                <td :colspan="perfilInterfaz === 'VENTA' ? 4 : 5" class="text-center text-muted">No hay ventas</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Finalizar Venta -->
    <div
      class="modal fade"
      tabindex="-1"
      aria-labelledby="posModalLabel"
      aria-hidden="true"
      ref="modalFinalizarRef"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="posModalLabel">{{ $t('pos_finalizar_venta') }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <!-- Resumen total -->
            <div class="alert alert-light border mb-4">
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">{{ $t('pos_total') }} a pagar:</span>
                <span class="fs-4 text-success fw-bold">$ {{ totalVenta.toFixed(2) }}</span>
              </div>
            </div>

            <!-- Toggle Donativo (destacado) -->
            <div class="mb-4 p-3 rounded-3" :class="esDonativo ? 'bg-warning bg-opacity-25 border border-warning' : 'bg-light'">
              <div class="form-check form-switch form-check-lg">
                <input
                  id="pos-donativo"
                  v-model="esDonativo"
                  class="form-check-input"
                  type="checkbox"
                  role="switch"
                />
                <label class="form-check-label fw-bold" for="pos-donativo">{{ $t('pos_donativo') }}</label>
              </div>
              <p v-if="esDonativo" class="small text-muted mb-2 mt-1">{{ $t('pos_observaciones_donativo') }}</p>
              <textarea
                v-if="esDonativo"
                v-model="observacionesDonativo"
                class="form-control"
                rows="2"
                :placeholder="$t('pos_observaciones_placeholder')"
                :class="{ 'is-invalid': esDonativo && observacionesRequeridasYVacias }"
              />
              <div v-if="esDonativo && observacionesRequeridasYVacias" class="invalid-feedback d-block">
                Las observaciones son obligatorias para donativos.
              </div>
            </div>

            <!-- Tabla de pagos (oculta si es donativo) -->
            <div v-if="!esDonativo" class="mb-3">
              <h6 class="mb-2">{{ $t('pos_pagos') }}</h6>
              <div class="table-responsive">
                <table class="table table-sm align-middle">
                  <thead>
                    <tr>
                      <th style="width: 140px;">{{ $t('pos_metodo_pago') }}</th>
                      <th style="width: 140px;">{{ $t('pos_monto') }}</th>
                      <th>{{ $t('pos_referencia') }}</th>
                      <th style="width: 44px;"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(p, idx) in pagos" :key="idx">
                      <td>
                        <select v-model="p.metodo_pago" class="form-select form-select-sm">
                          <option value="">—</option>
                          <option value="efectivo">Efectivo</option>
                          <option value="tarjeta">Tarjeta</option>
                          <option value="transferencia">Transferencia</option>
                          <option value="credito">Crédito</option>
                        </select>
                      </td>
                      <td>
                        <input
                          v-model.number="p.monto"
                          type="number"
                          min="0"
                          step="0.01"
                          class="form-control form-control-sm"
                          :placeholder="p.metodo_pago === 'efectivo' ? $t('pos_monto_entregado') : $t('pos_monto')"
                        />
                      </td>
                      <td>
                        <input
                          v-if="p.metodo_pago !== 'efectivo'"
                          v-model="p.referencia_pago"
                          type="text"
                          class="form-control form-control-sm"
                          :placeholder="$t('pos_referencia')"
                        />
                        <span v-else class="text-muted small">—</span>
                      </td>
                      <td>
                        <button
                          type="button"
                          class="btn btn-sm btn-outline-danger"
                          :disabled="pagos.length === 1"
                          @click="quitarPago(idx)"
                        >×</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <button type="button" class="btn btn-sm btn-outline-primary" @click="agregarPago">{{ $t('pos_agregar_pago') }}</button>
              <div class="mt-2 small pos-resumen-pagos" :class="pagosCuadran ? 'pos-resumen-ok' : 'pos-resumen-error'">
                {{ $t('pos_suma_pagos') }}: $ {{ sumaPagos.toFixed(2) }} /
                {{ $t('pos_total') }}: $ {{ totalVenta.toFixed(2) }}
                <span v-if="!pagosCuadran"> — {{ $t('pos_debe_coincidir') }}</span>
              </div>
              <!-- Cambio a devolver (grande) cuando hay efectivo con monto_recibido > monto -->
              <div v-if="cambioTotal > 0" class="mt-3 p-3 rounded-3 pos-cambio-box">
                <div class="pos-cambio-texto text-center">
                  <span class="small text-uppercase d-block">{{ $t('pos_cambio') }}</span>
                  <span class="display-6 fw-bold">$ {{ cambioTotal.toFixed(2) }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button
              type="button"
              class="btn btn-success"
              :disabled="!puedeConfirmar || enviando"
              @click="crearVenta"
            >
              {{ enviando ? $t('pos_guardando') : $t('pos_confirmar_venta') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Vista previa de ticket (sucursal tipo venta / Villahermosa) -->
    <div
      v-if="mostrarTicketPreview && ventaParaTicket"
      class="modal d-block bg-dark bg-opacity-50"
      tabindex="-1"
      style="overflow: auto;"
    >
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header py-2">
            <h5 class="modal-title">{{ $t('pos_finalizar_venta') }} — {{ $t('pos_imprimir') }}</h5>
            <button type="button" class="btn-close" aria-label="Cerrar" @click="cerrarTicketPreview"></button>
          </div>
          <div class="modal-body p-3">
            <VentaTicketPreview :venta="ventaParaTicket" @cerrar="cerrarTicketPreview" />
          </div>
        </div>
      </div>
    </div>

    <Loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, onBeforeUnmount } from "vue";
import { useStore } from "vuex";
import { useMeta } from "@/composables/use-meta";
import { useI18n } from "vue-i18n";
import { useVentas } from "@/composables/use-ventas";
import { useCaja } from "@/composables/useCaja";
import * as ConfiguracionEmpresaRepository from "@/repositories/ConfiguracionEmpresaRepository";
import * as ProductoRepository from "@/repositories/ProductoRepository";
import Loading from "vue-loading-overlay";
import VentaTicketPreview from "@/components/VentaTicketPreview.vue";
import "vue-loading-overlay/dist/css/index.css";
import { Modal } from "bootstrap";

useMeta({ title: "Nueva Venta - POS" });
const { t: $t } = useI18n();
const store = useStore();

const PRODUCTOS_VENTA_ALMACEN = ["Polvo", "Rezaga", "Balastre"];

const sucursalActiva = ref(null);
const sucursalId = ref("");
const productos = ref([]);
const cargandoSucursal = ref(true);
const cantidades = ref({});
const detalles = ref([]);
const lastUpdatedProductId = ref(null);
let lastUpdatedTimeoutId = null;
const ventasRecientes = ref([]);
const isLoading = ref(false);
const enviando = ref(false);
const modalFinalizarRef = ref(null);
const esDonativo = ref(false);
const observacionesDonativo = ref("");
const pagos = ref([{ metodo_pago: "efectivo", monto: 0, referencia_pago: "" }]);
const mostrarTicketPreview = ref(false);
const ventaParaTicket = ref(null);

const totalVenta = computed(() => detalles.value.reduce((acc, d) => acc + d.subtotal, 0));
const {
  remanentePago,
  sumaPagos,
  pagosCuadran,
  cambioTotal,
  createVenta: createVentaApi,
  getVentas: getVentasApi,
} = useVentas({ totalVenta, pagos });

const perfilInterfaz = computed(() => store.getters.perfilInterfaz || null);
const esPerfilVenta = computed(() => !perfilInterfaz.value || perfilInterfaz.value === "VENTA");
const { cajaAbierta, loadCajaAbierta } = useCaja();

const productosFiltrados = computed(() => {
  if (!sucursalId.value) return [];
  if (perfilInterfaz.value === "VENTA_ALMACEN") {
    return productos.value.filter((p) => PRODUCTOS_VENTA_ALMACEN.includes(p.nombre));
  }
  return productos.value;
});

const observacionesRequeridasYVacias = computed(() => {
  return esDonativo.value && !String(observacionesDonativo.value || "").trim();
});

const puedeConfirmar = computed(() => {
  if (detalles.value.length === 0) return false;
  if (esDonativo.value) return !observacionesRequeridasYVacias.value;
  return pagosCuadran.value;
});

function agregarDetalle(p) {
  const cant = Number(cantidades.value[p.id]);
  if (!cant || cant <= 0) return;

  const existing = detalles.value.find((d) => d.producto_id === p.id);

  if (existing) {
    const currentQty = Number(existing.cantidad_pedida) || 0;
    const newQty = currentQty + cant;
    existing.cantidad_pedida = newQty;
    existing.subtotal = Number(existing.precio_unitario) * newQty;
  } else {
    detalles.value.push({
      producto_id: p.id,
      nombre: p.nombre,
      cantidad_pedida: cant,
      unidad_medida: typeof p.unidad_medida === "string" ? p.unidad_medida : "m³",
      precio_unitario: p.precio_unitario,
      subtotal: Number(p.precio_unitario) * cant,
    });
  }

  cantidades.value[p.id] = null;

  // Feedback visual al aumentar cantidad del producto en el carrito
  lastUpdatedProductId.value = p.id;
  if (lastUpdatedTimeoutId) {
    clearTimeout(lastUpdatedTimeoutId);
  }
  lastUpdatedTimeoutId = setTimeout(() => {
    if (lastUpdatedProductId.value === p.id) {
      lastUpdatedProductId.value = null;
    }
  }, 300);
}

function quitarDetalle(idx) {
  detalles.value.splice(idx, 1);
}

function vaciarCarrito() {
  detalles.value = [];
}

function agregarPago() {
  const restante = Number(remanentePago.value) || 0;
  pagos.value.push({ metodo_pago: "efectivo", monto: restante, referencia_pago: "" });
}

function quitarPago(idx) {
  if (pagos.value.length === 1) return;
  pagos.value.splice(idx, 1);
}

function badgeEstatus(estatus) {
  const m = { pendiente: "badge bg-warning", parcial: "badge bg-info", entregado: "badge bg-success", pagado: "badge bg-success", cancelado: "badge bg-danger" };
  return m[estatus] || "badge bg-secondary";
}

function formatearFecha(f) {
  if (!f) return "";
  return new Date(f).toLocaleString("es-MX", { dateStyle: "short", timeStyle: "short" });
}

function abrirModalFinalizar() {
  esDonativo.value = false;
  observacionesDonativo.value = "";
  const total = Number(totalVenta.value) || 0;
  pagos.value = [{ metodo_pago: "efectivo", monto: total > 0 ? total : 0, referencia_pago: "" }];
  const modal = new Modal(modalFinalizarRef.value);
  modal.show();
}

async function onSucursalChange() {
  detalles.value = [];
  cantidades.value = {};
  if (!sucursalId.value) {
    productos.value = [];
    return;
  }
  try {
    const res = await ProductoRepository.getProductos({});
    const data = res?.data ?? res;
    productos.value = Array.isArray(data) ? data : data?.data ?? [];
  } catch (e) {
    console.error(e);
    productos.value = [];
  }
}

async function crearVenta() {
  if (detalles.value.length === 0) {
    window.Swal?.fire?.({ icon: "warning", title: $t("pos_error_sin_detalles") });
    return;
  }
  if (esDonativo.value && !String(observacionesDonativo.value || "").trim()) {
    window.Swal?.fire?.({ icon: "warning", title: $t("pos_error_observaciones_donativo") });
    return;
  }
  if (!esDonativo.value && !pagosCuadran.value) {
    window.Swal?.fire?.({ icon: "warning", title: $t("pos_error_suma_pagos") });
    return;
  }
  enviando.value = true;
  const pagosLimpios = esDonativo.value
    ? []
    : pagos.value
        .filter((p) => p.metodo_pago && Number(p.monto) > 0)
        .map((p) => ({
          metodo_pago: p.metodo_pago,
          monto: Number(p.monto),
          referencia_pago: p.metodo_pago === "efectivo" ? null : (p.referencia_pago || null),
          monto_recibido: p.metodo_pago === "efectivo" ? Number(p.monto) : null,
        }));

  const payload = {
    sucursal_id: parseInt(sucursalId.value, 10),
    tipo: esDonativo.value ? "donativo" : "venta",
    es_donativo: esDonativo.value,
    observaciones: esDonativo.value ? String(observacionesDonativo.value || "").trim() : null,
    detalles: detalles.value.map((d) => ({
      producto_id: d.producto_id,
      cantidad_pedida: d.cantidad_pedida,
      precio_unitario: Number(d.precio_unitario),
    })),
    pagos: pagosLimpios,
  };

  const result = await createVentaApi(payload);
  if (result.success) {
    window.Swal?.fire?.({ icon: "success", title: result.message });
    detalles.value = [];
    const modal = Modal.getInstance(modalFinalizarRef.value);
    if (modal) modal.hide();
    await cargarVentas();
    if (sucursalActiva.value?.tipo_sucursal === "venta" && result.data) {
      ventaParaTicket.value = result.data;
      mostrarTicketPreview.value = true;
    }
  } else {
    window.Swal?.fire?.({ icon: "error", title: $t("pos_error_crear_venta"), text: result.error });
  }
  enviando.value = false;
}

function cerrarTicketPreview() {
  mostrarTicketPreview.value = false;
  ventaParaTicket.value = null;
}

async function cargarVentas() {
  try {
    const res = await getVentasApi({ per_page: 10 });
    const paginator = res?.data ?? res;
    ventasRecientes.value = Array.isArray(paginator) ? paginator : paginator?.data ?? [];
  } catch (_) {
    ventasRecientes.value = [];
  }
}

onMounted(async () => {
  isLoading.value = true;
  cargandoSucursal.value = true;
  try {
    const [sucursalRes] = await Promise.all([
      ConfiguracionEmpresaRepository.getSucursal(),
      cargarVentas(),
      loadCajaAbierta(),
    ]);
    const suc = sucursalRes?.data ?? sucursalRes;
    if (suc?.id) {
      sucursalActiva.value = suc;
      sucursalId.value = suc.id;
      onSucursalChange();
    }
  } catch (_) {
    sucursalActiva.value = null;
  } finally {
    cargandoSucursal.value = false;
    isLoading.value = false;
  }
});

watch(sucursalId, (val) => {
  if (val) onSucursalChange();
});

watch(esDonativo, (esDon) => {
  if (esDon) {
    pagos.value = [];
  } else if (pagos.value.length === 0) {
    const total = Number(totalVenta.value) || 0;
    pagos.value = [{ metodo_pago: "efectivo", monto: total > 0 ? total : 0, referencia_pago: "" }];
  }
});

onBeforeUnmount(() => {
  if (lastUpdatedTimeoutId) {
    clearTimeout(lastUpdatedTimeoutId);
    lastUpdatedTimeoutId = null;
  }
});
</script>

<style scoped>
.pos-product-card {
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.pos-product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}
.pos-product-card-bump {
  animation: pos-bump 0.3s ease;
}
@keyframes pos-bump {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.03);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15);
  }
  100% {
    transform: scale(1);
  }
}
.pos-cart-card {
  border-radius: 12px;
}
.pos-cart-card .list-group-item {
  border-left: 0;
  border-right: 0;
}
.sticky-top {
  top: 1rem;
}

/* Resumen pagos: solo color de texto, sin fondo verde/rojo */
.pos-resumen-pagos {
  background: transparent;
}
.pos-resumen-ok {
  color: var(--bs-success, #198754);
}
.pos-resumen-error {
  color: var(--bs-danger, #dc3545);
}

/* Cambio a devolver: fondo claro y texto oscuro para que siempre se lea */
.pos-cambio-box {
  background-color: #e8f5e9;
  border: 1px solid #198754;
}
.pos-cambio-texto {
  color: #0d5c2e;
}

/* Precio solo lectura: no editable, valor siempre del catálogo */
.pos-precio-solo-lectura {
  user-select: none;
}
</style>
