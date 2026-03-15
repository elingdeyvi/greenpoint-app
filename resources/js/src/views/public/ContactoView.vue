<template>
  <div class="py-5">
    <div class="container">
      <div class="text-center mb-5">
        <span class="text-uppercase small gp-text-primary fw-bold">Contacto</span>
        <h1 class="h2">Contacto — {{ tituloUbicacion }}</h1>
      </div>
      <div class="row">
        <div class="col-lg-5 mb-4 mb-lg-0">
          <div v-if="contacto" class="card border-0 shadow-sm rounded-3 p-4">
            <h3 class="h6 gp-text-primary mb-3">{{ contacto.ubicacion }}</h3>
            <p v-if="contacto.direccion" class="mb-2"><strong>Dirección:</strong> {{ contacto.direccion }}</p>
            <p v-if="contacto.telefono" class="mb-2"><strong>Teléfono:</strong> <a :href="'tel:' + contacto.telefono" class="gp-link">{{ contacto.telefono }}</a></p>
            <p v-if="contacto.email" class="mb-2"><strong>Email:</strong> <a :href="'mailto:' + contacto.email" class="gp-link">{{ contacto.email }}</a></p>
            <iframe v-if="contacto.mapa_url" :src="contacto.mapa_url" class="w-100 mt-3 rounded" style="height: 250px; border: 0;" allowfullscreen loading="lazy"></iframe>
          </div>
          <div v-else class="card border-0 shadow-sm rounded-3 p-4">
            <p class="text-muted mb-0">Cargando información de contacto…</p>
          </div>
        </div>
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm rounded-3 p-4">
            <h3 class="h6 gp-text-primary mb-3">Envíanos un mensaje</h3>
            <form @submit.prevent="enviar">
              <div class="mb-3">
                <label class="form-label">Nombre <span class="text-danger">*</span></label>
                <input v-model="form.nombre" type="text" class="form-control" required maxlength="255">
                <span v-if="errors.nombre" class="small text-danger">{{ errors.nombre }}</span>
              </div>
              <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input v-model="form.email" type="email" class="form-control" required>
                <span v-if="errors.email" class="small text-danger">{{ errors.email }}</span>
              </div>
              <div class="mb-3">
                <label class="form-label">Teléfono</label>
                <input v-model="form.telefono" type="text" class="form-control" maxlength="50">
              </div>
              <div class="mb-3">
                <label class="form-label">Asunto</label>
                <input v-model="form.asunto" type="text" class="form-control" maxlength="255">
              </div>
              <div class="mb-4">
                <label class="form-label">Mensaje <span class="text-danger">*</span></label>
                <textarea v-model="form.mensaje" class="form-control" rows="4" required></textarea>
                <span v-if="errors.mensaje" class="small text-danger">{{ errors.mensaje }}</span>
              </div>
              <div v-if="success" class="alert alert-success small">Mensaje enviado correctamente.</div>
              <div v-if="submitError" class="alert alert-danger small">{{ submitError }}</div>
              <button type="submit" class="btn gp-btn-primary" :disabled="sending">Enviar</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { usePublicContactos } from '@/composables/usePublicContactos';
import { usePublicMeta } from '@/composables/use-public-meta';
import PublicSiteRepository from '@/repositories/PublicSiteRepository';

const route = useRoute();
const slug = computed(() => route.meta.contactoSlug || 'tabasco');
const titulos = { tabasco: 'Tabasco', veracruz: 'Veracruz', carmen: 'Cd. del Carmen' };
const tituloUbicacion = computed(() => titulos[slug.value] || slug.value);

usePublicMeta(computed(() => ({ title: `Contacto — ${tituloUbicacion.value}`, description: `Contacte a GreenPoint en ${tituloUbicacion.value}. Dirección, teléfono y formulario de contacto.` })));

const { contactos, loading, fetchContactos } = usePublicContactos();
const contacto = computed(() => {
  const list = contactos.value || [];
  const u = (slug.value || '').toLowerCase();
  return list.find(c => (c.ubicacion || '').toLowerCase().includes(u) || (c.ubicacion || '').toLowerCase() === u) || list[0];
});

const form = ref({ nombre: '', email: '', telefono: '', asunto: '', mensaje: '' });
const errors = ref({});
const success = ref(false);
const submitError = ref('');
const sending = ref(false);

const enviar = async () => {
  errors.value = {};
  submitError.value = '';
  if (!form.value.nombre?.trim()) errors.value.nombre = 'El nombre es obligatorio.';
  if (!form.value.email?.trim()) errors.value.email = 'El email es obligatorio.';
  if (!form.value.mensaje?.trim()) errors.value.mensaje = 'El mensaje es obligatorio.';
  if (Object.keys(errors.value).length) return;

  sending.value = true;
  try {
    await PublicSiteRepository.sendContacto({
      nombre: form.value.nombre.trim(),
      email: form.value.email.trim(),
      telefono: form.value.telefono?.trim() || null,
      asunto: form.value.asunto?.trim() || null,
      mensaje: form.value.mensaje.trim(),
    });
    success.value = true;
    form.value = { nombre: '', email: '', telefono: '', asunto: '', mensaje: '' };
  } catch (e) {
    submitError.value = e?.response?.data?.message || 'No se pudo enviar el mensaje. Intente de nuevo.';
  } finally {
    sending.value = false;
  }
};

onMounted(() => fetchContactos());
</script>
