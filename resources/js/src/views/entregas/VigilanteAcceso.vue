<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb">
      <ul class="navbar-nav flex-row">
        <li>
          <div class="page-header">
            <nav class="breadcrumb-one" aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item">
                  <router-link to="/entregas">Entregas</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                  <span>Control de Acceso Vigilante</span>
                </li>
              </ol>
            </nav>
          </div>
        </li>
      </ul>
    </teleport>

    <div class="row layout-top-spacing">
      <div class="col-xl-10 offset-xl-1 layout-spacing">
        <div class="panel p-4">
          <h3 class="titulo-dorado mb-2">{{ $t('caseta_titulo') }}</h3>
          <p class="text-muted mb-3">
            {{ $t('caseta_subtitulo') }}
          </p>

          <div v-if="cargandoSucursal" class="alert alert-info py-2">
            Cargando configuración de sucursal...
          </div>
          <div v-else-if="!sucursalActual" class="alert alert-warning">
            Configure la sucursal en <strong>Configuración de la empresa</strong> para habilitar este módulo.
          </div>
          <div v-else-if="!esMacuspana" class="alert alert-info">
            Este módulo solo está disponible en sucursales tipo almacén (Macuspana). Tipo actual:
            <strong>{{ sucursalActual.tipo_sucursal }}</strong>
          </div>
          <div v-if="sucursalActual && esMacuspana" class="mb-3">
            <span class="badge bg-primary fs-6">
              Sucursal actual: {{ sucursalActual.nombre }}
            </span>
          </div>

          <div
            v-if="sucursalActual && esMacuspana"
            class="mb-4 d-flex gap-2 flex-wrap"
          >
            <button
              type="button"
              class="btn"
              :class="modo === 'generar' ? 'btn-primary' : 'btn-outline-primary'"
              @click="modo = 'generar'"
            >
              {{ $t('caseta_generar_qr_local') }}
            </button>
            <button
              type="button"
              class="btn"
              :class="modo === 'escanear' ? 'btn-primary' : 'btn-outline-primary'"
              @click="modo = 'escanear'"
            >
              {{ $t('caseta_escanear_qr') }}
            </button>
          </div>

          <!-- Flujo: Generar QR local -->
          <div
            v-if="sucursalActual && esMacuspana && modo === 'generar'"
            class="card mb-4"
          >
            <div class="card-body">
              <h5 class="card-title mb-3">{{ $t('caseta_card_generar_titulo') }}</h5>
              <p class="text-muted">
                {{ $t('caseta_card_generar_desc') }}
              </p>
              <p v-if="perfilInterfaz === 'VENTA_ALMACEN'" class="small text-info mb-2">
                {{ $t('caseta_productos_permitidos_info') }}
              </p>

              <div class="row g-3">
                <div class="col-md-5">
                  <label class="form-label">{{ $t('caseta_pedido') }}</label>
                  <select
                    v-model="formGenerar.venta_id"
                    class="form-select"
                  >
                    <option value="">{{ $t('caseta_crear_nuevo_pedido') }}</option>
                    <option
                      v-for="v in pedidos"
                      :key="v.id"
                      :value="v.id"
                    >
                      {{ v.folio }}
                      <template v-if="v.viajes_permitidos != null">
                        ({{ v.viajes_usados ?? 0 }}/{{ v.viajes_permitidos }} viajes)
                      </template>
                    </option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">{{ $t('caseta_numero_viajes_pedido') }}</label>
                  <input
                    v-model.number="formGenerar.numero_viajes"
                    type="number"
                    min="1"
                    class="form-control"
                    :placeholder="viajesPlaceholder"
                  />
                  <small v-if="pedidoSeleccionado && pedidoSeleccionado.viajes_permitidos != null" class="text-muted">
                    Pedido: {{ pedidoSeleccionado.viajes_usados ?? 0 }} / {{ pedidoSeleccionado.viajes_permitidos }} usados
                  </small>
                </div>
                <div class="col-md-4">
                  <label class="form-label">{{ $t('caseta_producto') }}</label>
                  <select
                    v-model="formGenerar.producto_id"
                    class="form-select"
                  >
                    <option value="" disabled>{{ $t('caseta_seleccione_producto') }}</option>
                    <option
                      v-for="prod in productosParaGenerar"
                      :key="prod.id"
                      :value="prod.id"
                    >
                      {{ prod.nombre }}
                    </option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">{{ $t('caseta_foto_vehiculo') }}</label>
                  <input
                    type="file"
                    class="form-control"
                    accept="image/*"
                    @change="onFotoGenerarChange"
                  />
                </div>
              </div>

              <div class="mt-3">
                <button
                  type="button"
                  class="btn btn-success min-h-[44px] min-w-[44px]"
                  :disabled="enviandoGenerar"
                  @click="generarQrLocal"
                >
                  <span v-if="!enviandoGenerar">{{ $t('caseta_boton_generar_qr') }}</span>
                  <span v-else>{{ $t('caseta_generando') }}</span>
                </button>
              </div>

              <div
                v-if="resultadoGenerar"
                class="mt-4 p-3 border rounded bg-light"
              >
                <h6 class="mb-2">{{ $t('caseta_qr_generado') }}</h6>
                <p class="mb-1">
                  <strong>Pedido:</strong>
                  {{ resultadoGenerar.folio ?? '—' }}
                  <template v-if="resultadoGenerar.viajes_permitidos != null">
                    ({{ resultadoGenerar.viajes_usados ?? 0 }}/{{ resultadoGenerar.viajes_permitidos }} viajes)
                  </template>
                </p>
                <p class="mb-1">
                  <strong>UUID:</strong>
                  <span class="font-monospace">{{ resultadoGenerar.uuid }}</span>
                </p>
                <p class="mb-2">
                  <strong>{{ $t('caseta_payload_plano') }}</strong>
                </p>
                <pre class="small bg-white p-2 border rounded mb-0" style="white-space: pre-wrap;">
{{ resultadoGenerar.qr_payload }}
                </pre>
                <p class="mt-2 small text-muted">
                  Use esta cadena en un generador de QR. El lector debe enviar exactamente este texto al escanear.
                </p>
              </div>
            </div>
          </div>

          <!-- Flujo: Escanear QR -->
          <div
            v-if="sucursalActual && esMacuspana && modo === 'escanear'"
            class="card"
          >
            <div class="card-body">
              <h5 class="card-title mb-3">{{ $t('caseta_card_escanear_titulo') }}</h5>
              <p class="text-muted">
                {{ $t('caseta_card_escanear_desc') }}
              </p>

              <div
                class="border rounded p-4 text-center mb-3 min-h-[120px] flex flex-col items-center justify-center cursor-pointer"
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
                <p class="mb-2 text-muted">
                  <span v-if="escaneoActivo">Listo — escanee el código QR ahora.</span>
                  <span v-else>Haga clic aquí y luego escanee el código QR.</span>
                </p>
                <small class="text-muted">
                  El texto del QR no se muestra ni se edita manualmente.
                </small>
              </div>

              <div v-if="estadoQr" class="mb-3">
                <p class="mb-1">
                  <strong>UUID:</strong>
                  <span class="font-monospace">{{ estadoQr.uuid }}</span>
                </p>
                <p class="mb-1">
                  <strong>Producto:</strong>
                  {{ estadoQr.producto_nombre ?? estadoQr.producto_id }}
                </p>
                <p class="mb-1">
                  <strong>Cantidad autorizada por viaje:</strong>
                  {{ estadoQr.cantidad }}
                </p>
                <p class="mb-1">
                  <strong>Viajes:</strong>
                  {{ estadoQr.viaje_actual }} de {{ estadoQr.viajes_permitidos }}
                </p>
              </div>

              <div
                v-if="estadoQr && !estadoQr.agotado"
                class="mt-3 border-top pt-3"
              >
                <h6 class="mb-2">{{ $t('caseta_evidencia_ingreso') }}</h6>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label">{{ $t('caseta_cantidad_viaje') }}</label>
                    <input
                      v-model.number="estadoQr.cantidad_viaje"
                      type="number"
                      min="0.01"
                      step="0.01"
                      class="form-control"
                    />
                  </div>
                  <div class="col-md-5">
                    <label class="form-label">{{ $t('caseta_foto_vehiculo') }}</label>
                    <input
                      type="file"
                      class="form-control"
                      accept="image/*"
                      @change="onFotoAccesoChange"
                    />
                  </div>
                  <div class="col-md-3 d-flex align-items-end">
                    <button
                      type="button"
                    class="btn btn-success w-100 min-h-[44px] min-w-[44px]"
                      :disabled="enviandoAcceso"
                      @click="registrarAcceso"
                    >
                      <span v-if="!enviandoAcceso">{{ $t('caseta_registrar_acceso') }}</span>
                      <span v-else>{{ $t('caseta_registrando') }}</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <loading
      v-model:active="isLoading"
      :can-cancel="false"
      :is-full-page="true"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useStore } from "vuex";
