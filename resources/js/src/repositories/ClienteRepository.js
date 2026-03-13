import ApiService from "@/services/ApiService";

const baseUrl = "clientes";

export const getAll = async (params = {}) => {
  const response = await ApiService.get(baseUrl, { params });
  return response.data;
};

export const getClienteById = async (id) => {
  const response = await ApiService.get(`${baseUrl}/${id}`);
  return response.data;
};

export const createCliente = async (params) => {
  const response = await ApiService.post(baseUrl, params);
  return response.data;
};

export const updateCliente = async (id, params) => {
  const response = await ApiService.put(`${baseUrl}/${id}`, params);
  return response.data;
};

export const deleteCliente = async (id) => {
  const response = await ApiService.delete(`${baseUrl}/${id}`);
  return response.data;
};
