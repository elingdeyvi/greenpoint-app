<template>
  <div class="vigilante-scanner">
    <div class="mb-3">
      <h4 class="mb-1">Control de accesos - Vigilante</h4>
      <p class="text-muted mb-0">
        Escanee y genere QRs para controlar los viajes de camiones en esta sucursal.
      </p>
    </div>

    <div v-if="cargandoSucursal" class="alert alert-info py-2">
      Cargando información de la sucursal...
    </div>
    <div v-else-if="!sucursalActual" class="alert alert-warning">
      Configure la sucursal en <strong>Configuración de la empresa</strong> para habilitar el módulo de Vigilante.
    </div>
    <div
      v-else-if="!esMacuspana"
      class="alert alert-info"
    >
      Este módulo está diseñado para sucursales tipo almacén (ej. Macuspana). Tipo actual:
      <strong>{{ sucursalActual.tipo_sucursal }}</strong>
    </div>

    <div v-if="sucursalActual && esMacuspana" class="mb-3">
      <span class="badge bg-primary fs-6">
        Sucursal actual: {{ sucursalActual.nombre }}
      </span>
    </div>

    <div v-if="sucursalActual && esMacuspana" class="mb-4">
      <div class="btn-group" role="group">
        <button
          type="button"
          class="btn"
          :class="modo === 'generar' ? 'btn-primary' : 'btn-outline-primary'"
          @click="modo = 'generar'"
        >
          Generar QR local
        </button>
        <button
          type="button"
          class="btn"
          :class="modo === 'escanear' ? 'btn-primary' : 'btn-outline-primary'"
          @click="modo = 'escanear'"
        >
          Escanear QR
        </button>
      </div>
    </div>

    <!-- Generar QR local -->
    <div
      v-if="sucursalActual && esMacuspana && modo === 'generar'"
      class="card mb-4"
    >
      <div class="card-body">
        <h5 class="card-title mb-3">Generar QR local</h5>
        <p class="text-muted">
          Seleccione el producto y defina el <strong>número de viajes</strong> que permitirá este QR.
        </p>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Producto</label>
            <select
              v-model="formGenerar.producto_id"
              class="form-select"
            >
              <option value="" disabled>Seleccione producto</option>
              <option
                v-for="prod in productosParaGenerar"
                :key="prod.id"
                :value="prod.id"
              >
                {{ prod.nombre }}
              </option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Número de viajes</label>
            <input
              v-model.number="formGenerar.numero_viajes"
              type="number"
              min="1"
              class="form-control"
            />
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button
              type="button"
              class="btn btn-success w-100"
              :disabled="enviandoGenerar"
              @click="onGenerarQr"
            >
              <span v-if="!enviandoGenerar">Generar QR</span>
              <span v-else>Generando...</span>
            </button>
          </div>
        </div>

        <div
          v-if="resultadoGenerar"
          class="mt-4 p-3 border rounded bg-light"
        >
          <h6 class="mb-2">QR generado</h6>
          <p class="mb-1">
            <strong>UUID:</strong>
            <span class="font-monospace">{{ resultadoGenerar.registro?.uuid }}</span>
          </p>
          <p class="mb-1">
            <strong>Número de viajes permitidos:</strong>
            {{ resultadoGenerar.registro?.viajes_permitidos }}
          </p>
          <p class="mb-2">
            <strong>Payload (cadena para QR):</strong>
          </p>
          <pre class="small bg-white p-2 border rounded mb-0" style="white-space: pre-wrap;">
{{ resultadoGenerar.qr_payload }}
          </pre>
          <p class="mt-2 small text-muted">
            Use esta cadena en cualquier generador de QR. El lector del vigilante enviará exactamente este texto al sistema.
          </p>
        </div>
      </div>
    </div>

    <!-- Escanear / validar QR -->
    <div
      v-if="sucursalActual && esMacuspana && modo === 'escanear'"
      class="card"
    >
      <div class="card-body">
        <h5 class="card-title mb-3">Escanear y validar QR</h5>
        <p class="text-muted">
          Haga clic en el área de abajo y luego escanee el QR con el lector. El texto no se muestra ni edita manualmente.
        </p>

        <div
          class="border rounded p-4 text-center mb-3"
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
            El lector enviará el contenido automáticamente; no se muestra ni edita el texto.
          </small>
        </div>

        <div v-if="enviandoValidar" class="text-center py-2">
          <span class="text-primary">Validando QR...</span>
        </div>

        <div
          v-if="resultadoValidar"
          class="mt-3 p-3 border rounded bg-white"
        >
          <h6 class="mb-2">
            Resultado:
            <span
              class="badge"
              :class="resultadoValidar.data.estatus === 'activo' ? 'bg-success' : 'bg-secondary'"
            >
              {{ resultadoValidar.data.estatus === 'activo' ? 'QR ACTIVO' : 'SIN VIAJES' }}
            </span>
          </h6>
          <p class="mb-1">
            <strong>Origen:</strong>
            <span class="badge" :class="resultadoValidar.data.origen === 'importado' ? 'bg-info' : 'bg-primary'">
              {{ resultadoValidar.data.origen === 'importado' ? 'Importado (externo)' : 'Local (Macuspana)' }}
            </span>
          </p>
          <p class="mb-1">
            <strong>UUID:</strong>
            <span class="font-monospace">{{ resultadoValidar.data.uuid }}</span>
          </p>
          <p class="mb-1">
            <strong>Viajes:</strong>
            {{ resultadoValidar.data.viajes_usados }} de {{ resultadoValidar.data.viajes_permitidos }}
            (restantes: {{ resultadoValidar.data.viajes_restantes }})
          </p>
          <div v-if="resultadoValidar.data.detalle_principal" class="mt-2">
            <p class="mb-1"><strong>Producto:</strong> {{ resultadoValidar.data.detalle_principal.producto_nombre ?? resultadoValidar.data.detalle_principal.producto_id }}</p>
            <p class="mb-1"><strong>Cantidad:</strong> {{ resultadoValidar.data.detalle_principal.cantidad }}</p>
          </div>
          <p class="mt-2 mb-0 small text-muted">
            Mensaje: {{ resultadoValidar.message }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useStore } from "vuex";
