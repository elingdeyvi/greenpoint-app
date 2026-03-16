<template>
  <div class="gp-auth-page">
    <div class="gp-auth-card">
      <header class="gp-auth-header">
        <div class="gp-auth-logo">GreenPoint</div>
        <p class="gp-auth-subtitle">Acceso al panel administrativo</p>
      </header>

      <form novalidate ref="formComponent" class="gp-auth-form" @submit.stop.prevent="login">
        <div class="gp-field" :class="{ 'has-error': is_login && errors.email }">
          <label for="email" class="gp-label">Usuario</label>
          <div class="gp-input-wrapper">
            <span class="gp-input-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </span>
            <input
              id="email"
              v-model="form.email"
              type="text"
              class="gp-input"
              autocomplete="username"
              placeholder="Correo o usuario"
            />
          </div>
          <ul v-if="is_login && errors.email" class="gp-error-list">
            <li v-for="(item, index) in errors.email" :key="index">{{ item }}</li>
          </ul>
        </div>

        <div class="gp-field" :class="{ 'has-error': is_login && errors.password }">
          <label for="password" class="gp-label">Contraseña</label>
          <div class="gp-input-wrapper">
            <span class="gp-input-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </span>
            <input
              id="password"
              v-model="form.password"
              :type="passwordField"
              class="gp-input"
              autocomplete="current-password"
              placeholder="Ingresa tu contraseña"
            />
            <button type="button" class="gp-toggle-pass" @click="set_pwd_type">
              {{ passwordField === 'password' ? 'Mostrar' : 'Ocultar' }}
            </button>
          </div>
          <ul v-if="is_login && errors.password" class="gp-error-list">
            <li v-for="(item, index) in errors.password" :key="index">{{ item }}</li>
          </ul>
        </div>

        <div class="gp-actions">
          <router-link to="/auth/pass-recovery-boxed" class="gp-link">¿Olvidaste tu contraseña?</router-link>
          <button type="submit" class="gp-btn-primary" :disabled="isLoading">
            <span v-if="!isLoading">Iniciar sesión</span>
            <span v-else>Cargando…</span>
          </button>
        </div>
      </form>

      <footer class="gp-auth-footer">
        <p class="gp-auth-copy">© {{ currentYear }} GreenPoint. Todos los derechos reservados.</p>
      </footer>
    </div>

    <div class="gp-auth-background"></div>

    <loading
      v-model:active="isLoading"
      :can-cancel="false"
      :is-full-page="true"
    />
  </div>
</template>

<script setup>
    import "../../assets/sass/authentication/auth.scss";
    import { ref, onMounted, computed, watch } from "vue";
    import * as AuthRepository from "@/repositories/AuthRepository";
    import { useMeta } from "../../composables/use-meta";
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/css/index.css';
    useMeta({ title: "Iniciar sesión" });

    const passwordField = ref("password");
    const form = ref({
        email: null,
        password: null,
    });
    const formComponent = ref(null);
    const is_login = ref(false);
    const errors = ref({});
    const isLoading = ref(false);
    const currentYear = new Date().getFullYear();

    const set_pwd_type = () => {
        passwordField.value= passwordField.value==="password"?"text":"password";
    };

    const login = async () => {
        is_login.value = true;
        isLoading.value = true;
        try {
            const response = await AuthRepository.createTokens(form.value);
            if (!response.success) return (errors.value = response.error);
            errors.value ={
                email: null,
                password: null,
            }
            localStorage.setItem("token", response.token);
            // store.commit('setLayout', 'app');
            window.location.href = window.location.origin + window.location.pathname + '?nocache=' + Date.now();
        } catch (error) {
            console.log(error);
        } finally {
            isLoading.value = false;
        }
    };

    onMounted(() => {
        isLoading.value = false;
    });
</script>
