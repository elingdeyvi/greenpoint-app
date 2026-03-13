import { ref } from 'vue';
import PublicSiteRepository from '@/repositories/PublicSiteRepository';

export const usePublicContactos = () => {
  const contactos = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const fetchContactos = async () => {
    loading.value = true;
    error.value = null;
    try {
      contactos.value = await PublicSiteRepository.getContactos();
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  return {
    contactos,
    loading,
    error,
    fetchContactos,
  };
};

