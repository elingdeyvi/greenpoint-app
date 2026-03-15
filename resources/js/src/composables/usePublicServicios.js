import { ref } from 'vue';
import PublicSiteRepository from '@/repositories/PublicSiteRepository';

export const usePublicServicios = () => {
  const servicios = ref([]);
  const servicio = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const fetchServicios = async () => {
    loading.value = true;
    error.value = null;
    try {
      servicios.value = await PublicSiteRepository.getServicios();
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  const fetchServicio = async (id) => {
    loading.value = true;
    error.value = null;
    try {
      servicio.value = await PublicSiteRepository.getServicioById(id);
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  const fetchServicioBySlug = async (slug) => {
    loading.value = true;
    error.value = null;
    try {
      servicio.value = await PublicSiteRepository.getServicioBySlug(slug);
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  return {
    servicios,
    servicio,
    loading,
    error,
    fetchServicios,
    fetchServicio,
    fetchServicioBySlug,
  };
};

