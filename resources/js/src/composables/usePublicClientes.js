import { ref } from 'vue';
import PublicSiteRepository from '@/repositories/PublicSiteRepository';

export const usePublicClientes = () => {
  const clientes = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const fetchClientes = async () => {
    loading.value = true;
    error.value = null;
    try {
      clientes.value = await PublicSiteRepository.getClientes();
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  return {
    clientes,
    loading,
    error,
    fetchClientes,
  };
};

