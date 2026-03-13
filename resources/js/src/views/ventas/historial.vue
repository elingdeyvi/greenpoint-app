<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb">
      <ul class="navbar-nav flex-row">
        <li>
          <div class="page-header">
            <nav class="breadcrumb-one" aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:;">{{ $t('sales') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $t('pos_historial_ventas') }}</li>
              </ol>
            </nav>
          </div>
        </li>
      </ul>
    </teleport>

    <div class="row">
      <div class="col-12">
        <h3 class="titulo-dorado mb-3">{{ $t('pos_historial_ventas') }}</h3>
        <p class="text-muted small">{{ $t('pos_historial_del_dia') }}</p>
      </div>
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div v-if="cargando" class="text-center py-5">
              <span class="spinner-border text-primary" role="status"></span>
            </div>
            <div v-else class="table-responsive">
              <table class="table table-hover table-sm">
                <thead>
                  <tr>
                    <th>{{ $t('pos_folio') }}</th>
                    <th>{{ $t('pos_cliente') }}</th>
                    <th class="text-end">{{ $t('pos_total') }}</th>
                    <th v-if="!esPerfilVenta">{{ $t('pos_estatus') }}</th>
                    <th class="text-end" style="width: 180px;">{{ $t('pos_acciones') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="v in ventas" :key="v.id">
                    <td>{{ v.folio }}</td>
                    <td>{{ nombreCliente(v) }}</td>
                    <td class="text-end">$ {{ Number(v.total).toFixed(2) }}</td>
                    <td v-if="!esPerfilVenta"><span :class="badgeEstatus(v.estatus)">{{ v.estatus }}</span></td>
                    <td class="text-end">
                      <button
                        type="button"
                        class="btn btn-sm btn-outline-primary me-1"
                        :disabled="imprimiendoId === v.id"
                        @click="abrirTicket(v.id)"
                      >
                        {{ imprimiendoId === v.id ? $t('pos_cargando') : $t('pos_imprimir_ticket') }}
                      </button>
                      <button
                        v-if="puedeCancelar(v) && puedeCancelarPermiso"
                        type="button"
                        class="btn btn-sm btn-outline-danger"
                        :disabled="cancelandoId === v.id"
                        @click="confirmarCancelar(v)"
                      >
                        {{ cancelandoId === v.id ? $t('pos_cargando') : $t('pos_cancelar_venta') }}
                      </button>
                    </td>
                  </tr>
                  <tr v-if="ventas.length === 0 && !cargando">
                    <td :colspan="esPerfilVenta ? 4 : 5" class="text-center text-muted py-4">{{ $t('pos_sin_ventas_hoy') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal vista previa ticket para reimpresión -->
    <div
      v-if="mostrarTicket && ventaParaTicket"
      class="modal d-block bg-dark bg-opacity-50"
      tabindex="-1"
      style="overflow: auto;"
    >
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header py-2">
            <h5 class="modal-title">{{ $t('pos_imprimir_ticket') }}</h5>
            <button type="button" class="btn-close" aria-label="Cerrar" @click="cerrarTicket"></button>
          </div>
          <div class="modal-body p-3">
            <VentaTicketPreview :venta="ventaParaTicket" @cerrar="cerrarTicket" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useStore } from "vuex";
import { useMeta } from "@/composables/use-meta";
import { useI18n } from "vue-i18n";
import { useVentas } from "@/composables/use-ventas";
import * as ConfiguracionEmpresaRepository from "@/repositories/ConfiguracionEmpresaRepository";
import VentaTicketPreview from "@/components/VentaTicketPreview.vue";

useMeta({ title: "Historial de ventas" });
const { t: $t } = useI18n();
const store = useStore();

const ventas = ref([]);
const cargando = ref(true);
const mostrarTicket = ref(false);
const ventaParaTicket = ref(null);
const imprimiendoId = ref(null);
const cancelandoId = ref(null);

// Usa el composable para mantener el patrón de repositorio (getVentas/getVenta vía useVentas)
const { getVentas, getVenta, cancelarVenta } = useVentas();
const perfilInterfaz = computed(() => store.getters.perfilInterfaz || null);
const esPerfilVenta = computed(() => !perfilInterfaz.value || perfilInterfaz.value === "VENTA");

function puedeCancelar(venta) {
  return venta && ["pendiente", "parcial"].includes(venta.estatus);
}
function puedeCancelarPermiso() {
  try {
    return store.getters?.permissions?.includes("ventas.cancelar") ?? false;
  } catch {
    return false;
  }
}

function nombreCliente(v) {
  if (v.cliente?.nombre) return v.cliente.nombre;
  if (v.cliente?.razon_social) return v.cliente.razon_social;
  return "—";
}

function badgeEstatus(estatus) {
  const m = {
    pendiente: "badge bg-warning",
    parcial: "badge bg-info",
    entregado: "badge bg-success",
    pagado: "badge bg-success",
    pendiente_pago: "badge bg-info",
    cancelado: "badge bg-danger",
  };
  return m[estatus] || "badge bg-secondary";
}

function fechaHoy() {
  const d = new Date();
  return d.toISOString().slice(0, 10);
}

async function cargarVentas() {
  cargando.value = true;
  try {
    const hoy = fechaHoy();
    const res = await getVentas({ fecha_desde: hoy, fecha_hasta: hoy, per_page: 100 });
    const data = res?.data ?? res;
    ventas.value = Array.isArray(data) ? data : data?.data ?? [];
  } catch (_) {
    ventas.value = [];
  } finally {
    cargando.value = false;
  }
}

async function abrirTicket(ventaId) {
  imprimiendoId.value = ventaId;
  try {
    const res = await getVenta(ventaId);
    const data = res?.data ?? res;
    if (data?.id) {
      ventaParaTicket.value = data;
      mostrarTicket.value = true;
    }
  } catch (_) {
    window.Swal?.fire?.({ icon: "error", title: $t("pos_error_crear_venta") });
  } finally {
    imprimiendoId.value = null;
  }
}

function cerrarTicket() {
  mostrarTicket.value = false;
  ventaParaTicket.value = null;
}

async function confirmarCancelar(venta) {
  const ok = await window.Swal?.fire?.({
    icon: "warning",
    title: $t("pos_cancelar_venta"),
    text: `¿Cancelar venta ${venta.folio}?${esPerfilVenta.value ? "" : " Se repondrá el stock."}`,
    showCancelButton: true,
    confirmButtonText: "Sí, cancelar",
    cancelButtonText: "No",
  });
  if (!ok?.isConfirmed) return;
  cancelandoId.value = venta.id;
  try {
    const result = await cancelarVenta(venta.id);
    if (result.success) {
      window.Swal?.fire?.({ icon: "success", title: result.message });
      await cargarVentas();
    } else {
      window.Swal?.fire?.({ icon: "error", title: $t("pos_error_cancelar_venta"), text: result.error });
    }
  } finally {
    cancelandoId.value = null;
  }
}

onMounted(() => {
  if (perfilInterfaz.value !== "VENTA") {
    return;
  }
  cargarVentas();
});
</script>
