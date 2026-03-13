import ApiService from "@/services/ApiService";

const baseUrl = "inventario";

export const getAlertas = async (params = {}) => {
  const response = await ApiService.get(`${baseUrl}/alertas`, { params });
  return response.data;
};

export const getInventario = async (params = {}) => {
  const response = await ApiService.get(baseUrl, { params });
  return response.data;
};

export const ajustarStock = async (payload) => {
  const response = await ApiService.post(`${baseUrl}/ajustar`, payload);
  return response.data;
};

export const getMovements = async (params = {}) => {
  const response = await ApiService.get(`${baseUrl}/movimientos`, { params });
  return response.data;
};
