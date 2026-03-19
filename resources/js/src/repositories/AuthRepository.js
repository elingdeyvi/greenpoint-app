import ApiService from "@/services/ApiService";

const baseUrl = "tokens";

export const createTokens = async (params) => {
  try {
    const { data } = await ApiService.post(`${baseUrl}/create`, params);
    // Backend devuelve { token, user } en 200,
    // pero mantenemos fallback por compatibilidad si en algún caso viene anidado.
    const token =
      data?.token ??
      data?.data?.token ??
      null;
    const user =
      data?.user ??
      data?.data?.user ??
      null;
    return {
      token,
      user,
    };
  } catch (error) {
    // Repropagar para que el caller maneje errores de validación (422) o auth (401)
    throw error;
  }
};


export const expireTokens = async (idToken) => {
  try {
    const response = await ApiService.delete(`${baseUrl}/${idToken}`);
    return {
      token: response.data.data,
      success: true,
    };
  } catch (error) {
    return {
      error: error.response.data.errors,
      success: false,
    };
  }
};

export const permissions = async () => {
  try {
    const response = await ApiService.get(`${baseUrl}/permissions`);
    return response.data || { all: false, permissions: [] };
  } catch (error) {
    console.error('Error fetching permissions:', error);
    // Propagar para que el caller pueda limpiar sesión si aplica (401)
    throw error;
  }
};

export const changePw = async (params) => {
  try {
    const response = await ApiService.post(`${baseUrl}/pw`, params);
    return {
      token: response.data.data,
      success: true,
    };
  } catch (error) {
    return {
      error: error.response.data.errors,
      success: false,
    };
  }
};

export const savePw = async (params) => {
    const response = await ApiService.post(`${baseUrl}/savepw`, params);
    return response.data.data;
};

export const getPwUuid = async (params) => {
  const response = await ApiService.post(`${baseUrl}/getpwuuid`, params);
  return response.data.data;
};