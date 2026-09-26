<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { usePermissions } from '@/composables/usePermissions';

const props = defineProps({
    active: {
        type: String,
        default: '',
    },
});

const { can } = usePermissions();

const tabs = computed(() =>
    [
        {
            key: 'home',
            label: 'Inicio',
            icon: 'fa-house',
            route: 'admin.paginas.home.edit',
            permission: 'modulos.home',
        },
        {
            key: 'nosotros',
            label: 'Nosotros',
            icon: 'fa-users',
            route: 'admin.paginas.nosotros.edit',
            permission: 'modulos.nosotros',
        },
        {
            key: 'historia',
            label: 'Historia',
            icon: 'fa-clock-rotate-left',
            route: 'admin.paginas.historia.edit',
            permission: 'modulos.historia',
        },
        {
            key: 'tecnologia',
            label: 'Tecnología',
            icon: 'fa-microchip',
            route: 'admin.paginas.tecnologia.edit',
            permission: 'modulos.tecnologia',
        },
        {
            key: 'aviso',
            label: 'Aviso',
            icon: 'fa-file-shield',
            route: 'admin.paginas.aviso.edit',
            permission: 'modulos.aviso',
        },
    ].filter((tab) => can(tab.permission)),
);
</script>

<template>
    <ul v-if="tabs.length" class="nav nav-tabs mb-3 flex-nowrap overflow-auto">
        <li v-for="tab in tabs" :key="tab.key" class="nav-item text-nowrap">
            <Link
                :href="route(tab.route)"
                class="nav-link"
                :class="{ active: active === tab.key || route().current(tab.route) }"
            >
                <i class="fa-solid me-1" :class="tab.icon"></i>
                {{ tab.label }}
            </Link>
        </li>
    </ul>
</template>
