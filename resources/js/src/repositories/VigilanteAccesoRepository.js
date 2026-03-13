import ApiServiceFile from "@/services/ApiServiceFile";

const baseUrl = "entregas";

export const registrarAcceso = async (formData) => {
  const response = await ApiServiceFile.post(`${baseUrl}/registrar-acceso`, formData);
  return response.data;
};

