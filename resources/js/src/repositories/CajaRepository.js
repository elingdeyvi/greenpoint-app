import ApiService from "@/services/ApiService";

const baseUrl = "cajas";

export const getCajas = async (params = {}) => {
  const response = await ApiService.get(baseUrl, { params });
  return response.data;
};

export const getCajaAbierta = async (params = {}) => {
  const response = await ApiService.get(`${baseUrl}/caja-abierta`, { params });
  return response.data;
};

export const abrirCaja = async (data) => {
  const response = await ApiService.post(`${baseUrl}/apertura`, data);
  return response.data;
};

export const cerrarCaja = async (data) => {
  const response = await ApiService.post(`${baseUrl}/corte`, data);
  return response.data;
};

export const getGastos = async (cajaId) => {
  const response = await ApiService.get(`${baseUrl}/${cajaId}/gastos`);
  return response.data;
};

export const crearGasto = async (cajaId, data) => {
  const response = await ApiService.post(`${baseUrl}/${cajaId}/gastos`, data);
  return response.data;
};

/** Objeto repositorio siguiendo el patrón de ApiService (camelCase, mismo estilo que otros repos). */
export default {
  getCajas,
  getCajaAbierta,
  abrirCaja,
  cerrarCaja,
  getGastos,
  crearGasto,
};
