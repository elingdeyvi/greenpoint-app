<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
        default: '',
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Recuperar contraseña" />

        <p class="login-box-msg">
            Indica tu correo y te enviaremos un enlace para restablecer la
            contraseña.
        </p>

        <div v-if="status" class="alert alert-success py-2">{{ status }}</div>

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

            <div class="row mb-3">
                <div class="col-12">
                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                        :disabled="form.processing"
                    >
                        Enviar enlace
                    </button>
                </div>
            </div>
        </form>

        <p class="mb-0">
            <Link :href="route('login')">Volver al inicio de sesión</Link>
        </p>
    </GuestLayout>
</template>
