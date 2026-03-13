<template>
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="panel p-3">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-sm-12">
                            <div class="text-center titulo-dorado">
                                <h3 class="pull-left">Lista de Boletos</h3>
                                <button
                                    class="btn btn-black pull-right m-1"
                                    @click="refrescar"
                                >
                                    Refrescar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="custom-table">
                        <v-client-table
                            :data="boletos"
                            :columns="columns"
                            :options="table_option"
                        >
                            <template #foto_ruta="item">
                                <img 
                                    v-if="item.row.foto_ruta" 
                                    :src="getFotoUrl(item.row.foto_ruta)" 
                                    alt="Foto" 
                                    style="width: 80px; height: 60px; object-fit: cover; cursor: pointer;"
                                    @click="verFoto(item.row.foto_ruta)"
                                />
                                <span v-else class="text-muted">Sin foto</span>
                            </template>
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
                            <template #actions="item">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a
                                        href="javascript:void(0);"
                                        title="Ver QR"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        @click="verQR(item.row)"
                                        class="text-primary"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="20"
                                            height="20"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <rect x="3" y="3" width="5" height="5"></rect>
                                            <rect x="16" y="3" width="5" height="5"></rect>
                                            <rect x="3" y="16" width="5" height="5"></rect>
                                            <path d="M21 16h-3a2 2 0 0 1-2-2v-3"></path>
                                        </svg>
                                    </a>
                                    <a
                                        href="javascript:void(0);"
                                        title="Ver Detalles"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        @click="verDetalle(item.row.id)"
                                        class="text-info"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="20"
                                            height="20"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="feather feather-eye"
                                        >
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </a>
                                </div>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>
        </div>
        <loading
            v-model:active="isLoading"
            :can-cancel="false"
            :is-full-page="true"
        />

        <!-- Modal para mostrar QR -->
        <div v-if="mostrarModalQR && boletoSeleccionado" class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Código QR - {{ boletoSeleccionado.folio }}</h5>
                        <button type="button" class="btn-close" @click="cerrarModalQR"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div v-if="boletoSeleccionado.codigo_qr">
                            <img 
                                :src="getQRCodeUrl(boletoSeleccionado.codigo_qr)" 
                                alt="Código QR" 
                                class="img-fluid border p-3 mb-3"
                                style="max-width: 300px;"
                            />
                            <p class="mb-2"><strong>Folio:</strong> {{ boletoSeleccionado.folio }}</p>
                            <p class="mb-2"><strong>Placa:</strong> {{ boletoSeleccionado.placa }}</p>
                            <p class="text-muted small">Escanea este código para validar el boleto</p>
                        </div>
                        <div v-else>
                            <p class="text-muted">No hay código QR disponible</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModalQR">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useMeta } from "@/composables/use-meta";
import * as BoletoRepository from "@/repositories/BoletoRepository";
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';

useMeta({ title: 'Lista de Boletos' });

const boletos = ref([]);
const isLoading = ref(true);
const mostrarModalQR = ref(false);
const boletoSeleccionado = ref(null);

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
    perPage: 20,
    perPageValues: [20, 50, 100],
    skin: "table table-hover",
    columnsClasses: { actions: "actions text-center" },
    sortable: ["id", "folio", "placa", "fecha_generacion"],
    sortIcon: {
        base: "sort-icon-none",
        up: "sort-icon-asc",
        down: "sort-icon-desc",
    },
    pagination: { nav: "scroll", chunk: 5 },
    texts: {
        count: "Mostrando {from} a {to} de {count}",
        filter: "",
        filterPlaceholder: "Buscar...",
        limit: "Resultados:",
        noResults: "No hay registros"
    },
    resizableColumns: true,
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
    // Implementar modal de detalles
    console.log('Ver detalle boleto:', id);
};

const verQR = (boleto) => {
    boletoSeleccionado.value = boleto;
    mostrarModalQR.value = true;
};

const cerrarModalQR = () => {
    mostrarModalQR.value = false;
    boletoSeleccionado.value = null;
};

const getQRCodeUrl = (codigoQr) => {
    if (!codigoQr) return null;
    // Usar API online para generar QR
    return `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(codigoQr)}`;
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

const initialize = async () => {
    isLoading.value = true;
    try {
        const response = await BoletoRepository.getAll();
        boletos.value = response.data?.data || [];
    } catch (error) {
        console.log(error);
    } finally {
        isLoading.value = false;
    }
};

const refrescar = () => {
    initialize();
    showMessage("Completado correctamente");
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

