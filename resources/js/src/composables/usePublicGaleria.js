import { ref } from 'vue';
import PublicSiteRepository from '@/repositories/PublicSiteRepository';

export const usePublicGaleria = () => {
  const items = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const fetchGaleria = async () => {
    loading.value = true;
    error.value = null;
    try {
      items.value = await PublicSiteRepository.getGaleria();
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  return {
    items,
    loading,
    error,
    fetchGaleria,
  };
};

