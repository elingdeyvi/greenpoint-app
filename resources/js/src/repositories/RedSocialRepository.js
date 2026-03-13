import ApiService from '@/services/ApiService';

const baseUrl = 'redes-sociales';

const getAll = async (params = {}) => {
  const response = await ApiService.get(baseUrl, { params });
  return response.data;
};

const getById = async (id) => {
  const response = await ApiService.get(`${baseUrl}/${id}`);
  return response.data;
};

const create = async (data) => {
  const response = await ApiService.post(baseUrl, data);
  return response.data;
};

const update = async (id, data) => {
  const response = await ApiService.put(`${baseUrl}/${id}`, data);
  return response.data;
};

const remove = async (id) => {
  const response = await ApiService.delete(`${baseUrl}/${id}`);
  return response.data;
};

export default {
  getAll,
  getById,
  create,
  update,
  delete: remove,
};

