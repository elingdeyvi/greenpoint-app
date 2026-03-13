<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb">
      <ul class="navbar-nav flex-row">
        <li>
          <div class="page-header">
            <nav class="breadcrumb-one" aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item">
                  <router-link to="/cajas">Caja</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                  <span>Validación de pedidos (Oficina)</span>
                </li>
              </ol>
            </nav>
          </div>
        </li>
      </ul>
    </teleport>

    <div class="row layout-top-spacing">
      <div class="col-xl-12 layout-spacing">
        <div class="panel p-3 space-y-3">
          <h3 class="titulo-dorado mb-2">Validación de pedidos — Oficina Central</h3>
          <p class="text-muted mb-3">
            Flujo de <strong>cobro en oficina</strong> para pedidos generados en caseta (estatus pendiente_pago) en sucursal tipo
            <strong>venta_almacen</strong>.
          </p>

          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Buscar por folio</label>
              <input
                v-model="filtros.folio"
                type="text"
                class="form-control"
                placeholder="Folio del pedido"
                @keyup.enter="cargarPedidos"
              />
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="button" class="btn btn-outline-primary me-2" @click="cargarPedidos">
                Refrescar
              </button>
            </div>
          </div>

          <div class="row flex flex-col lg:flex-row gap-3">
            <div class="col-md-6 w-full lg:w-1/2">
              <h5 class="mb-2">Pedidos pendientes de pago</h5>
              <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                <table class="table table-sm table-hover align-middle">
                  <thead>
                    <tr>
                      <th>Folio</th>
                      <th>Total</th>
                      <th>Estatus</th>
                      <th>Fecha</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="v in pedidos"
                      :key="v.id"
                      :class="selectedVenta && selectedVenta.id === v.id ? 'table-active' : ''"
                      @click="seleccionarVenta(v)"
                      style="cursor: pointer"
                    >
                      <td>{{ v.folio }}</td>
                      <td>$ {{ Number(v.total).toFixed(2) }}</td>
                      <td><span class="badge bg-warning">Pendiente pago</span></td>
                      <td>{{ formatearFecha(v.created_at) }}</td>
                    </tr>
                    <tr v-if="pedidos.length === 0">
                      <td colspan="4" class="text-center text-muted py-3">
                        No hay pedidos pendientes de pago.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="col-md-6 w-full lg:w-1/2" v-if="selectedVenta">
              <h5 class="mb-2">Detalle del pedido</h5>
              <p class="mb-1">
                <strong>Folio:</strong> {{ selectedVenta.folio }}
              </p>
              <p class="mb-1">
                <strong>Total:</strong> $ {{ Number(selectedVenta.total).toFixed(2) }}
              </p>
              <p class="mb-2">
                <strong>Estatus actual:</strong> {{ selectedVenta.estatus }}
              </p>

              <h6>Productos</h6>
              <ul class="list-group mb-3">
                <li
                  v-for="d in selectedVenta.detalles || []"
                  :key="d.id"
                  class="list-group-item d-flex justify-content-between align-items-center"
                >
                  <span>{{ d.producto?.nombre || d.producto_id }} — {{ d.cantidad_pedida }}</span>
                </li>
                <li v-if="!selectedVenta.detalles || selectedVenta.detalles.length === 0" class="list-group-item text-muted">
                  Sin detalles registrados.
                </li>
              </ul>

              <!-- Pagos -->
              <h6 class="mb-2">Pagos</h6>
              <div class="table-responsive">
                <table class="table table-sm align-middle mb-1">
                  <thead>
                    <tr>
                      <th style="width: 150px;">Método</th>
                      <th style="width: 140px;">Monto</th>
                      <th>Referencia</th>
                      <th style="width: 40px;"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(p, idx) in pagos" :key="idx">
                      <td>
                        <select v-model="p.metodo_pago" class="form-select form-select-sm">
                          <option disabled value="">Seleccione</option>
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
                        />
                      </td>
                      <td>
                        <input
                          v-model="p.referencia_pago"
                          type="text"
                          class="form-control form-control-sm"
                          placeholder="Folio, banco, nota..."
                        />
                      </td>
                      <td>
                        <button
                          type="button"
                          class="btn btn-sm btn-outline-danger"
                          @click="quitarPago(idx)"
                          :disabled="pagos.length === 1"
                        >
                          ×
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <button type="button" class="btn btn-sm btn-outline-primary mb-2" @click="agregarPago">
                Agregar línea de pago
              </button>

              <div class="mb-2 small validar-pagos-resumen">
                <span :class="totalPagosOK ? 'validar-pagos-ok' : 'validar-pagos-error'">
                  Pagado: $ {{ totalPagos.toFixed(2) }} / Total venta: $ {{ Number(selectedVenta.total).toFixed(2) }}
                  <span v-if="!totalPagosOK"> — La suma de pagos debe coincidir con el total.</span>
                </span>
              </div>

              <button
                type="button"
                class="btn btn-success"
                :disabled="enviando || !totalPagosOK"
                @click="confirmarPagos"
              >
                {{ enviando ? "Registrando pagos..." : "Confirmar cobro y generar ticket" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useMeta } from "@/composables/use-meta";
import * as VentaRepository from "@/repositories/VentaRepository";

useMeta({ title: "Validación de pedidos" });

const pedidos = ref([]);
const selectedVenta = ref(null);
const filtros = ref({ folio: "" });
const pagos = ref([{ metodo_pago: "efectivo", monto: 0, referencia_pago: "" }]);
const enviando = ref(false);

const totalPagos = computed(() =>
  pagos.value.reduce((acc, p) => acc + (Number(p.monto) || 0), 0)
);

const totalPagosOK = computed(() => {
  if (!selectedVenta.value) return false;
  const tv = Number((selectedVenta.value.total || 0).toFixed(2));
  const tp = Number(totalPagos.value.toFixed(2));
  return tv > 0 && tv === tp;
});

function formatearFecha(f) {
  if (!f) return "";
  return new Date(f).toLocaleString("es-MX", { dateStyle: "short", timeStyle: "short" });
}

async function cargarPedidos() {
  try {
    const res = await VentaRepository.getPedidosPendientesPago({
      folio: filtros.value.folio || undefined,
    });
    const data = res.data || res;
    pedidos.value = data?.data || [];
    if (selectedVenta.value) {
      const refreshed = pedidos.value.find((v) => v.id === selectedVenta.value.id);
      if (refreshed) {
        seleccionarVenta(refreshed);
      } else {
        selectedVenta.value = null;
      }
    }
  } catch (e) {
    pedidos.value = [];
  }
}

function seleccionarVenta(v) {
  selectedVenta.value = v;
  const existentes = Array.isArray(v.pagos) ? v.pagos : [];
  if (existentes.length) {
    pagos.value = existentes.map((p) => ({
      metodo_pago: p.metodo_pago,
      monto: Number(p.monto),
      referencia_pago: p.referencia_pago || "",
    }));
  } else {
    pagos.value = [{ metodo_pago: "efectivo", monto: Number(v.total) || 0, referencia_pago: "" }];
  }
}

function agregarPago() {
  pagos.value.push({ metodo_pago: "efectivo", monto: 0, referencia_pago: "" });
}

function quitarPago(idx) {
  if (pagos.value.length === 1) return;
  pagos.value.splice(idx, 1);
}

async function confirmarPagos() {
  if (!selectedVenta.value) return;
  if (!totalPagosOK.value) {
    window.Swal?.fire?.({
      icon: "warning",
      title: "Pagos incompletos",
      text: "La suma de los pagos debe coincidir con el total de la venta.",
    });
    return;
  }

  try {
    enviando.value = true;
    const payload = {
      pagos: pagos.value
        .filter((p) => p.metodo_pago && Number(p.monto) > 0)
        .map((p) => ({
          metodo_pago: p.metodo_pago,
          monto: Number(p.monto),
          referencia_pago: p.referencia_pago || null,
        })),
    };
    const res = await VentaRepository.registrarPagosVenta(selectedVenta.value.id, payload);
    const data = res.data || res;
    window.Swal?.fire?.({
      icon: "success",
      title: "Cobro registrado",
      text: data.message || "Pagos registrados y ticket generado.",
    });
    await cargarPedidos();
  } catch (e) {
    const msg = e.response?.data?.errors
      ? Object.values(e.response.data.errors).flat().join(" ")
      : e.message;
    window.Swal?.fire?.({
      icon: "error",
      title: "Error al registrar pagos",
      text: msg,
    });
  } finally {
    enviando.value = false;
  }
}

onMounted(() => {
  cargarPedidos();
});
</script>

<style scoped>
.validar-pagos-resumen {
  background: transparent;
}
.validar-pagos-ok {
  color: var(--bs-success, #198754);
}
.validar-pagos-error {
  color: var(--bs-danger, #dc3545);
}
</style>
