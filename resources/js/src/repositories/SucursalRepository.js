import ApiService from "@/services/ApiService";

const baseUrl = "sucursales";

export const getSucursales = async () => {
  const response = await ApiService.get(baseUrl);
  return response.data;
};
