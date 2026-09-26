<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
        default: '',
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Verificar correo" />

        <p class="login-box-msg">
            Gracias por registrarte. Revisa tu correo y haz clic en el enlace
            de verificación. Si no llegó, puedes reenviarlo.
        </p>

        <div v-if="verificationLinkSent" class="alert alert-success py-2">
            Se envió un nuevo enlace de verificación a tu correo.
        </div>

        <form @submit.prevent="submit" class="mb-3">
            <button
                type="submit"
                class="btn btn-primary w-100"
                :disabled="form.processing"
            >
                Reenviar correo de verificación
            </button>
        </form>

        <p class="mb-0 text-center">
            <Link :href="route('logout')" method="post" as="button" class="btn btn-link">
                Cerrar sesión
            </Link>
        </p>
    </GuestLayout>
</template>
