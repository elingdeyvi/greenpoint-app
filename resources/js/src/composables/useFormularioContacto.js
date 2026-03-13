import { ref } from 'vue';
import FormularioContactoRepository from '@/repositories/FormularioContactoRepository';

export const useFormularioContacto = () => {
  const items = ref([]);
  const currentItem = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const fetchList = async (params = {}) => {
    loading.value = true;
    error.value = null;
    try {
      const data = await FormularioContactoRepository.getAll(params);
      items.value = data;
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  const fetchById = async (id) => {
    loading.value = true;
    error.value = null;
    try {
      const data = await FormularioContactoRepository.getById(id);
      currentItem.value = data;
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  const markAsRead = async (id, read = true) => {
    loading.value = true;
    error.value = null;
    try {
      const updated = await FormularioContactoRepository.markAsRead(id, read);
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
    currentItem,
    loading,
    error,
    fetchList,
    fetchById,
    markAsRead,
  };
};

