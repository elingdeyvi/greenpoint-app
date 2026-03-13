<template>
  <div class="layout-px-spacing">
    <teleport to="#breadcrumb">
      <ul class="navbar-nav flex-row">
        <li>
          <div class="page-header">
            <nav class="breadcrumb-one" aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:;">Reportes</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>Ventas</span></li>
              </ol>
            </nav>
          </div>
        </li>
      </ul>
    </teleport>

    <div class="row layout-top-spacing">
      <div class="col-12 mb-4">
        <div class="d-flex flex-wrap align-items-center gap-3">
          <label class="mb-0">Fecha desde</label>
          <input v-model="filtros.fecha_desde" type="date" class="form-control w-auto" />
          <label class="mb-0">Fecha hasta</label>
          <input v-model="filtros.fecha_hasta" type="date" class="form-control w-auto" />
          <span v-if="sucursalActiva" class="badge bg-primary align-self-center">Sucursal: {{ sucursalActiva.nombre }}</span>
          <button type="button" class="btn btn-primary" @click="cargarEstadisticas">Aplicar</button>
          <button type="button" class="btn btn-success" :disabled="exportando" @click="exportarExcel">
            {{ exportando ? "Exportando..." : "Exportar Excel (CSV)" }}
          </button>
        </div>
      </div>

      <div class="col-xl-4 col-lg-6 col-12 layout-spacing">
        <div class="widget widget-card-one">
          <div class="widget-content">
            <h6>Total ventas (período)</h6>
            <p class="meta-date-time text-primary fs-4">{{ estadisticas.cantidad_ventas || 0 }}</p>
            <p class="meta-date-time">Ventas</p>
          </div>
        </div>
      </div>
      <div class="col-xl-4 col-lg-6 col-12 layout-spacing">
        <div class="widget widget-card-one">
          <div class="widget-content">
            <h6>Monto total</h6>
            <p class="meta-date-time text-success fs-4">$ {{ (estadisticas.total_ventas || 0).toFixed(2) }}</p>
          </div>
        </div>
      </div>

      <div class="col-xl-8 col-12 layout-spacing">
        <div class="widget widget-revenue">
          <div class="widget-heading">
            <h5>Ventas por día (por sucursal)</h5>
          </div>
          <div class="widget-content">
            <apex-chart
              v-if="seriesPorSucursal.length"
              height="320"
              type="area"
              :options="opcionesGrafico"
              :series="seriesPorSucursal"
            />
            <div v-else class="text-center text-muted py-5">Sin datos para el período seleccionado</div>
          </div>
        </div>
      </div>

      <div class="col-xl-4 col-12 layout-spacing">
        <div class="widget widget-sales-category">
          <div class="widget-heading">
            <h5>Ventas por sucursal</h5>
          </div>
          <div class="widget-content">
            <apex-chart
              v-if="seriesDonut.length"
              height="320"
              type="donut"
              :options="opcionesDonut"
              :series="seriesDonut"
            />
            <div v-else class="text-center text-muted py-5">Sin datos</div>
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
import { useStore } from "vuex";
import ApexChart from "vue3-apexcharts";
import * as ConfiguracionEmpresaRepository from "@/repositories/ConfiguracionEmpresaRepository";
import * as ReporteRepository from "@/repositories/ReporteRepository";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";

useMeta({ title: "Reportes de Ventas" });

const store = useStore();
const isLoading = ref(false);
const exportando = ref(false);
const sucursalActiva = ref(null);
const estadisticas = ref({
  por_dia: [],
  por_sucursal: [],
  fechas: [],
  series_por_sucursal: [],
  total_ventas: 0,
  cantidad_ventas: 0,
});

const filtros = ref({
  fecha_desde: "",
  fecha_hasta: "",
  sucursal_id: "",
});

const seriesPorSucursal = computed(() => estadisticas.value.series_por_sucursal || []);
const seriesDonut = computed(() => (estadisticas.value.por_sucursal || []).map((s) => Number(s.total_ventas) || 0));
const labelsDonut = computed(() => (estadisticas.value.por_sucursal || []).map((s) => s.sucursal_nombre || "N/A"));

