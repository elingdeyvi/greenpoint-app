import ApiService from '@/services/ApiService';
import ApiServiceFile from '@/services/ApiServiceFile';

const baseUrl = 'galeria';

const getAll = async (params = {}) => {
  const response = await ApiService.get(baseUrl, { params });
  return response.data;
};

const getById = async (id) => {
  const response = await ApiService.get(`${baseUrl}/${id}`);
  return response.data;
};

const create = async (data) => {
  const formData = new FormData();
  Object.keys(data).forEach((key) => {
    if (data[key] !== undefined && data[key] !== null) {
      formData.append(key, data[key]);
    }
  });
  const response = await ApiServiceFile.post(baseUrl, formData);
  return response.data;
};

const update = async (id, data) => {
  const formData = new FormData();
  Object.keys(data).forEach((key) => {
    if (data[key] !== undefined && data[key] !== null) {
      formData.append(key, data[key]);
    }
  });
  formData.append('_method', 'PUT');
  const response = await ApiServiceFile.post(`${baseUrl}/${id}`, formData);
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

