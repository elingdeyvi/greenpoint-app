<script setup>
</script>

<template>
    <div class="login-page">
        <div class="login-box">
            <div class="auth-brand">
                <h1><i class="fa-solid fa-leaf me-2"></i>GreenPoint</h1>
                <p>Panel administrativo · Laravel 12 + Vue + AdminLTE</p>
            </div>
            <div class="card card-outline card-primary shadow">
                <div class="card-body login-card-body">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
