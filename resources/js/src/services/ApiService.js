import axios from "axios";
const API_BASE_URI = import.meta.env.VITE_API_URL || '/api';
const instance = axios.create({
  baseURL: API_BASE_URI,
});
instance.defaults.headers.post["Content-Type"] = "application/json";
instance.interceptors.request.use(
  (config) => {
    const token = window.localStorage.getItem("token");
    const cleanToken = token && token !== 'undefined' && token !== 'null' ? token : '';
    if (config.headers) {
      if (cleanToken) config.headers["Authorization"] = `Bearer ${cleanToken}`;
      else delete config.headers["Authorization"];
    }
    return config;
  },
  (error) => Promise.reject(error)
);
export default instance;
