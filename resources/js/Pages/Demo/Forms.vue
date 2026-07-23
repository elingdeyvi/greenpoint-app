<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    role: 'Usuario',
    bio: '',
    active: true,
});

const submit = () => {
    // Demo visual: no backend
    alert('Formulario de demostración listo para conectar a tu API/controlador.');
};
</script>

<template>
    <Head title="Formularios" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Formularios</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Formularios</li>
        </template>

        <div class="row">
            <div class="col-md-8">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Crear usuario</h3>
                    </div>
                    <form @submit.prevent="submit">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Nombre completo</label>
                                <input v-model="form.name" type="text" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input v-model="form.email" type="email" class="form-control" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Rol</label>
                                <select v-model="form.role" class="form-select">
                                    <option>Admin</option>
                                    <option>Editor</option>
                                    <option>Usuario</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Biografía</label>
                                <textarea v-model="form.bio" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="form-check">
                                <input
                                    id="active"
                                    v-model="form.active"
                                    class="form-check-input"
                                    type="checkbox"
                                />
                                <label class="form-check-label" for="active">Usuario activo</label>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Guardar</button>
                            <button type="reset" class="btn btn-secondary ms-2">Limpiar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title">Ayuda</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            Este formulario usa componentes Bootstrap 5 de AdminLTE y está listo
                            para conectar con un controlador Laravel.
                        </p>
                        <ul class="mb-0 ps-3">
                            <li>Validación con Form Requests</li>
                            <li>Inertia <code>useForm</code></li>
                            <li>Mensajes flash / toasts</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
