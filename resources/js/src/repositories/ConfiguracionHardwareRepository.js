import ApiService from "@/services/ApiService";

const baseUrl = "configuracion-hardware";

export const getAll = async (params = {}) => {
  const response = await ApiService.get(`${baseUrl}`, { params });
  return response.data;
};

export const createHardware = async (params) => {
  const response = await ApiService.post(`${baseUrl}`, params);
  return response.data;
};

export const getHardwareById = async (id) => {
  const response = await ApiService.get(`${baseUrl}/${id}`);
  return response.data;
};

export const updateHardware = async (id, params) => {
  const response = await ApiService.put(`${baseUrl}/${id}`, params);
  return response.data;
};

export const deleteHardware = async (id) => {
  const response = await ApiService.delete(`${baseUrl}/${id}`);
  return response.data;
};

export const probarVideo = async (id) => {
  const response = await ApiService.post(`${baseUrl}/${id}/probar-video`);
  return response.data;
};

