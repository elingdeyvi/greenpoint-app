import { ref } from 'vue';
import PublicSiteRepository from '@/repositories/PublicSiteRepository';

export const usePublicAviso = () => {
  const pagina = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const fetchPagina = async () => {
    loading.value = true;
    error.value = null;
    try {
      pagina.value = await PublicSiteRepository.getPaginaAviso();
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  return {
    pagina,
    loading,
    error,
    fetchPagina,
  };
};

