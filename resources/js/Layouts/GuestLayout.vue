<script setup>
import { onMounted } from 'vue';

onMounted(() => {
    document.body.className = 'login-page';
});
</script>

<template>
    <div class="login-page">
        <div class="login-box">
            <div class="auth-brand text-center">
                <img
                    src="/images/greenpoint/logo.png"
                    alt="GreenPoint"
                    height="56"
                    class="mb-3"
                />
                <p class="mb-0 text-muted">Panel administrativo</p>
            </div>
            <div class="card card-outline card-success shadow">
                <div class="card-body login-card-body">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
