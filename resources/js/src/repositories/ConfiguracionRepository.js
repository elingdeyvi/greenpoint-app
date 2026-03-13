import ApiService from '@/services/ApiService';

const baseUrl = 'configuracion';

const getConfig = async () => {
  const response = await ApiService.get(baseUrl);
  return response.data;
};

const updateConfig = async (items) => {
  const response = await ApiService.put(baseUrl, { items });
  return response.data;
};

export default {
  getConfig,
  updateConfig,
};

