import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import * as VentaRepository from "@/repositories/VentaRepository";

/**
 * Composable para la lógica reactiva del módulo de Ventas (POS).
 * Usa VentaRepository para todas las llamadas al backend.
 *
 * Nota backend: Si la sucursal es tipo "venta" (Villahermosa), el servidor no
 * descuenta stock al crear la venta pero sí genera qr_payload en la respuesta.
 * Si es "venta_almacen" (Macuspana), descuenta stock. El payload enviado es
 * el mismo; el backend decide según sucursal.
 */
export function useVentas(options = {}) {
  const { t } = useI18n();

  const totalVentaRef = options.totalVenta ?? ref(0);
  const pagosRef = options.pagos ?? ref([]);

  /**
   * Remanente de pago: Total Venta - Suma(monto de pagos).
   * Reactivo: se actualiza cuando cambian totalVenta o pagos.
   */
  const remanentePago = computed(() => {
    const total = Number(totalVentaRef.value) || 0;
    const sumaPagos = (pagosRef.value || []).reduce(
      (acc, p) => acc + (Number(p.monto) || 0),
      0
    );
    return Math.max(0, total - sumaPagos);
  });

  /**
   * Suma total de los montos registrados en pagos.
   */
  const sumaPagos = computed(() => {
    return (pagosRef.value || []).reduce(
      (acc, p) => acc + (Number(p.monto) || 0),
      0
    );
  });

  /**
   * Indica si la suma de pagos es suficiente (>= total), incluyendo pagos con cambio.
   */
  const pagosCuadran = computed(() => {
    const total = Number(totalVentaRef.value) || 0;
    if (total <= 0) return false;
    return sumaPagos.value >= total - 0.01;
  });

  /**
   * Cambio a devolver: cuando el método es efectivo, el campo monto representa el dinero
   * entregado por el cliente. Se calcula el exceso por línea (en orden) sobre el total a cubrir.
   */
  const cambioTotal = computed(() => {
    const total = Number(totalVentaRef.value) || 0;
    const lista = pagosRef.value || [];
    let restante = total;
    let cambio = 0;
    for (const p of lista) {
      const monto = Number(p.monto) || 0;
      const aplicado = Math.min(monto, restante);
      if (p.metodo_pago === "efectivo" && monto > aplicado) {
        cambio += monto - aplicado;
      }
      restante -= aplicado;
    }
    return Math.max(0, cambio);
  });

  /**
   * Crea una venta en el backend usando VentaRepository.
   * @param {Object} payload - { sucursal_id, tipo, es_donativo?, observaciones?, detalles[], pagos[] }
   * @returns {Promise<{ success: boolean, data?: object, message?: string, error?: string }>}
   *   message/error ya traducidos con t().
   */
  const createVenta = async (payload) => {
    try {
      const result = await VentaRepository.createVenta(payload);
      return {
        success: true,
        data: result?.data ?? result,
        message: t("pos_venta_creada"),
      };
    } catch (e) {
      const rawMessage = e.response?.data?.errors
        ? Object.values(e.response.data.errors).flat().join(" ")
        : e.message;
      return {
        success: false,
        error: rawMessage || t("pos_error_crear_venta"),
      };
    }
  };

  const getVentas = async (params = {}) => {
    return VentaRepository.getVentas(params);
  };

  const getVenta = async (id) => {
    return VentaRepository.getVenta(id);
  };

  const getPedidosPendientesPago = async (params = {}) => {
    return VentaRepository.getPedidosPendientesPago(params);
  };

  const registrarPagosVenta = async (ventaId, payload) => {
    try {
      const result = await VentaRepository.registrarPagosVenta(ventaId, payload);
      return {
        success: true,
        data: result?.data ?? result,
        message: t("pos_venta_creada"),
      };
    } catch (e) {
      const rawMessage = e.response?.data?.errors
        ? Object.values(e.response.data.errors).flat().join(" ")
        : e.message;
      return {
        success: false,
        error: rawMessage || t("pos_error_crear_venta"),
      };
    }
  };

  /** Cancelar venta. Disponible para ambos tipos de sucursal; backend repone stock solo en venta_almacen. */
  const cancelarVenta = async (ventaId) => {
    try {
      const result = await VentaRepository.cancelarVenta(ventaId);
      return {
        success: true,
        data: result?.data ?? result,
        message: t("pos_venta_cancelada"),
      };
    } catch (e) {
      const rawMessage = e.response?.data?.errors
        ? Object.values(e.response.data.errors).flat().join(" ")
        : e.message;
      return {
        success: false,
        error: rawMessage || t("pos_error_cancelar_venta"),
      };
    }
  };

  return {
    remanentePago,
    sumaPagos,
    pagosCuadran,
    cambioTotal,
    createVenta,
    getVentas,
    getVenta,
    getPedidosPendientesPago,
    registrarPagosVenta,
    cancelarVenta,
    t,
  };
}
