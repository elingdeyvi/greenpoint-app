import ApiService from "@/services/ApiService";

const baseUrl = "productos";

export const getProductos = async (params = {}) => {
  const response = await ApiService.get(baseUrl, { params });
  return response.data;
};

export const getAll = async (params = {}) => {
  const response = await ApiService.get(baseUrl, { params });
  return response.data;
};

export const getProductoById = async (id) => {
  const response = await ApiService.get(`${baseUrl}/${id}`);
  return response.data;
};

export const createProducto = async (params) => {
  const response = await ApiService.post(baseUrl, params);
  return response.data;
};

export const updateProducto = async (id, params) => {
  const response = await ApiService.put(`${baseUrl}/${id}`, params);
  return response.data;
};

export const deleteProducto = async (id) => {
  const response = await ApiService.delete(`${baseUrl}/${id}`);
  return response.data;
};
