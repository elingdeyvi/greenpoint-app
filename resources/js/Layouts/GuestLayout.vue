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
                    src="/images/greenpoint/logo.svg"
                    alt="GreenPoint"
                    height="56"
                    class="mb-3"
                />
                <h1>GreenPoint</h1>
                <p>Panel administrativo · Vue 3 + Laravel 12</p>
            </div>
            <div class="card card-outline card-success shadow">
                <div class="card-body login-card-body">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
