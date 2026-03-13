import ApiService from "@/services/ApiService";

const baseUrl = "marcas";

export const getAll = async (params = {}) => {
  const response = await ApiService.get(`${baseUrl}`, { params });
  return response.data;
};

export const createMarca = async (params) => {
  const response = await ApiService.post(`${baseUrl}`, params, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  });
  return response.data;
};

export const getMarcaById = async (id) => {
  const response = await ApiService.get(`${baseUrl}/${id}`);
  return response.data;
};

export const updateMarca = async (id, params) => {
  const response = await ApiService.post(`${baseUrl}/${id}`, params, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  });
  return response.data;
};

export const deleteMarca = async (id) => {
  const response = await ApiService.delete(`${baseUrl}/${id}`);
  return response.data;
};

export const uploadLogo = async (id, logo) => {
  const formData = new FormData();
  formData.append('logo', logo);
  
  const response = await ApiService.post(`${baseUrl}/${id}/upload-logo`, formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  });
  return response.data;
};

export const deleteLogo = async (id) => {
  const response = await ApiService.delete(`${baseUrl}/${id}/delete-logo`);
  return response.data;
};

