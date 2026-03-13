<template>
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="panel p-3">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-sm-12">
                            <div class="text-center titulo-dorado">
                                <h3 class="pull-left">Reporte de Salidas</h3>
                                <button class="btn btn-black pull-right m-1" @click="exportarCsv">Exportar CSV</button>
                                <button class="btn btn-black pull-right m-1" @click="aplicarFiltros">Aplicar Filtros</button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha Desde</label>
                                <input type="date" class="form-control" v-model="filtros.fecha_desde" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha Hasta</label>
                                <input type="date" class="form-control" v-model="filtros.fecha_hasta" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Estatus</label>
                                <select class="form-control" v-model="filtros.estatus">
                                    <option value="">Todos</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="utilizado">Utilizado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Folio</label>
                                <input type="text" class="form-control" placeholder="Buscar por folio" v-model="filtros.folio" />
                            </div>
                        </div>
                    </div>
                    <div class="custom-table mt-4">
                        <v-client-table :data="boletos" :columns="columns" :options="table_option">
                            <template #estatus="item">
                                <span :class="getEstatusClass(item.row.estatus)">
                                    {{ getEstatusLabel(item.row.estatus) }}
                                </span>
                            </template>
                            <template #fecha_generacion="item">
                                <span>{{ formatearFecha(item.row.fecha_generacion) }}</span>
                            </template>
                            <template #fecha_validacion="item">
                                <span v-if="item.row.fecha_validacion">{{ formatearFecha(item.row.fecha_validacion) }}</span>
                                <span v-else class="text-muted">-</span>
                            </template>
                            <template #foto_ruta="item">
                                <img 
                                    v-if="item.row.foto_ruta" 
                                    :src="getFotoUrl(item.row.foto_ruta)" 
                                    alt="Foto" 
                                    style="width: 60px; height: 45px; object-fit: cover; cursor: pointer;"
                                    @click="verFoto(item.row.foto_ruta)"
                                />
                                <span v-else class="text-muted">Sin foto</span>
                            </template>
                            <template #actions="item">
                                <a href="javascript:void(0);" title="Ver Detalles" @click="verDetalle(item.row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>
        </div>
        <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" />
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useMeta } from "@/composables/use-meta";
import * as ReporteRepository from "@/repositories/ReporteRepository";
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';

useMeta({ title: 'Reporte de Salidas' });

const boletos = ref([]);
const isLoading = ref(true);
const filtros = ref({
    fecha_desde: '',
    fecha_hasta: '',
    estatus: '',
    folio: '',
});

const columns = ref([
    "id",
    "folio",
    "placa",
    "conductor",
    "estatus",
    "fecha_generacion",
    "fecha_validacion",
    "foto_ruta",
    "actions",
]);

const table_option = ref({
    headings: {
        id: () => "ID",
        folio: () => "Folio",
        placa: () => "Placa",
        conductor: () => "Conductor",
        estatus: () => "Estatus",
        fecha_generacion: () => "Fecha Generación",
        fecha_validacion: () => "Fecha Validación",
        foto_ruta: () => "Foto",
        actions: () => "",
    },
    perPage: 50,
    perPageValues: [20, 50, 100],
    skin: "table table-hover",
    columnsClasses: { actions: "actions text-center" },
    sortable: ["id", "folio", "placa", "fecha_generacion"],
    pagination: { nav: "scroll", chunk: 5 },
    texts: {
        count: "Mostrando {from} a {to} de {count}",
        filter: "",
        filterPlaceholder: "Buscar...",
        limit: "Resultados:",
        noResults: "No hay registros"
    },
});

const getFotoUrl = (fotoRuta) => {
    if (!fotoRuta) return null;
    return `/storage/${fotoRuta}`;
};

const getEstatusClass = (estatus) => {
    const classes = {
        'pendiente': 'badge bg-warning',
        'utilizado': 'badge bg-success',
        'cancelado': 'badge bg-danger'
    };
    return classes[estatus] || 'badge bg-secondary';
};

const getEstatusLabel = (estatus) => {
    const labels = {
        'pendiente': 'Pendiente',
        'utilizado': 'Utilizado',
        'cancelado': 'Cancelado'
    };
    return labels[estatus] || estatus;
};

const verFoto = (fotoRuta) => {
    if (fotoRuta) {
        window.open(getFotoUrl(fotoRuta), '_blank');
    }
};

const verDetalle = (id) => {
    console.log('Ver detalle boleto:', id);
};

const formatearFecha = (fecha) => {
    if (!fecha) return '-';
    return new Date(fecha).toLocaleString('es-MX', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const aplicarFiltros = async () => {
    isLoading.value = true;
    try {
        const params = {};
        if (filtros.value.fecha_desde) params.fecha_desde = filtros.value.fecha_desde;
        if (filtros.value.fecha_hasta) params.fecha_hasta = filtros.value.fecha_hasta;
        if (filtros.value.estatus) params.estatus = filtros.value.estatus;
        if (filtros.value.folio) params.folio = filtros.value.folio;

        const response = await ReporteRepository.getSalidas(params);
        const paginated = response?.data ?? response;
        boletos.value = Array.isArray(paginated?.data) ? paginated.data : (paginated || []);
        showMessage("Filtros aplicados correctamente");
    } catch (error) {
        console.log(error);
        showMessage("Error al aplicar filtros", "error");
    } finally {
        isLoading.value = false;
    }
};

const exportarCsv = async () => {
    try {
        isLoading.value = true;
        const params = {};
        if (filtros.value.fecha_desde) params.fecha_desde = filtros.value.fecha_desde;
        if (filtros.value.fecha_hasta) params.fecha_hasta = filtros.value.fecha_hasta;
        if (filtros.value.estatus) params.estatus = filtros.value.estatus;
        if (filtros.value.folio) params.folio = filtros.value.folio;

        const response = await ReporteRepository.exportarCsv(params);
        const base64 = response?.data;
        const filename = response?.filename || 'reporte_salidas.csv';
        if (!base64) {
            showMessage("No hay datos para exportar", "warning");
            return;
        }
        const csvContent = atob(base64);
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
        
        showMessage("CSV exportado correctamente");
    } catch (error) {
        console.log(error);
        showMessage("Error al exportar CSV", "error");
    } finally {
        isLoading.value = false;
    }
};

const initialize = async () => {
    isLoading.value = true;
    try {
        const response = await ReporteRepository.getSalidas();
        const paginated = response?.data ?? response;
        boletos.value = Array.isArray(paginated?.data) ? paginated.data : (paginated || []);
    } catch (error) {
        console.log(error);
    } finally {
        isLoading.value = false;
    }
};

const showMessage = (msg = "", type = "success") => {
    const toast = window.Swal.mixin({
        toast: true,
        position: "top",
        showConfirmButton: false,
        timer: 3000,
    });
    toast.fire({
        icon: type,
        title: msg,
        padding: "10px 20px",
    });
};

onMounted(async () => {
    await initialize();
});
</script>