import { useMeta } from "@/composables/use-meta";
import { useI18n } from "vue-i18n";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";

import * as ConfiguracionEmpresaRepository from "@/repositories/ConfiguracionEmpresaRepository";
import * as ProductoRepository from "@/repositories/ProductoRepository";
import * as VentaRepository from "@/repositories/VentaRepository";
import * as VigilanteRepository from "@/repositories/VigilanteRepository";
import * as VigilanteAccesoRepository from "@/repositories/VigilanteAccesoRepository";

useMeta({ title: "Control de Acceso Vigilante" });
const { t: $t } = useI18n();
const store = useStore();

const sucursalActual = ref(null);
const cargandoSucursal = ref(true);
const esMacuspana = computed(
  () => sucursalActual.value?.tipo_sucursal === "venta_almacen"
);

/** Perfil de interfaz (VENTA | VENTA_ALMACEN). Para filtrar productos solo en generación local. */
const perfilInterfaz = computed(() => store.getters.perfilInterfaz || null);

/** Nombres permitidos para generar QR local cuando la sucursal es venta_almacen. */
const PRODUCTOS_PERMITIDOS_CASETA = ["Polvo", "Rezaga", "Balastre"];

/** Productos para el selector de "Generar QR local". En VENTA_ALMACEN solo Polvo, Rezaga, Balastre. */
const productosParaGenerar = computed(() => {
  const lista = productos.value;
  if (perfilInterfaz.value === "VENTA_ALMACEN") {
    return lista.filter((p) => PRODUCTOS_PERMITIDOS_CASETA.includes(p.nombre));
  }
  return lista;
});

