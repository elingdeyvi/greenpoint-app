<script setup>
import AdminModal from '@/Components/Admin/AdminModal.vue';

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Confirmar eliminación',
    },
    message: {
        type: String,
        default: '¿Seguro que deseas eliminar este registro?',
    },
    processing: {
        type: Boolean,
        default: false,
    },
    confirmLabel: {
        type: String,
        default: 'Eliminar',
    },
});

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <AdminModal :show="show" :title="title" size="sm" @close="emit('close')">
        <p class="mb-0">{{ message }}</p>
        <template #footer>
            <button type="button" class="btn btn-secondary" :disabled="processing" @click="emit('close')">
                Cancelar
            </button>
            <button
                type="button"
                class="btn btn-danger"
                :disabled="processing"
                @click="emit('confirm')"
            >
                <span v-if="processing" class="spinner-border spinner-border-sm me-1" />
                {{ confirmLabel }}
            </button>
        </template>
    </AdminModal>
</template>
