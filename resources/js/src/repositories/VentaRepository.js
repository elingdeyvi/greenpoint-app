import ApiService from "@/services/ApiService";

const baseUrl = "ventas";

export const getVentas = async (params = {}) => {
  const response = await ApiService.get(baseUrl, { params });
  return response.data;
};

export const getVenta = async (id) => {
  const response = await ApiService.get(`${baseUrl}/${id}`);
  return response.data;
};

export const createVenta = async (payload) => {
  const response = await ApiService.post(baseUrl, payload);
  return response.data;
};

export const importarPedido = async (payload) => {
  const response = await ApiService.post(`${baseUrl}/importar-pedido`, payload);
  return response.data;
};

export const getPedidosPendientesPago = async (params = {}) => {
  const response = await ApiService.get(`${baseUrl}/pedidos-pendientes-pago`, { params });
  return response.data;
};

export const registrarPagosVenta = async (id, payload) => {
  const response = await ApiService.post(`${baseUrl}/${id}/registrar-pagos`, payload);
  return response.data;
};

/** Cancelar venta (PATCH). Backend repone stock solo en sucursal venta_almacen. */
export const cancelarVenta = async (id) => {
  const response = await ApiService.post(`${baseUrl}/${id}/cancelar`, {});
  return response.data;
};