// Si el producto seleccionado no está en la lista filtrada (ej. cambio de perfil), limpiar selección.
watch(
  [productosParaGenerar, () => formGenerar.value.producto_id],
  () => {
    const ids = productosParaGenerar.value.map((p) => String(p.id));
    if (formGenerar.value.producto_id && !ids.includes(String(formGenerar.value.producto_id))) {
      formGenerar.value.producto_id = "";
    }
  },
  { immediate: true }
);

const isLoading = ref(false);

const productos = ref([]);
const pedidos = ref([]);
const modo = ref("generar");

// Generar QR (viajes a nivel pedido global)
const formGenerar = ref({
  venta_id: "",
  numero_viajes: null,
  producto_id: "",
});

const pedidoSeleccionado = computed(() => {
  if (!formGenerar.value.venta_id) return null;
  return pedidos.value.find((v) => String(v.id) === String(formGenerar.value.venta_id)) ?? null;
});

const viajesPlaceholder = computed(() => {
  const p = pedidoSeleccionado.value;
  if (p && p.viajes_permitidos != null) return "Del pedido: " + (p.viajes_permitidos ?? 0);
  return "Obligatorio si crea nuevo pedido";
});
const fotoGenerar = ref(null);
const enviandoGenerar = ref(false);
const resultadoGenerar = ref(null);

