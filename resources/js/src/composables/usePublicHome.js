import { ref } from 'vue';
import PublicSiteRepository from '@/repositories/PublicSiteRepository';

export const usePublicHome = () => {
  const banners = ref([]);
  const servicios = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const fetchHome = async () => {
    loading.value = true;
    error.value = null;
    try {
      const data = await PublicSiteRepository.getHome();
      banners.value = data.banners || [];
      servicios.value = data.servicios || [];
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  return {
    banners,
    servicios,
    loading,
    error,
    fetchHome,
  };
};

