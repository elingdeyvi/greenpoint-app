<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: 'admin@greenpoint.com',
    password: 'admin123456',
    remember: true,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar sesión" />

        <p class="login-box-msg">Inicia sesión para continuar</p>

        <div v-if="status" class="alert alert-success py-2">{{ status }}</div>

        <form @submit.prevent="submit">
            <div class="input-group mb-3">
                <input
                    v-model="form.email"
                    type="email"
                    class="form-control"
                    placeholder="Email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <div class="input-group-text"><i class="fa-solid fa-envelope"></i></div>
            </div>
            <InputError class="mb-2" :message="form.errors.email" />

            <div class="input-group mb-3">
                <input
                    v-model="form.password"
                    type="password"
                    class="form-control"
                    placeholder="Contraseña"
                    required
                    autocomplete="current-password"
                />
                <div class="input-group-text"><i class="fa-solid fa-lock"></i></div>
            </div>
            <InputError class="mb-2" :message="form.errors.password" />

            <div class="row mb-3">
                <div class="col-8">
                    <div class="form-check">
                        <input
                            id="remember"
                            v-model="form.remember"
                            class="form-check-input"
                            type="checkbox"
                        />
                        <label class="form-check-label" for="remember">Recordarme</label>
                    </div>
                </div>
                <div class="col-4">
                    <button
                        type="submit"
                        class="btn btn-primary btn-block w-100"
                        :disabled="form.processing"
                    >
                        Entrar
                    </button>
                </div>
            </div>
        </form>

        <p class="mb-1" v-if="canResetPassword">
            <Link :href="route('password.request')">¿Olvidaste tu contraseña?</Link>
        </p>
        <p class="mb-0">
            <Link :href="route('register')" class="text-center">
                Registrar una cuenta nueva
            </Link>
        </p>

        <hr />
        <p class="mb-0 text-muted small text-center">
            Demo: <strong>admin@admin.com</strong> / <strong>password</strong>
        </p>
    </GuestLayout>
</template>
