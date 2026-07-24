<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { usePublicSite } from '@/composables/usePublicSite';

const { sitioNombre, whatsappUrl } = usePublicSite();

const collapsed = ref(true);
const scrolled = ref(false);
const openDropdown = ref(null);

const isActive = (name) => route().current(name) || route().current(`${name}.*`);

const toggle = () => {
    collapsed.value = !collapsed.value;
};

const toggleDropdown = (key) => {
    openDropdown.value = openDropdown.value === key ? null : key;
};

const closeMenus = () => {
    collapsed.value = true;
    openDropdown.value = null;
};

const onScroll = () => {
    scrolled.value = window.scrollY > 40;
};

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
});
</script>

<template>
    <header class="gp-navbar" :class="{ 'is-scrolled': scrolled }">
        <nav class="navbar navbar-expand-lg py-2">
            <div class="container-fluid px-3 px-lg-4 px-xl-5">
                <Link :href="route('public.home')" class="navbar-brand" @click="closeMenus">
                    <img
                        src="/images/greenpoint/logo.svg"
                        :alt="sitioNombre"
                        class="gp-brand-logo"
                    />
                    <span class="d-none d-sm-inline">{{ sitioNombre }}</span>
                </Link>

                <button
                    class="navbar-toggler"
                    type="button"
                    aria-label="Abrir menú"
                    @click="toggle"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="navbar-collapse" :class="{ collapse: collapsed, show: !collapsed }">
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item">
                            <Link
                                :href="route('public.home')"
                                class="nav-link"
                                :class="{ active: isActive('public.home') }"
                                @click="closeMenus"
                            >
                                Inicio
                            </Link>
                        </li>

                        <li class="nav-item dropdown">
                            <button
                                type="button"
                                class="nav-link dropdown-toggle btn btn-link"
                                :class="{
                                    active:
                                        isActive('public.nosotros') ||
                                        isActive('public.historia') ||
                                        isActive('public.aviso'),
                                    show: openDropdown === 'nosotros',
                                }"
                                @click="toggleDropdown('nosotros')"
                            >
                                Nosotros
                            </button>
                            <ul class="dropdown-menu" :class="{ show: openDropdown === 'nosotros' }">
                                <li>
                                    <Link
                                        :href="route('public.nosotros')"
                                        class="dropdown-item"
                                        :class="{ active: isActive('public.nosotros') }"
                                        @click="closeMenus"
                                    >
                                        Quiénes Somos
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        :href="route('public.historia')"
                                        class="dropdown-item"
                                        :class="{ active: isActive('public.historia') }"
                                        @click="closeMenus"
                                    >
                                        Historia
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        :href="route('public.aviso')"
                                        class="dropdown-item"
                                        :class="{ active: isActive('public.aviso') }"
                                        @click="closeMenus"
                                    >
                                        Aviso de Privacidad
                                    </Link>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <Link
                                :href="route('public.servicios.index')"
                                class="nav-link"
                                :class="{ active: isActive('public.servicios') }"
                                @click="closeMenus"
                            >
                                Servicios
                            </Link>
                        </li>

                        <li class="nav-item">
                            <Link
                                :href="route('public.clientes')"
                                class="nav-link"
                                :class="{ active: isActive('public.clientes') }"
                                @click="closeMenus"
                            >
                                Clientes
                            </Link>
                        </li>

                        <li class="nav-item">
                            <Link
                                :href="route('public.galeria')"
                                class="nav-link"
                                :class="{ active: isActive('public.galeria') }"
                                @click="closeMenus"
                            >
                                Galería
                            </Link>
                        </li>

                        <li class="nav-item">
                            <Link
                                :href="route('public.tecnologia')"
                                class="nav-link"
                                :class="{ active: isActive('public.tecnologia') }"
                                @click="closeMenus"
                            >
                                Tecnología
                            </Link>
                        </li>

                        <li class="nav-item">
                            <Link
                                :href="route('public.contacto')"
                                class="nav-link"
                                :class="{ active: isActive('public.contacto') }"
                                @click="closeMenus"
                            >
                                Contacto
                            </Link>
                        </li>

                        <li v-if="whatsappUrl" class="nav-item ms-lg-3 mt-2 mt-lg-0">
                            <a
                                :href="whatsappUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="butn sm text-white"
                                title="Envíenos su consulta"
                            >
                                <span>WhatsApp</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</template>