const opcionesGrafico = computed(() => {
  const isDark = store.state.is_dark_mode;
  const fechas = (estadisticas.value.fechas || []).map((f) => {
    const d = new Date(f);
    return d.toLocaleDateString("es-ES", { day: "2-digit", month: "short" });
  });
  return {
    chart: { fontFamily: "Nunito, sans-serif", toolbar: { show: false } },
    dataLabels: { enabled: false },
    stroke: { curve: "smooth", width: 2 },
    xaxis: { categories: fechas },
    yaxis: { labels: { formatter: (v) => (v != null ? "$ " + Number(v).toFixed(0) : "") } },
    colors: isDark ? ["#2196f3", "#4caf50", "#ff9800"] : ["#1b55e2", "#1abc9c", "#e2a03f"],
    fill: { type: "gradient", gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
    grid: { borderColor: isDark ? "#191e3a" : "#e0e6ed" },
    legend: { position: "top" },
  };
});

const opcionesDonut = computed(() => ({
  chart: {},
  labels: labelsDonut.value,
  legend: { position: "bottom" },
  dataLabels: { enabled: true },
}));

function descargarArchivo(base64, filename) {
  try {
    const bin = atob(base64);
    const arr = new Uint8Array(bin.length);
    for (let i = 0; i < bin.length; i++) arr[i] = bin.charCodeAt(i);
    const blob = new Blob([arr], { type: "text/csv;charset=utf-8;" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = filename || "reporte_ventas.csv";
    a.click();
    URL.revokeObjectURL(url);
  } catch (e) {
    console.error(e);
  }
}

async function cargarEstadisticas() {
  isLoading.value = true;
  try {
    const params = {
      fecha_desde: filtros.value.fecha_desde,
      fecha_hasta: filtros.value.fecha_hasta,
      sucursal_id: filtros.value.sucursal_id || undefined,
    };
    const res = await ReporteRepository.getEstadisticasVentas(params);
    const payload = res?.data ?? res ?? {};
    estadisticas.value = {
      por_dia: payload.por_dia ?? [],
      por_sucursal: Array.isArray(payload.por_sucursal) ? payload.por_sucursal : [],
      fechas: Array.isArray(payload.fechas) ? payload.fechas : [],
      series_por_sucursal: Array.isArray(payload.series_por_sucursal) ? payload.series_por_sucursal : [],
      total_ventas: Number(payload.total_ventas) || 0,
      cantidad_ventas: Number(payload.cantidad_ventas) || 0,
    };
  } catch (e) {
    console.error(e);
    estadisticas.value = { por_dia: [], por_sucursal: [], fechas: [], series_por_sucursal: [], total_ventas: 0, cantidad_ventas: 0 };
  } finally {
    isLoading.value = false;
  }
}

async function exportarExcel() {
  exportando.value = true;
  try {
    const params = {
      fecha_desde: filtros.value.fecha_desde,
      fecha_hasta: filtros.value.fecha_hasta,
      sucursal_id: filtros.value.sucursal_id || undefined,
    };
    const res = await ReporteRepository.exportarVentasExcel(params);
    const data = res?.data ?? res;
    const filename = res?.filename || "reporte_ventas.csv";
    if (data) {
      descargarArchivo(data, filename);
      window.Swal?.fire?.({ icon: "success", title: "Exportado correctamente" });
    } else {
      window.Swal?.fire?.({ icon: "warning", title: "No hay datos para exportar" });
    }
  } catch (e) {
    window.Swal?.fire?.({ icon: "error", title: "Error al exportar", text: e?.message || "Error" });
  } finally {
    exportando.value = false;
  }
}

onMounted(async () => {
  const hoy = new Date().toISOString().slice(0, 10);
  const hace30 = new Date();
  hace30.setDate(hace30.getDate() - 30);
  filtros.value.fecha_desde = filtros.value.fecha_desde || hace30.toISOString().slice(0, 10);
  filtros.value.fecha_hasta = filtros.value.fecha_hasta || hoy;
  try {
    const res = await ConfiguracionEmpresaRepository.getSucursal();
    const suc = res?.data ?? res;
    if (suc?.id) {
      sucursalActiva.value = suc;
      filtros.value.sucursal_id = suc.id;
    }
  } catch (_) {}
  await cargarEstadisticas();
});
</script>
