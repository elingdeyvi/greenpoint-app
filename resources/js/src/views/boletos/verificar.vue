<template>
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="panel p-3">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-sm-12">
                            <div class="text-center titulo-dorado">
                                <h3 class="pull-left">Verificar Boleto de Salida</h3>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6 offset-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-group mb-3">
                                        <label>Folio o Código QR</label>
                                        <input
                                            type="text"
                                            class="form-control form-control-lg text-center"
                                            placeholder="Ingrese el folio o escanee el código QR"
                                            v-model="folio"
                                            @keyup.enter="validarBoleto"
                                            autofocus
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        class="btn btn-primary btn-lg w-100"
                                        @click="validarBoleto"
                                        :disabled="!folio || validando"
                                    >
                                        {{ validando ? 'Validando...' : 'Validar Boleto' }}
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Resultado de validación -->
                            <div v-if="resultadoValidacion" class="mt-4">
                                <div :class="['card', resultadoValidacion.valido ? 'border-success' : 'border-danger']">
                                    <div class="card-body text-center">
                                        <div v-if="resultadoValidacion.valido" class="mb-3">
                                            <div class="badge bg-success p-3" style="font-size: 2rem;">
                                                ✓ VÁLIDO
                                            </div>
                                            <p class="mt-3 text-success">Boleto validado correctamente. Puede proceder con la salida.</p>
                                        </div>
                                        <div v-else>
                                            <div class="badge bg-danger p-3" style="font-size: 2rem;">
                                                ✗ INVÁLIDO
                                            </div>
                                            <p class="mt-3 text-danger">{{ resultadoValidacion.mensaje }}</p>
                                        </div>
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
import { ref } from "vue";
import { useMeta } from "@/composables/use-meta";
import * as BoletoRepository from "@/repositories/BoletoRepository";
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import axios from "axios";

useMeta({ title: 'Verificar Boleto' });

const folio = ref('');
const validando = ref(false);
const isLoading = ref(false);
const resultadoValidacion = ref(null);

const validarBoleto = async () => {
    if (!folio.value.trim()) {
        showMessage("Ingrese un folio o código QR", "warning");
        return;
    }

    try {
        validando.value = true;
        isLoading.value = true;

        const response = await BoletoRepository.validarBoleto({
            folio: folio.value.trim()
        });

        if (response.data && response.valido) {
            resultadoValidacion.value = {
                valido: true,
                mensaje: "Boleto validado correctamente",
                boleto: response.data
            };
            showMessage("Boleto validado correctamente", "success");
            
            // Limpiar después de 3 segundos
            setTimeout(() => {
                folio.value = '';
                resultadoValidacion.value = null;
            }, 3000);
        } else {
            resultadoValidacion.value = {
                valido: false,
                mensaje: response.errors?.boleto?.[0] || "Boleto inválido"
            };
        }
    } catch (error) {
        if (axios.isAxiosError(error)) {
            const errorData = error.response?.data;
            resultadoValidacion.value = {
                valido: false,
                mensaje: errorData?.errors?.boleto?.[0] || errorData?.message || "Error al validar el boleto"
            };
        }
    } finally {
        validando.value = false;
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
</script>

<style scoped>
.card {
    transition: all 0.3s ease;
}
</style>

