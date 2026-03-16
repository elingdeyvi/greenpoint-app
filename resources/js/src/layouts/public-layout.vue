<template>
  <div class="gp-site">
    <header
      class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm"
      role="banner"
    >
      <div class="container-fluid px-lg-4 px-xl-5">
        <router-link to="/" class="navbar-brand d-flex align-items-center">
          <img
            :src="logoInner"
            alt="GreenPoint"
            class="me-2"
            style="height: 40px"
          />
          <span class="fw-bold gp-text-primary">GreenPoint</span>
        </router-link>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#gpNav"
          aria-controls="gpNav"
          aria-expanded="false"
          aria-label="Alternar navegación"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <nav
          id="gpNav"
          class="collapse navbar-collapse"
          aria-label="Navegación principal del sitio"
        >
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0 text-uppercase fw-semibold">
            <li class="nav-item">
              <router-link to="/" class="nav-link" active-class="active" exact>
                Inicio
              </router-link>
            </li>

            <li class="nav-item dropdown">
              <button
                class="nav-link dropdown-toggle btn btn-link px-0"
                id="navNosotros"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                aria-haspopup="true"
              >
                Nosotros
              </button>
              <ul class="dropdown-menu" aria-labelledby="navNosotros">
                <li>
                  <router-link to="/nosotros" class="dropdown-item">
                    Quiénes somos
                  </router-link>
                </li>
                <li>
                  <router-link to="/historia" class="dropdown-item">
                    Historia
                  </router-link>
                </li>
                <li>
                  <router-link to="/aviso" class="dropdown-item">
                    Aviso de Privacidad
                  </router-link>
                </li>
              </ul>
            </li>

            <li class="nav-item">
              <router-link to="/servicios" class="nav-link" active-class="active">
                Servicios
              </router-link>
            </li>

            <li class="nav-item">
              <router-link to="/clientes" class="nav-link" active-class="active">
                Clientes
              </router-link>
            </li>

            <li class="nav-item">
              <router-link to="/galeria" class="nav-link" active-class="active">
                Galería
              </router-link>
            </li>

            <li class="nav-item">
              <router-link to="/tecnologia" class="nav-link" active-class="active">
                Tecnología
              </router-link>
            </li>

            <li class="nav-item dropdown">
              <button
                class="nav-link dropdown-toggle btn btn-link px-0"
                id="navContacto"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                aria-haspopup="true"
              >
                Contacto
              </button>
              <ul class="dropdown-menu" aria-labelledby="navContacto">
                <li>
                  <router-link to="/contacto/tabasco" class="dropdown-item">
                    Tabasco
                  </router-link>
                </li>
                <li>
                  <router-link to="/contacto/veracruz" class="dropdown-item">
                    Veracruz
                  </router-link>
                </li>
                <li>
                  <router-link to="/contacto/carmen" class="dropdown-item">
                    Cd. del Carmen
                  </router-link>
                </li>
              </ul>
            </li>
          </ul>

          <div class="d-flex ms-lg-3 mt-3 mt-lg-0 align-items-center">
            <a
              v-if="whatsappUrl"
              :href="whatsappUrl"
              target="_blank"
              rel="noopener"
              class="btn gp-btn-secondary btn-sm"
            >
              WhatsApp
            </a>
            <router-link
              v-else
              to="/contacto/tabasco"
              class="btn gp-btn-primary btn-sm"
            >
              Contacto
            </router-link>
          </div>
        </nav>
      </div>
    </header>

    <main>
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <footer class="bg-dark text-light py-5 mt-auto">
      <div class="container">
        <div class="row">
          <div class="col-md-4 mb-4 mb-md-0">
            <h5 class="gp-text-primary mb-3">GreenPoint</h5>
            <p class="small mb-0">
              {{ footerTexto || 'Proveedor de internet satelital, broadband, internet services México.' }}
            </p>
          </div>

          <div class="col-md-4 mb-4 mb-md-0">
            <h6 class="mb-3">Contacto</h6>
            <p class="small mb-1" v-if="telefonoGeneral">
              Teléfono: {{ telefonoGeneral }}
            </p>
            <p class="small mb-1" v-if="emailGeneral">
              <a :href="'mailto:' + emailGeneral" class="text-white-50">
                {{ emailGeneral }}
              </a>
            </p>
            <p class="small mb-0" v-if="direccionMatriz">
              {{ direccionMatriz }}
            </p>
          </div>

          <div class="col-md-4">
            <h6 class="mb-3">Enlaces</h6>
            <ul class="list-unstyled small mb-0">
              <li>
                <router-link
                  to="/servicios"
                  class="text-white-50 text-decoration-none"
                >
                  Servicios
                </router-link>
              </li>
              <li>
                <router-link
                  to="/nosotros"
                  class="text-white-50 text-decoration-none"
                >
                  Nosotros
                </router-link>
              </li>
              <li>
                <router-link
                  to="/aviso"
                  class="text-white-50 text-decoration-none"
                >
                  Aviso de Privacidad
                </router-link>
              </li>
              <li v-if="whatsappUrl">
                <a
                  :href="whatsappUrl"
                  target="_blank"
                  rel="noopener"
                  class="text-white-50 text-decoration-none"
                >
                  WhatsApp
                </a>
              </li>
            </ul>
          </div>
        </div>

        <hr class="border-secondary my-4" />

        <p class="small text-center text-white-50 mb-0">
          &copy; {{ new Date().getFullYear() }} GreenPoint. Todos los derechos reservados.
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import PublicSiteRepository from '@/repositories/PublicSiteRepository';
import logoInner from '@/assets/images/logo-inner.png';

const whatsappUrl = ref('');
const telefonoGeneral = ref('');
const emailGeneral = ref('');
const direccionMatriz = ref('');
const footerTexto = ref('');

onMounted(async () => {
  try {
    const map = await PublicSiteRepository.getConfiguracionPublic();

    if (map && typeof map === 'object') {
      whatsappUrl.value =
        map.whatsapp_url || 'https://api.whatsapp.com/send?phone=529933581890';
      telefonoGeneral.value = map.telefono_general || '';
      emailGeneral.value = map.email_general || '';
      direccionMatriz.value = map.direccion_matriz || '';
      footerTexto.value =
        map.footer_texto_empresa ||
        'Proveedor de internet satelital, broadband, internet services México.';
    } else {
      whatsappUrl.value =
        'https://api.whatsapp.com/send?phone=529933581890';
      footerTexto.value =
        'Proveedor de internet satelital, broadband, internet services México.';
    }
  } catch {
    whatsappUrl.value = 'https://api.whatsapp.com/send?phone=529933581890';
    footerTexto.value =
      'Proveedor de internet satelital, broadband, internet services México.';
  }
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.navbar-brand img {
  object-fit: contain;
}

.navbar-nav .nav-link {
  font-weight: 500;
  padding-inline: 0.85rem;
}

.navbar-nav .nav-link.active {
  color: var(--gp-primary);
}

.dropdown-menu {
  font-size: 0.9rem;
}
</style>
