import { ref } from 'vue';
import PublicSiteRepository from '@/repositories/PublicSiteRepository';

export const usePublicHistoria = () => {
  const pagina = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const fetchPagina = async () => {
    loading.value = true;
    error.value = null;
    try {
      pagina.value = await PublicSiteRepository.getPaginaHistoria();
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

