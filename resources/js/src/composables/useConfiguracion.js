import { ref } from 'vue';
import ConfiguracionRepository from '@/repositories/ConfiguracionRepository';

export const useConfiguracion = () => {
  const items = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const fetchConfig = async () => {
    loading.value = true;
    error.value = null;
    try {
      const data = await ConfiguracionRepository.getConfig();
      items.value = Array.isArray(data) ? data : (data?.data ?? data?.items ?? []);
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  const updateConfig = async (itemsToUpdate) => {
    loading.value = true;
    error.value = null;
    try {
      const updated = await ConfiguracionRepository.updateConfig(itemsToUpdate);
      return updated;
    } catch (e) {
      error.value = e;
      throw e;
    } finally {
      loading.value = false;
    }
  };

  return {
    items,
    loading,
    error,
    fetchConfig,
    updateConfig,
  };
};
