<template>
  <div>
    <!-- Banner / Carrusel -->
    <section class="bg-dark position-relative overflow-hidden" style="min-height: 70vh;">
      <div v-if="loading" class="position-absolute top-50 start-50 translate-middle text-white">
        <div class="spinner-border gp-text-primary" role="status"><span class="visually-hidden">Cargando…</span></div>
      </div>
      <div v-else-if="banners.length" id="gpBannerCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel">
        <div class="carousel-indicators">
          <button v-for="(_, i) in banners" :key="i" type="button" :data-bs-target="'#gpBannerCarousel'" :data-bs-slide-to="i" :class="{ active: i === 0 }" :aria-label="'Slide ' + (i + 1)"></button>
        </div>
        <div class="carousel-inner h-100">
          <div v-for="(banner, i) in banners" :key="banner.id" class="carousel-item h-100" :class="{ active: i === 0 }">
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark" style="opacity: 0.5;"></div>
            <img v-if="banner.imagen" :src="storageUrl(banner.imagen)" class="d-block w-100 h-100 object-fit-cover" :alt="banner.titulo || 'Banner'">
            <div class="carousel-caption position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center">
              <div class="container">
                <div class="row">
                  <div class="col-md-10 col-lg-8">
                    <h1 class="display-5 fw-bold text-white mb-3">{{ banner.titulo }}</h1>
                    <p v-if="banner.descripcion" class="lead text-white-50 mb-4">{{ banner.descripcion }}</p>
                    <router-link v-if="banner.enlace" :to="banner.enlace" class="btn gp-btn-primary me-2">Ver más</router-link>
                    <router-link to="/contacto/tabasco" class="btn gp-btn-secondary">Contacto</router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#gpBannerCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#gpBannerCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Siguiente</span>
        </button>
      </div>
      <div v-else class="position-absolute top-50 start-50 translate-middle text-center text-white">
        <h1 class="display-5 fw-bold">GreenPoint</h1>
        <p class="lead">Comunicaciones para el sector petrolero</p>
        <router-link to="/nosotros" class="btn gp-btn-primary me-2">Nosotros</router-link>
        <router-link to="/contacto/tabasco" class="btn gp-btn-secondary">Contacto</router-link>
      </div>
    </section>

    <!-- Servicios destacados -->
    <section class="py-5">
      <div class="container">
        <div class="text-center mb-5">
          <span class="text-uppercase small gp-text-primary fw-bold">GreenPoint</span>
          <h2 class="h1 mb-3">Internet Satelital <span class="fw-normal">— Expertos en comunicaciones</span></h2>
        </div>
        <div v-if="loading && !servicios.length" class="text-center py-5"><div class="spinner-border gp-text-primary" role="status"></div></div>
        <div v-else class="row g-4">
          <div v-for="s in servicios" :key="s.id" class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 rounded-3 overflow-hidden">
              <img v-if="s.imagen" :src="storageUrl(s.imagen)" class="card-img-top object-fit-cover" style="height: 200px;" :alt="s.nombre">
              <div class="card-body">
                <h5 class="card-title">
                  <router-link :to="servicioLink(s)" class="text-dark text-decoration-none">{{ s.nombre }}</router-link>
                </h5>
                <p class="card-text text-muted small">{{ (s.descripcion || '').slice(0, 120) }}{{ (s.descripcion || '').length > 120 ? '…' : '' }}</p>
                <router-link :to="servicioLink(s)" class="gp-link small fw-semibold">Ver más →</router-link>
              </div>
            </div>
          </div>
        </div>
        <div v-if="!loading && servicios.length" class="text-center mt-4">
          <router-link to="/servicios" class="btn gp-btn-primary">Ver todos los servicios</router-link>
        </div>
      </div>
    </section>

    <!-- CTA Nosotros -->
    <section class="py-5 gp-bg-primary">
      <div class="container text-center text-white">
        <h3 class="h2 mb-3">¿Quieres conocernos?</h3>
        <p class="lead mb-4">Somos líderes en comunicaciones para el sector petrolero.</p>
        <router-link to="/nosotros" class="btn btn-light">Sobre nosotros</router-link>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { usePublicHome } from '@/composables/usePublicHome';
import { usePublicMeta } from '@/composables/use-public-meta';

usePublicMeta({
  title: 'Inicio',
  description: 'GreenPoint - Proveedor de internet satelital, broadband, internet services México. Líder en comunicaciones para el sector petrolero.',
  keywords: 'internet satelital, broadband, comunicaciones, sector petrolero, México',
});

const baseStorage = () => (import.meta.env.VITE_API_URL || '').replace(/\/api\/?$/, '') || window.location.origin;
const storageUrl = (path) => (path && !path.startsWith('http') ? `${baseStorage()}/storage/${path}` : path) || '';

const { banners, servicios, loading, fetchHome } = usePublicHome();

const servicioLink = (s) => (s.slug ? { name: 'PublicServicioDetalle', params: { idOrSlug: s.slug } } : { name: 'PublicServicioDetalle', params: { idOrSlug: String(s.id) } });

onMounted(() => fetchHome());
</script>
