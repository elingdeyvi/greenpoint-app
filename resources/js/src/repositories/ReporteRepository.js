import ApiService from "@/services/ApiService";

const baseUrl = "reportes";

export const getSalidas = async (params = {}) => {
  const response = await ApiService.get(`${baseUrl}/salidas`, { params });
  return response.data;
};

/** Estadísticas de reportes (boletos). Requiere sucursal tipo venta_almacen. */
export const getEstadisticas = async (params = {}) => {
  const response = await ApiService.get(`${baseUrl}/estadisticas`, { params });
  return response.data;
};

/** Estadísticas unificadas para el dashboard. Filtradas por sucursal activa. Devuelve { data: { perfil, boletos, ventas, donativos_total, alertas_inventario } }. */
export const getDashboardEstadisticas = async (params = {}) => {
  const response = await ApiService.get("dashboard/estadisticas", { params });
  return response.data;
};

export const exportarCsv = async (params = {}) => {
  const response = await ApiService.get(`${baseUrl}/exportar-csv`, { params });
  return response.data;
};

export const getEstadisticasVentas = async (params = {}) => {
  const response = await ApiService.get(`${baseUrl}/ventas/estadisticas`, { params });
  return response.data;
};

export const exportarVentasExcel = async (params = {}) => {
  const response = await ApiService.get(`${baseUrl}/ventas/exportar-excel`, { params });
  return response.data;
};

