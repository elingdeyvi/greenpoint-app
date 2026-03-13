import ApiService from "@/services/ApiService";

const baseUrl = "ventas/vigilante";

export const generarQrLocal = async (params) => {
  const response = await ApiService.post(`${baseUrl}/generar-qr-local`, params);
  return response.data;
};

export const validarQr = async (qrPayload) => {
  const response = await ApiService.post(`${baseUrl}/validar-qr`, {
    qr_payload: qrPayload,
  });
  return response.data;
};

