import ApiService from '@/services/ApiService';

const baseUrl = 'public';

const getHome = async () => {
  const response = await ApiService.get(`${baseUrl}/home`);
  return response.data;
};

const getServicios = async () => {
  const response = await ApiService.get(`${baseUrl}/servicios`);
  return response.data;
};

const getServicioById = async (id) => {
  const response = await ApiService.get(`${baseUrl}/servicios/${id}`);
  return response.data;
};

const getClientes = async () => {
  const response = await ApiService.get(`${baseUrl}/clientes`);
  return response.data;
};

const getGaleria = async () => {
  const response = await ApiService.get(`${baseUrl}/galeria`);
  return response.data;
};

const getContactos = async () => {
  const response = await ApiService.get(`${baseUrl}/contactos`);
  return response.data;
};

const getPaginaNosotros = async () => {
  const response = await ApiService.get(`${baseUrl}/pagina-nosotros`);
  return response.data;
};

const getPaginaHistoria = async () => {
  const response = await ApiService.get(`${baseUrl}/pagina-historia`);
  return response.data;
};

const getPaginaTecnologia = async () => {
  const response = await ApiService.get(`${baseUrl}/pagina-tecnologia`);
  return response.data;
};

const getPaginaAviso = async () => {
  const response = await ApiService.get(`${baseUrl}/pagina-aviso`);
  return response.data;
};

const sendContacto = async (data) => {
  const response = await ApiService.post(`${baseUrl}/formulario-contacto`, data);
  return response.data;
};

export default {
  getHome,
  getServicios,
  getServicioById,
  getClientes,
  getGaleria,
  getContactos,
  getPaginaNosotros,
  getPaginaHistoria,
  getPaginaTecnologia,
  getPaginaAviso,
  sendContacto,
};

