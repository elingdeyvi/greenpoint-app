<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirmar contraseña" />

        <p class="login-box-msg">
            Área segura. Confirma tu contraseña para continuar.
        </p>

        <form @submit.prevent="submit">
            <div class="input-group mb-3">
                <input
                    v-model="form.password"
                    type="password"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.password }"
                    placeholder="Contraseña"
                    required
                    autofocus
                    autocomplete="current-password"
                />
                <div class="input-group-text">
                    <i class="fa-solid fa-lock"></i>
                </div>
            </div>
            <InputError class="mb-2" :message="form.errors.password" />

            <button
                type="submit"
                class="btn btn-primary w-100"
                :disabled="form.processing"
            >
                Confirmar
            </button>
        </form>
    </GuestLayout>
</template>
