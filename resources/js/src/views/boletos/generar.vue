<template>
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="panel p-3">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-sm-12">
                            <div class="text-center titulo-dorado">
                                <h3 class="pull-left">Generar Boleto de Salida</h3>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <form @submit.stop.prevent="generarBoleto">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Placa *</label>
                                            <input
                                                type="text"
                                                class="form-control mb-2"
                                                placeholder="Placa del vehículo"
                                                v-model="form.placa"
                                                :class="[is_entrada ? (!errors.placa ? 'is-valid' : 'is-invalid') : '']"
                                            />
                                            <div class="invalid-feedback">
                                                <li v-for="(item, index) in errors.placa" :key="index">
                                                    {{ item }}
                                                </li>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Conductor</label>
                                            <input
                                                type="text"
                                                class="form-control mb-2"
                                                placeholder="Nombre del conductor"
                                                v-model="form.conductor"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Observaciones</label>
                                            <textarea
                                                class="form-control mb-2"
                                                placeholder="Observaciones adicionales"
                                                v-model="form.observaciones"
                                                rows="3"
                                            ></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Foto del Volteo</label>
                                            <div class="d-flex gap-2 mb-2">
                                                <button
                                                    type="button"
                                                    class="btn btn-primary"
                                                    @click="capturarFoto"
                                                    :disabled="capturandoFoto"
                                                >
                                                    {{ capturandoFoto ? 'Capturando...' : 'Capturar Foto' }}
                                                </button>
                                            </div>
                                            <div v-if="fotoPreview" class="mt-2">
                                                <img :src="fotoPreview" alt="Vista previa" style="max-width: 100%; max-height: 400px; object-fit: contain; border: 2px solid #ddd; border-radius: 4px;" />
                                                <p class="text-success mt-2">✓ Foto capturada correctamente</p>
                                            </div>
                                            <div v-else class="alert alert-info">
                                                La foto es opcional. Puede capturar una foto del volteo si lo desea.
                                            </div>
                                            <div class="invalid-feedback">
                                                <li v-for="(item, index) in errors.foto_ruta" :key="index">
                                                    {{ item }}
                                                </li>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button
                                                type="submit"
                                                class="btn btn-success w-100"
                                                :disabled="generando"
                                            >
                                                {{ generando ? 'Generando...' : 'Generar e Imprimir Boleto' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Instrucciones</h5>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Verifique que el volteo esté posicionado en el área designada</li>
                                        <li>Complete los datos del formulario</li>
                                        <li>Opcionalmente capture la foto del volteo</li>
                                        <li>Genere e imprima el boleto</li>
                                        <li>Entregue el boleto al conductor</li>
                                    </ol>
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

        <!-- Modal para mostrar boleto generado con QR -->
        <div v-if="mostrarModalBoleto && boletoGenerado" class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Boleto Generado</h5>
                        <button type="button" class="btn-close" @click="cerrarModalBoleto"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-3">Información del Boleto</h4>
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">Folio:</th>
                                                <td><strong>{{ boletoGenerado.folio }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Placa:</th>
                                                <td>{{ boletoGenerado.placa }}</td>
                                            </tr>
                                            <tr v-if="boletoGenerado.conductor">
                                                <th>Conductor:</th>
                                                <td>{{ boletoGenerado.conductor }}</td>
                                            </tr>
                                            <tr>
                                                <th>Estatus:</th>
                                                <td>
                                                    <span class="badge bg-warning">{{ boletoGenerado.estatus }}</span>
                                                </td>
                                            </tr>
                                            <tr v-if="boletoGenerado.observaciones">
                                                <th>Observaciones:</th>
                                                <td>{{ boletoGenerado.observaciones }}</td>
                                            </tr>
                                            <tr>
                                                <th>Fecha Generación:</th>
                                                <td>{{ new Date(boletoGenerado.fecha_generacion).toLocaleString() }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Código QR</h5>
                                        <div v-if="boletoGenerado.codigo_qr" class="mb-3">
                                            <img 
                                                :src="getQRCodeUrl(boletoGenerado.codigo_qr)" 
                                                alt="Código QR" 
                                                class="img-fluid border p-2"
                                                style="max-width: 200px;"
                                            />
                                        </div>
                                        <p class="text-muted small">Escanea este código para validar el boleto</p>
                                        <div v-if="boletoGenerado.foto_ruta" class="mt-3">
                                            <h6>Foto del Volteo</h6>
                                            <img 
                                                :src="`/storage/${boletoGenerado.foto_ruta}`" 
                                                alt="Foto" 
                                                class="img-fluid border rounded"
                                                style="max-width: 100%; max-height: 200px; object-fit: contain;"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModalBoleto">Cerrar</button>
                        <button type="button" class="btn btn-primary" @click="window.print()">Imprimir</button>
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
import axios from "axios";

useMeta({ title: 'Generar Boleto' });

const form = ref({
    placa: '',
    conductor: '',
    observaciones: '',
    foto_ruta: null,
});

const errors = ref({});
const is_entrada = ref(false);
const isLoading = ref(false);
const capturandoFoto = ref(false);
const generando = ref(false);
const fotoPreview = ref(null);
const boletoGenerado = ref(null);
const mostrarModalBoleto = ref(false);

const capturarFoto = async () => {
    try {
        capturandoFoto.value = true;
        const response = await BoletoRepository.capturarFoto();
        if (response.data) {
            form.value.foto_ruta = response.data.foto_ruta;
            fotoPreview.value = response.data.foto_url;
            if (response.prueba && response.mensaje_aviso) {
                showMessage(response.mensaje_aviso, "warning");
            } else {
                showMessage("Foto capturada correctamente", "success");
            }
        }
    } catch (error) {
        if (axios.isAxiosError(error)) {
            errors.value = error.response?.data?.errors || {};
            showMessage("Error al capturar foto: " + (error.response?.data?.message || error.message), "error");
        }
    } finally {
        capturandoFoto.value = false;
    }
};

const generarBoleto = async () => {
    try {
        generando.value = true;
        is_entrada.value = true;
        isLoading.value = true;

        const response = await BoletoRepository.createBoleto(form.value);
        
        if (response.data) {
            boletoGenerado.value = response.data;
            mostrarModalBoleto.value = true;
            showMessage("Boleto generado correctamente", "success");
        }
        
    } catch (error) {
        if (axios.isAxiosError(error)) {
            errors.value = error.response?.data?.errors || {};
            showMessage("Error al generar boleto: " + (error.response?.data?.message || error.message), "error");
        }
    } finally {
        generando.value = false;
        isLoading.value = false;
    }
};

const cerrarModalBoleto = () => {
    mostrarModalBoleto.value = false;
    boletoGenerado.value = null;
    // Resetear formulario después de cerrar el modal
    initializeForm();
};

const getQRCodeUrl = (codigoQr) => {
    if (!codigoQr) return null;
    // Usar API online para generar QR
    return `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(codigoQr)}`;
};

const initializeForm = () => {
    form.value = {
        placa: '',
        conductor: '',
        observaciones: '',
        foto_ruta: null,
    };
    fotoPreview.value = null;
    errors.value = {};
    is_entrada.value = false;
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

onMounted(() => {
    isLoading.value = false;
});
</script>

