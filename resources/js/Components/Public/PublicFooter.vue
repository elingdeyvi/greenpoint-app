<script setup>
import { Link } from '@inertiajs/vue3';
import { usePublicSite } from '@/composables/usePublicSite';

const {
    sitioNombre,
    empresaDescripcion,
    telefonoPrincipal,
    emailPrincipal,
    direccionMatriz,
    redesSociales,
    horarioLunesViernes,
    horarioSabado,
    horarioDomingo,
} = usePublicSite();

const year = new Date().getFullYear();
</script>

<template>
    <footer class="gp-footer">
        <div class="container gp-footer-top">
            <div class="row gy-4 align-items-center">
                <div class="col-sm-6 col-lg-3">
                    <Link :href="route('public.home')" class="d-inline-flex align-items-center gap-2">
                        <img
                            src="/images/greenpoint/logo.svg"
                            :alt="sitioNombre"
                            height="40"
                            style="filter: brightness(0) invert(1);"
                        />
                        <span class="text-white fw-bold fs-5">{{ sitioNombre }}</span>
                    </Link>
                </div>
                <div v-if="telefonoPrincipal" class="col-sm-6 col-lg-3">
                    <div class="gp-footer-contact-item">
                        <i class="fas fa-phone-alt text-white fs-2 mt-1"></i>
                        <div class="borders-start">
                            <h5 class="h6 text-white mb-1">Teléfono</h5>
                            <a :href="`tel:${telefonoPrincipal.replace(/[^\d+]/g, '')}`" class="text-white opacity9">
                                {{ telefonoPrincipal }}
                            </a>
                        </div>
                    </div>
                </div>
                <div v-if="emailPrincipal" class="col-sm-6 col-lg-3">
                    <div class="gp-footer-contact-item">
                        <i class="far fa-envelope-open text-white fs-2 mt-1"></i>
                        <div class="borders-start">
                            <h5 class="h6 text-white mb-1">Email</h5>
                            <a :href="`mailto:${emailPrincipal}`" class="text-white opacity9">{{ emailPrincipal }}</a>
                        </div>
                    </div>
                </div>
                <div v-if="direccionMatriz" class="col-sm-6 col-lg-3">
                    <div class="gp-footer-contact-item">
                        <i class="fas fa-map-marker-alt text-white fs-2 mt-1"></i>
                        <div class="borders-start">
                            <h5 class="h6 text-white mb-1">Matriz</h5>
                            <span class="text-white opacity9">{{ direccionMatriz }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container gp-footer-main">
            <div class="row gy-4">
                <div class="col-sm-6 col-lg-3">
                    <h4 class="gp-footer-title"><span>|</span>Empresa</h4>
                    <p v-if="empresaDescripcion" class="text-white opacity9 mb-0">
                        {{ empresaDescripcion }}
                    </p>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <h4 class="gp-footer-title"><span>|</span>Nosotros</h4>
                    <ul class="footer-list-style">
                        <li><Link :href="route('public.nosotros')">Quiénes Somos</Link></li>
                        <li><Link :href="route('public.historia')">Historia {{ sitioNombre }}</Link></li>
                        <li><Link :href="route('public.clientes')">Algunos Clientes</Link></li>
                        <li><Link :href="route('public.aviso')">Aviso de Privacidad</Link></li>
                    </ul>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <h4 class="gp-footer-title"><span>|</span>Servicios</h4>
                    <ul class="footer-list-style">
                        <li><Link :href="route('public.servicios.index')">Soluciones</Link></li>
                        <li><Link :href="route('public.tecnologia')">Tecnología Innovadora</Link></li>
                        <li><Link :href="route('public.galeria')">Proyectos</Link></li>
                        <li><Link :href="route('public.contacto')">Contacto</Link></li>
                    </ul>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <h4 class="gp-footer-title"><span>|</span>Horarios</h4>
                    <ul class="footer-hour-list">
                        <li v-if="horarioLunesViernes">
                            <i class="fa-regular fa-clock"></i>
                            <div>
                                <h4 class="h6 fw-medium text-white mb-1">Lunes - Viernes</h4>
                                <p class="mb-0 text-white opacity9">{{ horarioLunesViernes }}</p>
                            </div>
                        </li>
                        <li v-if="horarioSabado">
                            <i class="fa-regular fa-clock"></i>
                            <div>
                                <h4 class="h6 fw-medium text-white mb-1">Sábados</h4>
                                <p class="mb-0 text-white opacity9">{{ horarioSabado }}</p>
                            </div>
                        </li>
                        <li v-if="horarioDomingo">
                            <i class="fa-regular fa-clock"></i>
                            <div>
                                <h4 class="h6 fw-medium text-white mb-1">Domingo</h4>
                                <p class="mb-0 text-white opacity9">{{ horarioDomingo }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="gp-footer-bar">
            <div class="container">
                <div class="row align-items-center gy-3">
                    <div class="col-md-6 text-center text-md-start order-2 order-md-1">
                        <p class="d-inline-block text-white mb-0 small">
                            &copy; {{ year }} Derechos Reservados
                            <span class="text-gp-primary">{{ sitioNombre }}</span>
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end order-1 order-md-2">
                        <template v-if="redesSociales.length">
                            <p class="text-white d-inline-block fw-semibold mb-0 align-middle me-3">
                                Redes Sociales:
                            </p>
                            <ul class="footer-social-style">
                                <li v-for="red in redesSociales" :key="red.nombre">
                                    <a
                                        :href="red.url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        :title="red.nombre"
                                    >
                                        <i :class="red.icono || 'fa-solid fa-link'"></i>
                                    </a>
                                </li>
                            </ul>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>
