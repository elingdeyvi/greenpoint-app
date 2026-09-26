<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Nueva contraseña" />

        <p class="login-box-msg">Elige una nueva contraseña para tu cuenta</p>

        <form @submit.prevent="submit">
            <div class="input-group mb-3">
                <input
                    v-model="form.email"
                    type="email"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.email }"
                    placeholder="Email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <div class="input-group-text">
                    <i class="fa-solid fa-envelope"></i>
                </div>
            </div>
            <InputError class="mb-2" :message="form.errors.email" />

            <div class="input-group mb-3">
                <input
                    v-model="form.password"
                    type="password"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.password }"
                    placeholder="Nueva contraseña"
                    required
                    autocomplete="new-password"
                />
                <div class="input-group-text">
                    <i class="fa-solid fa-lock"></i>
                </div>
            </div>
            <InputError class="mb-2" :message="form.errors.password" />

            <div class="input-group mb-3">
                <input
                    v-model="form.password_confirmation"
                    type="password"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.password_confirmation }"
                    placeholder="Confirmar contraseña"
                    required
                    autocomplete="new-password"
                />
                <div class="input-group-text">
                    <i class="fa-solid fa-lock"></i>
                </div>
            </div>
            <InputError
                class="mb-2"
                :message="form.errors.password_confirmation"
            />

            <button
                type="submit"
                class="btn btn-primary w-100"
                :disabled="form.processing"
            >
                Guardar contraseña
            </button>
        </form>
    </GuestLayout>
</template>
