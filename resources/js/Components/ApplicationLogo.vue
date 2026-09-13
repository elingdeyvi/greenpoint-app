<script setup>
/**
 * Logo GreenPoint (Vue 3) — usado en auth / Breeze.
 */
defineProps({
    height: {
        type: [Number, String],
        default: 48,
    },
});
</script>

<template>
    <img
        src="/images/greenpoint/logo.png"
        alt="GreenPoint"
        :height="height"
        class="gp-application-logo"
    />
</template>