// Escanear QR
const inputScanRef = ref(null);
const escaneoActivo = ref(false);
const estadoQr = ref(null);
const fotoAcceso = ref(null);
const enviandoAcceso = ref(false);

async function cargarSucursal() {
  cargandoSucursal.value = true;
  try {
    const res = await ConfiguracionEmpresaRepository.getSucursal();
    const suc = res?.data ?? res;
    sucursalActual.value = suc?.id ? suc : null;
  } catch (e) {
    sucursalActual.value = null;
  } finally {
    cargandoSucursal.value = false;
  }
}

async function cargarProductos() {
  try {
    const res = await ProductoRepository.getProductos({ per_page: 200 });
    productos.value = Array.isArray(res?.data ?? res)
      ? res.data ?? res
      : res?.data?.data ?? [];
  } catch (e) {
    productos.value = [];
  }
}

async function cargarPedidos() {
  if (!sucursalActual.value?.id) return;
  try {
    const res = await VentaRepository.getVentas({
      sucursal_id: sucursalActual.value.id,
      estatus: "pendiente,parcial",
      per_page: 100,
    });
    const raw = res?.data ?? res;
    const list = raw?.data ?? (Array.isArray(raw) ? raw : []);
    pedidos.value = Array.isArray(list) ? list : [];
  } catch (e) {
    pedidos.value = [];
  }
}

function onFotoGenerarChange(ev) {
  const file = ev.target?.files?.[0];
  fotoGenerar.value = file || null;
}

async function generarQrLocal() {
  if (!formGenerar.value.producto_id) {
    window.Swal?.fire?.({
      icon: "warning",
      title: "Datos incompletos",
      text: "Seleccione un producto.",
    });
    return;
  }
  const ventaId = formGenerar.value.venta_id ? parseInt(formGenerar.value.venta_id, 10) : null;
  const numeroViajes = formGenerar.value.numero_viajes != null ? parseInt(formGenerar.value.numero_viajes, 10) : null;
  const pedido = pedidoSeleccionado.value;
  const necesitaViajes = !ventaId || (pedido && pedido.viajes_permitidos == null);
  if (necesitaViajes && (!numeroViajes || numeroViajes < 1)) {
    window.Swal?.fire?.({
      icon: "warning",
      title: "Número de viajes del pedido",
      text: "Indique el número de viajes del pedido (obligatorio al crear nuevo pedido o si el pedido aún no tiene viajes).",
    });
    return;
  }
  if (!fotoGenerar.value) {
    window.Swal?.fire?.({
      icon: "warning",
      title: "Foto requerida",
      text: "Debe tomar una foto del vehículo/material antes de generar el QR.",
    });
    return;
  }

  try {
    enviandoGenerar.value = true;
    resultadoGenerar.value = null;

    const payload = {
      producto_id: parseInt(formGenerar.value.producto_id, 10),
    };
    if (ventaId) payload.venta_id = ventaId;
    if (necesitaViajes && numeroViajes) payload.numero_viajes = numeroViajes;

    const respQr = await VigilanteRepository.generarQrLocal(payload);
    const dataQr = respQr.data || respQr;

    const qrPayload = dataQr.qr_payload;
    const partes = (qrPayload || "").split("|").filter((v) => v !== "");
    const uuid = partes[0] || dataQr.registro?.uuid;

    const formData = new FormData();
    formData.append("qr_payload", qrPayload);
    formData.append("foto", fotoGenerar.value);

    await VigilanteAccesoRepository.registrarAcceso(formData);

    resultadoGenerar.value = {
      uuid,
      folio: dataQr.folio ?? null,
      venta_id: dataQr.venta_id ?? null,
      viajes_permitidos: dataQr.viajes_permitidos ?? null,
      viajes_usados: dataQr.viajes_usados ?? 0,
      qr_payload: qrPayload,
    };

    await cargarPedidos();

    window.Swal?.fire?.({
      icon: "success",
      title: "QR generado y acceso inicial registrado",
    });
  } catch (e) {
    const msg = e.response?.data?.errors
      ? Object.values(e.response.data.errors).flat().join(" ")
      : e.message;
    window.Swal?.fire?.({
      icon: "error",
      title: "Error",
      text: msg,
    });
  } finally {
    enviandoGenerar.value = false;
  }
}

