import { ref, computed } from "vue";
import * as CajaRepository from "@/repositories/CajaRepository";

const cajaAbiertaData = ref(null);
const isLoading = ref(false);
const error = ref(null);

/**
 * Composable para estado global de la caja (abierta/cerrada).
 * Usado para bloquear "Nueva Venta" cuando no hay sesión de caja activa.
 */
export function useCaja() {
  const loadCajaAbierta = async (params = {}) => {
    try {
      isLoading.value = true;
      error.value = null;
      const response = await CajaRepository.getCajaAbierta(params);
      cajaAbiertaData.value = response?.data ?? null;
      return cajaAbiertaData.value;
    } catch (e) {
      error.value = e;
      cajaAbiertaData.value = null;
      return null;
    } finally {
      isLoading.value = false;
    }
  };

  const setCajaAbierta = (caja) => {
    cajaAbiertaData.value = caja;
  };

  const clearCajaAbierta = () => {
    cajaAbiertaData.value = null;
  };

  const cajaAbierta = computed(() => !!cajaAbiertaData.value);
  const caja = computed(() => cajaAbiertaData.value);

  return {
    caja,
    cajaAbierta,
    isLoading,
    error,
    loadCajaAbierta,
    setCajaAbierta,
    clearCajaAbierta,
  };
}
