<script setup>
import AdminModal from '@/Components/Admin/AdminModal.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <div>
        <p class="text-muted small mb-3">
            Al eliminar tu cuenta se borrarán de forma permanente tus datos de
            acceso. Esta acción no se puede deshacer.
        </p>

        <button
            type="button"
            class="btn btn-outline-danger"
            @click="confirmUserDeletion"
        >
            <i class="fa-solid fa-trash me-1"></i>
            Eliminar mi cuenta
        </button>

        <AdminModal
            :show="confirmingUserDeletion"
            title="Confirmar eliminación"
            size="md"
            @close="closeModal"
        >
            <p class="mb-3">
                ¿Seguro que deseas eliminar tu cuenta? Escribe tu contraseña para
                confirmar.
            </p>

            <div class="mb-0">
                <label for="delete-password" class="form-label">Contraseña</label>
                <input
                    id="delete-password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.password }"
                    placeholder="Tu contraseña"
                    @keyup.enter="deleteUser"
                />
                <div v-if="form.errors.password" class="invalid-feedback">
                    {{ form.errors.password }}
                </div>
            </div>

            <template #footer>
                <button type="button" class="btn btn-secondary" @click="closeModal">
                    Cancelar
                </button>
                <button
                    type="button"
                    class="btn btn-danger"
                    :disabled="form.processing"
                    @click="deleteUser"
                >
                    <span
                        v-if="form.processing"
                        class="spinner-border spinner-border-sm me-1"
                    />
                    Eliminar cuenta
                </button>
            </template>
        </AdminModal>
    </div>
</template>
