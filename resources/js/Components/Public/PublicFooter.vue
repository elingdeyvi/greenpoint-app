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
    <!-- cgi-bin: footer.pt-0.bg-dark -->
    <footer class="pt-0 bg-dark gp-footer">
        <div class="container border-bottom border-color-light-white py-2-5 py-md-6 mb-6 mb-md-8 mb-lg-10">
            <div class="row justify-content-center mt-n1-9 align-items-center">
                <div class="col-sm-6 col-lg-3 mt-2-2">
                    <Link :href="route('public.home')">
                        <img
                            src="/images/greenpoint/footer-light-logo.png"
                            :alt="sitioNombre"
                            class="gp-footer-logo"
                        />
                    </Link>
                </div>
                <div v-if="telefonoPrincipal" class="col-sm-6 col-lg-3 mt-2-2">
                    <div class="d-flex">
                        <div class="flex-shrink-0 mt-2">
                            <i class="fas fa-phone-alt text-white fs-2 d-block"></i>
                        </div>
                        <div class="flex-grow-1 borders-start border-color-light-white ps-4 ms-3">
                            <h5 class="h6 text-white">Telefono</h5>
                            <a
                                :href="`tel:${telefonoPrincipal.replace(/[^\d+]/g, '')}`"
                                class="text-white opacity9 display-30"
                            >
                                {{ telefonoPrincipal }}
                            </a>
                        </div>
                    </div>
                </div>
                <div v-if="emailPrincipal" class="col-sm-6 col-lg-3 mt-2-2">
                    <div class="d-flex">
                        <div class="flex-shrink-0 mt-2">
                            <i class="far fa-envelope-open text-white fs-2 d-block"></i>
                        </div>
                        <div class="flex-grow-1 borders-start border-color-light-white ps-4 ms-3">
                            <h5 class="h6 text-white">Email</h5>
                            <a :href="`mailto:${emailPrincipal}`" class="text-white opacity9 display-30">
                                {{ emailPrincipal }}
                            </a>
                        </div>
                    </div>
                </div>
                <div v-if="direccionMatriz" class="col-sm-6 col-lg-3 mt-2-2">
                    <div class="d-flex">
                        <div class="flex-shrink-0 mt-2">
                            <i class="fas fa-map-marker-alt text-white fs-2 d-block"></i>
                        </div>
                        <div class="flex-grow-1 borders-start border-color-light-white ps-4 ms-3">
                            <h5 class="h6 text-white">Matriz</h5>
                            <span class="text-white opacity9 display-30">{{ direccionMatriz }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row mt-n2-6">
                <div class="col-sm-6 col-lg-3 mt-2-6">
                    <h4 class="h5 text-white mb-1-9"><span class="me-2">|</span>Empresa</h4>
                    <p v-if="empresaDescripcion" class="text-white mb-4 opacity9">
                        {{ empresaDescripcion }}
                    </p>
                </div>

                <div class="col-sm-6 col-lg-3 mt-2-6">
                    <div class="ps-sm-2-5">
                        <h4 class="h5 text-white mb-1-9"><span class="me-2">|</span>Nosotros</h4>
                        <ul class="footer-list-style">
                            <li><Link :href="route('public.nosotros')">Quienes Somos</Link></li>
                            <li>
                                <Link :href="route('public.historia')">Historia {{ sitioNombre }}</Link>
                            </li>
                            <li><Link :href="route('public.clientes')">Algunos Clientes</Link></li>
                            <li><Link :href="route('public.aviso')">Aviso de Privacidad</Link></li>
                        </ul>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mt-2-6">
                    <div class="ps-lg-2-5">
                        <h4 class="h5 text-white mb-1-9"><span class="me-2">|</span>Servicios</h4>
                        <ul class="footer-list-style">
                            <li>
                                <Link :href="route('public.servicios.index')">Conexion Satelital</Link>
                            </li>
                            <li>
                                <Link :href="route('public.servicios.index')">Soluciones</Link>
                            </li>
                            <li>
                                <Link :href="route('public.tecnologia')">Tecnologia Innovadora</Link>
                            </li>
                            <li><Link :href="route('public.galeria')">Proyectos</Link></li>
                        </ul>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mt-2-6">
                    <div class="ps-sm-2-5">
                        <h4 class="h5 text-white mb-1-9"><span class="me-2">|</span>Horarios</h4>
                        <ul class="footer-hour-list">
                            <li v-if="horarioLunesViernes">
                                <i class="ti-time text-white"></i>
                                <div>
                                    <h4 class="h6 font-weight-500 text-white">Lunes - Viernes</h4>
                                    <p class="mb-0 text-white display-30">{{ horarioLunesViernes }}</p>
                                </div>
                            </li>
                            <li v-if="horarioSabado">
                                <i class="ti-time text-white"></i>
                                <div>
                                    <h4 class="h6 font-weight-500 text-white">Sabados</h4>
                                    <p class="mb-0 text-white display-30">{{ horarioSabado }}</p>
                                </div>
                            </li>
                            <li v-if="horarioDomingo">
                                <i class="ti-time text-white"></i>
                                <div>
                                    <h4 class="h6 font-weight-500 text-white">Domingo</h4>
                                    <p class="mb-0 text-white display-30">{{ horarioDomingo }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bar">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mt-3 mt-md-0 order-2 order-md-1">
                        <p class="d-inline-block text-white mb-0 display-30 display-lg-29">
                            &copy; {{ year }} Derechos Reservados
                            <span class="text-primary white-hover">{{ sitioNombre }}</span>
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end order-1 order-md-2">
                        <template v-if="redesSociales.length">
                            <p class="text-white d-inline-block font-weight-600 mb-0 align-middle me-3">
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
                                        <i :class="red.icono || 'fab fa-link'"></i>
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
