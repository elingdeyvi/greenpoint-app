import { ref } from 'vue';
import BannerRepository from '@/repositories/BannerRepository';

export const useBanner = () => {
  const items = ref([]);
  const currentItem = ref(null);
  const loading = ref(false);
  const error = ref(null);

  const fetchList = async (params = {}) => {
    loading.value = true;
    error.value = null;
    try {
      const data = await BannerRepository.getAll(params);
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
      const data = await BannerRepository.getById(id);
      currentItem.value = data;
    } catch (e) {
      error.value = e;
    } finally {
      loading.value = false;
    }
  };

  const create = async (data) => {
    loading.value = true;
    error.value = null;
    try {
      const created = await BannerRepository.create(data);
      return created;
    } catch (e) {
      error.value = e;
      throw e;
    } finally {
      loading.value = false;
    }
  };

  const update = async (id, data) => {
    loading.value = true;
    error.value = null;
    try {
      const updated = await BannerRepository.update(id, data);
      return updated;
    } catch (e) {
      error.value = e;
      throw e;
    } finally {
      loading.value = false;
    }
  };

  const remove = async (id) => {
    loading.value = true;
    error.value = null;
    try {
      await BannerRepository.delete(id);
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
    create,
    update,
    deleteItem: remove,
  };
};