function activarEscaneo() {
  escaneoActivo.value = true;
  requestAnimationFrame(() => inputScanRef.value?.focus());
}

function onFotoAccesoChange(ev) {
  const file = ev.target?.files?.[0];
  fotoAcceso.value = file || null;
}

async function onScanEnter(event) {
  const input = event.target;
  const payload = (input?.value || "").trim();
  if (!payload) return;
  input.value = "";

  // Parsear cadena plana uuid|idSucursal|idProd|cant|idUnidad|...
  const partes = payload.split("|").filter((v) => v !== "");
  if (partes.length < 3) {
    window.Swal?.fire?.({
      icon: "error",
      title: "QR inválido",
      text: "Formato esperado: uuid|idSucursal|idProd|cant|idUnidad|...",
    });
    return;
  }

  const uuid = partes[0];
  const sucursalId = partes[1] ? parseInt(partes[1], 10) : null;
  const productoId = partes[2] ? parseInt(partes[2], 10) : null;
  const cantidad = partes[3] ? parseFloat(partes[3]) : 1;
  const unidadId = partes[4] ? parseInt(partes[4], 10) : null;

  estadoQr.value = {
    qr_payload: payload,
    uuid,
    sucursal_id: sucursalId,
    producto_id: productoId,
    cantidad,
    unidad_id: unidadId,
    cantidad_viaje: cantidad,
    viaje_actual: 1,
    viajes_permitidos: 1,
    producto_nombre: null,
    agotado: false,
  };

  window.Swal?.fire?.({
    icon: "info",
    title: "QR leído",
    text: "Seleccione una foto y registre el acceso.",
  });
}

async function registrarAcceso() {
  if (!estadoQr.value) return;
  if (!fotoAcceso.value) {
    window.Swal?.fire?.({
      icon: "warning",
      title: "Foto requerida",
      text: "Debe tomar una foto de evidencia para registrar el ingreso.",
    });
    return;
  }

  try {
    enviandoAcceso.value = true;

    const qr = estadoQr.value;
    const formData = new FormData();
    formData.append("qr_payload", qr.qr_payload);
    formData.append(
      "cantidad_viaje",
      String(qr.cantidad_viaje || qr.cantidad)
    );
    formData.append("foto", fotoAcceso.value);

    const resp = await VigilanteAccesoRepository.registrarAcceso(formData);

    const data = resp.data || resp;
    const viajeActual = data.viaje_actual ?? data.data?.viaje_actual;
    const viajesTotales = data.viajes_totales ?? data.data?.viajes_totales;

    window.Swal?.fire?.({
      icon: "success",
      title:
        resp.message ||
        (viajeActual && viajesTotales
          ? `Viaje ${viajeActual} de ${viajesTotales} registrado`
          : "Acceso registrado"),
    });

    fotoAcceso.value = null;
  } catch (e) {
    const msg = e.response?.data?.errors
      ? Object.values(e.response.data.errors).flat().join(" ")
      : e.message;
    window.Swal?.fire?.({
      icon: "error",
      title: "Error al registrar acceso",
      text: msg,
    });
  } finally {
    enviandoAcceso.value = false;
    requestAnimationFrame(() => inputScanRef.value?.focus());
  }
}

onMounted(async () => {
  isLoading.value = true;
  await cargarSucursal();
  if (sucursalActual.value && esMacuspana.value) {
    await Promise.all([cargarProductos(), cargarPedidos()]);
  }
  isLoading.value = false;
});
</script>

