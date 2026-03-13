import ApiService from '@/services/ApiService';

const baseUrl = 'formularios-contacto';

const getAll = async (params = {}) => {
  const response = await ApiService.get(baseUrl, { params });
  return response.data;
};

const getById = async (id) => {
  const response = await ApiService.get(`${baseUrl}/${id}`);
  return response.data;
};

const markAsRead = async (id, read = true) => {
  const response = await ApiService.put(`${baseUrl}/${id}`, {
    leido: read,
  });
  return response.data;
};

export default {
  getAll,
  getById,
  markAsRead,
};

