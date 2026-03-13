import ApiService from '@/services/ApiService';
import ApiServiceFile from '@/services/ApiServiceFile';

const baseUrl = 'pagina-nosotros';

const getPage = async () => {
  const response = await ApiService.get(baseUrl);
  return response.data;
};

const updatePage = async (data) => {
  // Comentario: Si el payload es FormData (por manejo de imágenes), usar ApiServiceFile.
  if (data instanceof FormData) {
    data.append('_method', 'PUT');
    const response = await ApiServiceFile.post(baseUrl, data);
    return response.data;
  }

  const response = await ApiService.put(baseUrl, data);
  return response.data;
};

export default {
  getPage,
  updatePage,
};

