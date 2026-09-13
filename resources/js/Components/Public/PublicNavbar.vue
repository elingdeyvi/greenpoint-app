<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { usePublicSite } from '@/composables/usePublicSite';

const { sitioNombre, whatsappUrl, contactos, navServicios } = usePublicSite();

const collapsed = ref(true);
const scrolled = ref(false);
const openDropdown = ref(null);

const logoSrc = computed(() =>
    scrolled.value
        ? '/images/greenpoint/logo.png'
        : '/images/greenpoint/logo-inner.png',
);

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

/** cgi-bin/main.js: ≤50 → fixedHeader, >50 → scrollHeader */
const onScroll = () => {
    const y = window.scrollY || document.documentElement.scrollTop || 0;
    scrolled.value = y > 50;
};

onMounted(() => {
    // Estado inicial transparente (evita flash blanco / scroll restaurado)
    scrolled.value = false;
    window.addEventListener('scroll', onScroll, { passive: true });
    requestAnimationFrame(() => {
        onScroll();
    });
});

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
});
</script>

<template>
    <header
        class="gp-navbar header-style1 menu_area-light"
        :class="scrolled ? 'scrollHeader' : 'fixedHeader'"
    >
        <div class="navbar-default border-bottom border-color-light-white">
            <!-- Misma estructura que cgi-bin/index.html -->
            <div class="container-fluid px-lg-1-6 px-xl-2-5 px-xxl-2-9">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-12">
                        <div class="menu_area">
                            <nav class="navbar navbar-expand-lg navbar-light p-0">
                                <div class="navbar-header navbar-header-custom">
                                    <Link
                                        :href="route('public.home')"
                                        class="navbar-brand logochange"
                                        @click="closeMenus"
                                    >
                                        <img
                                            :src="logoSrc"
                                            :alt="sitioNombre"
                                            class="gp-brand-logo"
                                        />
                                    </Link>
                                </div>

                                <button
                                    class="navbar-toggler"
                                    type="button"
                                    aria-label="Abrir menú"
                                    :class="{ 'menu-opened': !collapsed }"
                                    @click="toggle"
                                >
                                    <span class="navbar-toggler-icon" aria-hidden="true"></span>
                                </button>

                                <!-- Misma estructura que cgi-bin: #nav y attr-nav hermanos (sin navbar-collapse) -->
                                <ul
                                    class="navbar-nav ms-auto"
                                    id="nav"
                                    :class="{ open: !collapsed }"
                                >
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

                                        <li
                                            class="nav-item dropdown has-sub"
                                            :class="{ active: openDropdown === 'nosotros' }"
                                        >
                                            <button
                                                type="button"
                                                class="nav-link gp-nav-toggle btn btn-link"
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
                                            <span
                                                class="submenu-button d-lg-none"
                                                aria-hidden="true"
                                                @click.stop="toggleDropdown('nosotros')"
                                            ></span>
                                            <ul
                                                class="dropdown-menu"
                                                :class="{ show: openDropdown === 'nosotros' }"
                                            >
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

                                        <li
                                            class="nav-item dropdown has-sub"
                                            :class="{ active: openDropdown === 'servicios' }"
                                        >
                                            <button
                                                type="button"
                                                class="nav-link gp-nav-toggle btn btn-link"
                                                :class="{
                                                    active: isActive('public.servicios'),
                                                    show: openDropdown === 'servicios',
                                                }"
                                                @click="toggleDropdown('servicios')"
                                            >
                                                Servicios
                                            </button>
                                            <span
                                                class="submenu-button d-lg-none"
                                                aria-hidden="true"
                                                @click.stop="toggleDropdown('servicios')"
                                            ></span>
                                            <ul
                                                class="dropdown-menu"
                                                :class="{ show: openDropdown === 'servicios' }"
                                            >
                                                <li v-for="servicio in navServicios" :key="servicio.id">
                                                    <Link
                                                        :href="route('public.servicios.show', servicio.id)"
                                                        class="dropdown-item"
                                                        @click="closeMenus"
                                                    >
                                                        {{ servicio.nombre }}
                                                    </Link>
                                                </li>
                                                <li v-if="!navServicios.length">
                                                    <Link
                                                        :href="route('public.servicios.index')"
                                                        class="dropdown-item"
                                                        @click="closeMenus"
                                                    >
                                                        Ver servicios
                                                    </Link>
                                                </li>
                                            </ul>
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
                                                Galeria
                                            </Link>
                                        </li>

                                        <li class="nav-item">
                                            <Link
                                                :href="route('public.tecnologia')"
                                                class="nav-link"
                                                :class="{ active: isActive('public.tecnologia') }"
                                                @click="closeMenus"
                                            >
                                                Tecnologia
                                            </Link>
                                        </li>

                                        <li
                                            class="nav-item dropdown has-sub"
                                            :class="{ active: openDropdown === 'contacto' }"
                                        >
                                            <button
                                                type="button"
                                                class="nav-link gp-nav-toggle btn btn-link"
                                                :class="{
                                                    active: isActive('public.contacto'),
                                                    show: openDropdown === 'contacto',
                                                }"
                                                @click="toggleDropdown('contacto')"
                                            >
                                                Contacto
                                            </button>
                                            <span
                                                class="submenu-button d-lg-none"
                                                aria-hidden="true"
                                                @click.stop="toggleDropdown('contacto')"
                                            ></span>
                                            <ul
                                                class="dropdown-menu"
                                                :class="{ show: openDropdown === 'contacto' }"
                                            >
                                                <li v-for="contacto in contactos" :key="contacto.id">
                                                    <Link
                                                        :href="`${route('public.contacto')}#oficina-${contacto.id}`"
                                                        class="dropdown-item"
                                                        @click="closeMenus"
                                                    >
                                                        {{ contacto.ubicacion }}
                                                    </Link>
                                                </li>
                                                <li v-if="!contactos.length">
                                                    <Link
                                                        :href="route('public.contacto')"
                                                        class="dropdown-item"
                                                        @click="closeMenus"
                                                    >
                                                        Contacto
                                                    </Link>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>

                                <!-- WhatsApp derecha; d-none d-xl = oculto en móvil/tablet (como prod) -->
                                <div
                                    v-if="whatsappUrl"
                                    class="attr-nav align-items-lg-center ms-lg-auto"
                                >
                                    <ul>
                                        <li class="d-none d-xl-inline-block">
                                            <a
                                                :href="whatsappUrl"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="butn text-white sm"
                                                title="Envíenos su consulta"
                                            >
                                                <span>WhatsApp</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
