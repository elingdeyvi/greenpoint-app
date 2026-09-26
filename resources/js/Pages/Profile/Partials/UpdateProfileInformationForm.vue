<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
        default: false,
    },
    status: {
        type: String,
        default: '',
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <form @submit.prevent="form.patch(route('profile.update'))">
        <p class="text-muted small mb-3">
            Actualiza tu nombre y correo electrónico de la cuenta.
        </p>

        <div class="mb-3">
            <label for="profile-name" class="form-label">Nombre</label>
            <input
                id="profile-name"
                v-model="form.name"
                type="text"
                class="form-control"
                :class="{ 'is-invalid': form.errors.name }"
                required
                autofocus
                autocomplete="name"
            />
            <div v-if="form.errors.name" class="invalid-feedback">
                {{ form.errors.name }}
            </div>
        </div>

        <div class="mb-3">
            <label for="profile-email" class="form-label">Email</label>
            <input
                id="profile-email"
                v-model="form.email"
                type="email"
                class="form-control"
                :class="{ 'is-invalid': form.errors.email }"
                required
                autocomplete="username"
            />
            <div v-if="form.errors.email" class="invalid-feedback">
                {{ form.errors.email }}
            </div>
        </div>

        <div
            v-if="mustVerifyEmail && user.email_verified_at === null"
            class="alert alert-warning"
        >
            <p class="mb-2">Tu correo aún no está verificado.</p>
            <Link
                :href="route('verification.send')"
                method="post"
                as="button"
                class="btn btn-sm btn-outline-warning"
            >
                Reenviar correo de verificación
            </Link>
            <div
                v-if="status === 'verification-link-sent'"
                class="mt-2 small text-success"
            >
                Se envió un nuevo enlace de verificación a tu correo.
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button
                type="submit"
                class="btn btn-primary"
                :disabled="form.processing"
            >
                <span
                    v-if="form.processing"
                    class="spinner-border spinner-border-sm me-1"
                />
                Guardar cambios
            </button>
            <span v-if="form.recentlySuccessful" class="text-success small">
                Guardado.
            </span>
        </div>
    </form>
</template>
