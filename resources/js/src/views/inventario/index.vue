<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb">
      <ul class="navbar-nav flex-row">
        <li>
          <div class="page-header">
            <nav class="breadcrumb-one" aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:;">Inventario</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>Gestión de inventario</span></li>
              </ol>
            </nav>
          </div>
        </li>
      </ul>
    </teleport>

    <div class="row layout-top-spacing">
      <div class="col-12" v-if="perfilInterfaz !== 'VENTA_ALMACEN'">
        <div class="alert alert-info">
          Este módulo solo está disponible para sucursales tipo almacén (ej. Macuspana).
        </div>
      </div>

      <div v-else class="col-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
          <div class="d-flex flex-wrap align-items-center gap-2">
            <input
              v-model="filters.search"
              type="text"
              class="form-control"
              placeholder="Buscar producto..."
              style="max-width: 260px;"
              @keyup.enter="loadInventory"
            />
            <button type="button" class="btn btn-outline-primary btn-sm" @click="loadInventory">
              Buscar
            </button>
          </div>
          <div class="text-end">
            <div class="fw-bold">Valor total inventario:</div>
            <div class="fs-5 text-primary">
              $ {{ formatMoney(totalInventoryValue) }}
            </div>
          </div>
        </div>

        <div class="widget widget-table-one">
          <div class="widget-heading">
            <h5>Existencias por producto</h5>
          </div>
          <div class="widget-content">
            <div v-if="loading" class="text-center py-5">Cargando inventario...</div>
            <div v-else-if="!items.length" class="text-center text-muted py-5">
              No hay productos registrados.
            </div>
            <div v-else class="table-responsive">
              <table class="table table-sm table-hover align-middle">
                <thead>
                  <tr>
                    <th>Producto</th>
                    <th class="text-end">Stock actual</th>
                    <th class="text-end">Stock mínimo</th>
                    <th>Unidad</th>
                    <th class="text-end">Precio unitario</th>
                    <th class="text-end">Valor</th>
                    <th class="text-end" style="width: 120px;" v-if="canAdjust">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="p in items"
                    :key="p.id"
                    :class="rowClass(p)"
                  >
                    <td>
                      <div class="d-flex flex-column">
                        <span>{{ p.nombre }}</span>
                        <button
                          type="button"
                          class="btn btn-link btn-sm p-0 align-self-start"
                          @click="openHistoryModal(p)"
                        >
                          Ver historial
                        </button>
                      </div>
                    </td>
                    <td class="text-end">
                      {{ Number(p.stock_actual).toFixed(2) }}
                    </td>
                    <td class="text-end">
                      {{ Number(p.stock_minimo || 0).toFixed(2) }}
                    </td>
                    <td>{{ p.unidad_medida || 'm³' }}</td>
                    <td class="text-end">$ {{ Number(p.precio_unitario).toFixed(2) }}</td>
                    <td class="text-end">$ {{ Number(p.valor_inventario).toFixed(2) }}</td>
                    <td class="text-end" v-if="canAdjust">
                      <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary"
                        @click="openAdjustModal(p)"
                      >
                        Ajustar
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de ajuste de stock -->
    <div
      class="modal fade"
      tabindex="-1"
      aria-hidden="true"
      ref="adjustModalRef"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Ajustar inventario
              <span v-if="adjustForm.product" class="text-muted">— {{ adjustForm.product.nombre }}</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <div v-if="adjustForm.product" class="mb-3 small text-muted">
              Stock actual: {{ Number(adjustForm.product.stock_actual).toFixed(2) }} {{ adjustForm.product.unidad_medida || 'm³' }}
            </div>
            <div class="mb-3">
              <label class="form-label">Tipo de ajuste</label>
              <select v-model="adjustForm.type" class="form-select">
                <option value="incremento">Incrementar</option>
                <option value="decremento">Decrementar</option>
                <option value="establecer">Establecer valor exacto</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Cantidad</label>
              <input
                v-model.number="adjustForm.value"
                type="number"
                min="0"
                step="0.01"
                class="form-control"
              />
            </div>
            <div class="mb-0">
              <label class="form-label">Observación (obligatoria)</label>
              <textarea
                v-model="adjustForm.reason"
                class="form-control"
                rows="2"
                placeholder="Ej. Ajuste manual por conteo físico, corrección de carga, etc."
              />
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
              :disabled="saving"
            >
              Cerrar
            </button>
            <button
              type="button"
              class="btn btn-primary"
              :disabled="saving || !canSubmitAdjust"
              @click="submitAdjust"
            >
              {{ saving ? "Guardando..." : "Guardar ajuste" }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal historial de movimientos -->
    <div
      class="modal fade"
      tabindex="-1"
      aria-hidden="true"
      ref="historyModalRef"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Historial de movimientos
              <span v-if="historyProduct" class="text-muted">— {{ historyProduct.nombre }}</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <div v-if="historyLoading" class="text-center py-4">
              Cargando movimientos...
            </div>
            <div v-else-if="!movements.length" class="text-center text-muted py-4">
              No hay movimientos registrados para este producto.
            </div>
            <div v-else class="table-responsive">
              <table class="table table-sm table-striped align-middle mb-0">
                <thead>
                  <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th class="text-end">Cantidad</th>
                    <th class="text-end">Stock antes</th>
                    <th class="text-end">Stock después</th>
                    <th>Motivo</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="m in movements" :key="m.id">
                    <td>{{ formatDate(m.created_at) }}</td>
                    <td>{{ m.tipo }}</td>
                    <td class="text-end">{{ Number(m.cantidad).toFixed(2) }}</td>
                    <td class="text-end">{{ Number(m.stock_anterior || 0).toFixed(2) }}</td>
                    <td class="text-end">{{ Number(m.stock_nuevo || 0).toFixed(2) }}</td>
                    <td>{{ m.motivo }}</td>
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
import { ref, computed, onMounted } from "vue";
import { useStore } from "vuex";
import { Modal } from "bootstrap";
import { useMeta } from "@/composables/use-meta";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
import * as InventarioRepository from "@/repositories/InventarioRepository";

useMeta({ title: "Gestión de inventario" });

const store = useStore();
const perfilInterfaz = computed(() => store.getters.perfilInterfaz || null);

const loading = ref(false);
const items = ref([]);
const filters = ref({ search: "" });
const saving = ref(false);
const adjustModalRef = ref(null);
let adjustModalInstance = null;
const historyModalRef = ref(null);
let historyModalInstance = null;
const historyProduct = ref(null);
const historyLoading = ref(false);
const movements = ref([]);

const adjustForm = ref({
  product: null,
  type: "incremento",
  value: 0,
  reason: "",
});

const canAdjust = computed(() => {
  try {
    const perms = store.getters?.permissions || [];
    return perms.includes("inventario.ajustar");
  } catch {
    return false;
  }
});

const canSubmitAdjust = computed(() => {
  if (!adjustForm.value.product) return false;
  if (!adjustForm.value.reason || !String(adjustForm.value.reason).trim()) return false;
  const val = Number(adjustForm.value.value || adjustForm.value);
  return val >= 0;
});

const totalInventoryValue = computed(() =>
  items.value.reduce((acc, p) => acc + (Number(p.valor_inventario) || 0), 0)
);

function formatMoney(n) {
  return Number(n || 0).toLocaleString("es-MX", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
}

function formatDate(value) {
  if (!value) return "";
  const d = new Date(value);
  return d.toLocaleString("es-MX", {
    dateStyle: "short",
    timeStyle: "short",
  });
}

function rowClass(p) {
  const stock = Number(p.stock_actual);
  const min = Number(p.stock_minimo || 0);
  if (min > 0 && stock < min) {
    return "table-danger";
  }
  return "";
}

async function loadInventory() {
  loading.value = true;
  try {
    const res = await InventarioRepository.getInventario({
      q: filters.value.search || undefined,
    });
    const data = res?.data ?? res ?? [];
    items.value = Array.isArray(data) ? data : data.data ?? [];
  } catch (e) {
    console.error(e);
    items.value = [];
  } finally {
    loading.value = false;
  }
}

function openAdjustModal(product) {
  if (!canAdjust.value) return;
  adjustForm.value.product = product;
  adjustForm.value.type = "incremento";
  adjustForm.value.value = 0;
  adjustForm.value.reason = "";
  if (!adjustModalInstance && adjustModalRef.value) {
    adjustModalInstance = new Modal(adjustModalRef.value);
  }
  adjustModalInstance?.show();
}

async function openHistoryModal(product) {
  historyProduct.value = product;
  movements.value = [];
  historyLoading.value = true;
  if (!historyModalInstance && historyModalRef.value) {
    historyModalInstance = new Modal(historyModalRef.value);
  }
  historyModalInstance?.show();
  try {
    const res = await InventarioRepository.getMovements({
      producto_id: product.id,
    });
    const data = res?.data ?? res ?? [];
    movements.value = Array.isArray(data) ? data : data.data ?? [];
  } catch (e) {
    console.error(e);
    movements.value = [];
  } finally {
    historyLoading.value = false;
  }
}

async function submitAdjust() {
  if (!canSubmitAdjust.value || !adjustForm.value.product) return;
  try {
    saving.value = true;
    const payload = {
      producto_id: adjustForm.value.product.id,
      tipo: adjustForm.value.type,
      valor: Number(adjustForm.value.value || 0),
      reason: String(adjustForm.value.reason || "").trim(),
    };
    const res = await InventarioRepository.ajustarStock(payload);
    const msg = res?.message || "Inventario ajustado correctamente.";
    window.Swal?.fire?.({ icon: "success", title: msg });
    adjustModalInstance?.hide();
    await loadInventory();
  } catch (e) {
    const msg =
      e.response?.data?.errors
        ? Object.values(e.response.data.errors).flat().join(" ")
        : e.message;
    window.Swal?.fire?.({
      icon: "error",
      title: "Error al ajustar inventario",
      text: msg,
    });
  } finally {
    saving.value = false;
  }
}

onMounted(async () => {
  if (perfilInterfaz.value === "VENTA_ALMACEN") {
    await loadInventory();
  }
});
</script>

<style scoped>
.table-danger {
  --bs-table-bg: #fdecea;
}
</style>

