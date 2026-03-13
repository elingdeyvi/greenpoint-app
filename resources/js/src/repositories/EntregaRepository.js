import ApiService from "@/services/ApiService";

const baseUrl = "entregas";

export const getEntregas = async (params = {}) => {
  const response = await ApiService.get(baseUrl, { params });
  return response.data;
};

export const registrarEntrega = async (formData) => {
  const response = await ApiService.post(`${baseUrl}/registrar`, formData, {
    headers: { "Content-Type": "multipart/form-data" },
  });
  return response.data;
};
