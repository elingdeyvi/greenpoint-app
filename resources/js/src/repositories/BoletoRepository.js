import ApiService from "@/services/ApiService";

const baseUrl = "boletos";

export const getAll = async (params = {}) => {
  const response = await ApiService.get(`${baseUrl}`, { params });
  return response.data;
};

export const createBoleto = async (params) => {
  const response = await ApiService.post(`${baseUrl}`, params);
  return response.data;
};

export const getBoletoById = async (id) => {
  const response = await ApiService.get(`${baseUrl}/${id}`);
  return response.data;
};

export const capturarFoto = async () => {
  const response = await ApiService.post(`${baseUrl}/capturar-foto`);
  return response.data;
};

export const validarBoleto = async (params) => {
  const response = await ApiService.post(`${baseUrl}/validar`, params);
  return response.data;
};