import * as ConfiguracionEmpresaRepository from "@/repositories/ConfiguracionEmpresaRepository";
import * as ProductoRepository from "@/repositories/ProductoRepository";
import * as VigilanteRepository from "@/repositories/VigilanteRepository";

const store = useStore();
const sucursalActual = ref(null);
const cargandoSucursal = ref(true);
const esMacuspana = computed(
  () => sucursalActual.value?.tipo_sucursal === "venta_almacen"
);

/** Perfil de interfaz: en VENTA_ALMACEN solo se muestran Polvo, Rezaga, Balastre en generación de QR. */
const perfilInterfaz = computed(() => store.getters.perfilInterfaz || null);
const PRODUCTOS_PERMITIDOS_CASETA = ["Polvo", "Rezaga", "Balastre"];

const productos = ref([]);
/** Productos para el selector de "Generar QR local". En VENTA_ALMACEN solo los permitidos en caseta. */
const productosParaGenerar = computed(() => {
  const lista = productos.value;
  if (perfilInterfaz.value === "VENTA_ALMACEN") {
    return lista.filter((p) => PRODUCTOS_PERMITIDOS_CASETA.includes(p.nombre));
  }
  return lista;
});
const modo = ref("generar");

const formGenerar = ref({
  producto_id: "",
  numero_viajes: 1,
});
const enviandoGenerar = ref(false);
const resultadoGenerar = ref(null);

const inputScanRef = ref(null);
const escaneoActivo = ref(false);
const enviandoValidar = ref(false);
const resultadoValidar = ref(null);

async function cargarSucursal() {
  cargandoSucursal.value = true;
  try {
    const res = await ConfiguracionEmpresaRepository.getSucursal();
    const suc = res?.data ?? res;
    if (suc?.id) {
      sucursalActual.value = suc;
    } else {
      sucursalActual.value = null;
    }
  } catch (e) {
    sucursalActual.value = null;
  } finally {
    cargandoSucursal.value = false;
  }
}

async function cargarProductos() {
  try {
    const res = await ProductoRepository.getProductos({ per_page: 200 });
    // Backend devuelve paginado o lista directa según implementación; normalizar.
    productos.value = Array.isArray(res?.data ?? res)
      ? res.data ?? res
      : res?.data?.data ?? [];
  } catch (e) {
    productos.value = [];
  }
}

async function onGenerarQr() {
  if (!formGenerar.value.producto_id || !formGenerar.value.numero_viajes) {
    window.Swal?.fire?.({
      icon: "warning",
      title: "Datos incompletos",
      text: "Seleccione un producto e indique el número de viajes.",
    });
    return;
  }

  try {
    enviandoGenerar.value = true;
    resultadoGenerar.value = null;
    const resp = await VigilanteRepository.generarQrLocal({
      producto_id: parseInt(formGenerar.value.producto_id, 10),
      numero_viajes: parseInt(formGenerar.value.numero_viajes, 10),
    });
    resultadoGenerar.value = resp.data ? resp : { data: resp };
    window.Swal?.fire?.({
      icon: "success",
      title: resp.message || "QR generado",
    });
  } catch (e) {
    const msg = e.response?.data?.errors
      ? Object.values(e.response.data.errors).flat().join(" ")
      : e.message;
    window.Swal?.fire?.({
      icon: "error",
      title: "Error al generar",
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

async function onScanEnter(event) {
  const input = event.target;
  const payload = (input?.value || "").trim();
  if (!payload) return;

  input.value = "";
  await validarQr(payload);
}

async function validarQr(payload) {
  try {
    enviandoValidar.value = true;
    resultadoValidar.value = null;
    const resp = await VigilanteRepository.validarQr(payload);
    resultadoValidar.value = resp;
    window.Swal?.fire?.({
      icon: "success",
      title: resp.message || "QR validado",
    });
  } catch (e) {
    const msg = e.response?.data?.errors
      ? Object.values(e.response.data.errors).flat().join(" ")
      : e.message;
    window.Swal?.fire?.({
      icon: "error",
      title: "QR inválido",
      text: msg,
    });
  } finally {
    enviandoValidar.value = false;
    requestAnimationFrame(() => inputScanRef.value?.focus());
  }
}

onMounted(async () => {
  await cargarSucursal();
  if (sucursalActual.value && esMacuspana.value) {
    await cargarProductos();
  }
});
</script>

<style scoped>
.vigilante-scanner {
  max-width: 960px;
}
</style>

