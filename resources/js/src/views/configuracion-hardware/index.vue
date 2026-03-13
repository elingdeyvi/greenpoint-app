<template>
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="panel p-3">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-sm-12">
                            <div class="text-center titulo-dorado">
                                <h3 class="pull-left">Configuración de Hardware</h3>
                                <button class="btn btn-black pull-right m-1" @click="refrescar">Refrescar</button>
                                <button class="btn btn-black pull-right m-1" data-bs-toggle="modal" data-bs-target="#registerModal" @click="initializeCreate">Nuevo</button>
                            </div>
                        </div>
                    </div>
                    <div class="custom-table">
                        <v-client-table :data="hardware" :columns="columns" :options="table_option">
                            <template #tipo="item">
                                <span :class="item.row.tipo === 'camara_ip' ? 'badge bg-primary' : 'badge bg-info'">
                                    {{ item.row.tipo === 'camara_ip' ? 'Cámara IP' : 'Impresora' }}
                                </span>
                            </template>
                            <template #activo="item">
                                <span :class="item.row.activo ? 'badge bg-success' : 'badge bg-danger'">
                                    {{ item.row.activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </template>
                            <template #actions="item">
                                <a v-if="item.row.tipo === 'camara_ip'" href="javascript:void(0);" title="Probar video" @click="probarVideo(item.row.id)" class="me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-video"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                                </a>
                                <a href="javascript:void(0);" title="Editar" @click="getHardware(item.row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2">
                                        <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                    </svg>
                                </a>
                                <a href="javascript:void(0);" title="Eliminar" @click="deleteItem(item.row.id)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </a>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="registerModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header titulo-dorado">
                        <h4 class="modal-title">{{ editedIndex === -1 ? "Nueva" : "Editar" }} Configuración</h4>
                        <button type="button" data-bs-dismiss="modal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.stop.prevent="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tipo *</label>
                                        <select class="form-control mb-2" v-model="form.tipo" :class="[is_entrada ? (!errors.tipo ? 'is-valid' : 'is-invalid') : '']">
                                            <option value="">Seleccione...</option>
                                            <option value="camara_ip">Cámara IP</option>
                                            <option value="impresora">Impresora</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            <li v-for="(item, index) in errors.tipo" :key="index">{{ item }}</li>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre *</label>
                                        <input type="text" class="form-control mb-2" placeholder="Nombre" v-model="form.nombre" :class="[is_entrada ? (!errors.nombre ? 'is-valid' : 'is-invalid') : '']" />
                                        <div class="invalid-feedback">
                                            <li v-for="(item, index) in errors.nombre" :key="index">{{ item }}</li>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>IP/URL</label>
                                        <input type="text" class="form-control mb-2" placeholder="192.168.1.100" v-model="form.ip_url" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Puerto</label>
                                        <input type="number" class="form-control mb-2" placeholder="80" v-model="form.puerto" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Usuario</label>
                                        <input type="text" class="form-control mb-2" placeholder="Usuario" v-model="form.usuario" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contraseña</label>
                                        <input type="password" class="form-control mb-2" placeholder="Contraseña" v-model="form.password" />
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="form.tipo === 'camara_ip'">
                                    <div class="form-group">
                                        <label>URL Snapshot o RTSP</label>
                                        <input type="text" class="form-control mb-2" placeholder="rtsp://usuario:password@ip:554/Streaming/Channels/101" v-model="form.url_snapshot" />
                                        <small class="form-text text-muted">HTTP: http://.../snapshot.cgi. HiLook NVR: use RTSP completo (rtsp://...) y el sistema capturará la foto desde el video con FFmpeg.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Modelo</label>
                                        <input type="text" class="form-control mb-2" placeholder="Modelo" v-model="form.modelo" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select class="form-control mb-2" v-model="form.activo">
                                            <option :value="true">Activo</option>
                                            <option :value="false">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-success w-100" v-show="editedIndex === -1" @click="createHardware">Guardar</button>
                                    <button type="button" class="btn btn-default w-100" v-show="editedIndex !== -1" @click="updateHardware">Actualizar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">¿Estás seguro de eliminar esta configuración?</h5>
                        <button type="button" data-bs-dismiss="modal" class="btn-close"></button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-black" @click="deleteHardware()">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" />
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useMeta } from "@/composables/use-meta";
import * as ConfiguracionHardwareRepository from "@/repositories/ConfiguracionHardwareRepository";
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import axios from "axios";

useMeta({ title: 'Configuración de Hardware' });

const hardware = ref([]);
let registerModal = ref(false);
let deleteModal = ref(false);
const is_entrada = ref(false);
const editedIndex = ref(-1);
const errors = ref({});
const form = ref({
    tipo: '',
    nombre: '',
    ip_url: '',
    puerto: null,
    usuario: '',
    password: '',
    url_snapshot: '',
    modelo: '',
    activo: true,
});
const isLoading = ref(true);

const columns = ref(["id", "tipo", "nombre", "ip_url", "puerto", "modelo", "activo", "actions"]);
const table_option = ref({
    headings: {
        id: () => "ID",
        tipo: () => "Tipo",
        nombre: () => "Nombre",
        ip_url: () => "IP/URL",
        puerto: () => "Puerto",
        modelo: () => "Modelo",
        activo: () => "Estado",
        actions: () => "",
    },
    perPage: 20,
    perPageValues: [20, 50, 100],
    skin: "table table-hover",
    columnsClasses: { actions: "actions text-center" },
    sortable: ["id", "nombre", "tipo"],
    pagination: { nav: "scroll", chunk: 5 },
    texts: {
        count: "Mostrando {from} a {to} de {count}",
        filter: "",
        filterPlaceholder: "Buscar...",
        limit: "Resultados:",
        noResults: "No hay registros"
    },
});

const createHardware = async () => {
    try {
        isLoading.value = true;
        is_entrada.value = true;
        await ConfiguracionHardwareRepository.createHardware(form.value);
        showMessage("Guardado correctamente");
        initializeCreate();
        close();
        initialize();
    } catch (error) {
        if (axios.isAxiosError(error))
            errors.value = error.response.data.errors;
        isLoading.value = false;
    }
};

const updateHardware = async () => {
    try {
        isLoading.value = true;
        is_entrada.value = true;
        await ConfiguracionHardwareRepository.updateHardware(editedIndex.value, form.value);
        showMessage("Actualizado correctamente");
        close();
        initializeCreate();
        initialize();
    } catch (error) {
        if (axios.isAxiosError(error))
            errors.value = error.response.data.errors;
        isLoading.value = false;
    }
};

const getHardware = async (id) => {
    try {
        const response = await ConfiguracionHardwareRepository.getHardwareById(id);
        form.value = {
            tipo: response.data.tipo,
            nombre: response.data.nombre,
            ip_url: response.data.ip_url,
            puerto: response.data.puerto,
            usuario: response.data.usuario,
            password: response.data.password,
            url_snapshot: response.data.url_snapshot,
            modelo: response.data.modelo,
            activo: response.data.activo,
        };
        errors.value = {};
        editedIndex.value = id;
        registerModal.show();
    } catch (error) {
        console.log(error);
    }
};

const deleteItem = (id) => {
    editedIndex.value = id;
    deleteModal.show();
};

const deleteHardware = async () => {
    try {
        isLoading.value = true;
        await ConfiguracionHardwareRepository.deleteHardware(editedIndex.value);
        showMessage("Eliminado correctamente");
        closeDelete();
        initialize();
    } catch (error) {
        console.log(error);
        isLoading.value = false;
    }
};

const probandoVideo = ref(false);
const probarVideo = async (id) => {
    try {
        probandoVideo.value = true;
        const res = await ConfiguracionHardwareRepository.probarVideo(id);
        showMessage(res.mensaje || (res.accesible ? "El video es accesible." : "No se pudo acceder al video."), res.accesible ? "success" : "warning");
    } catch (error) {
        showMessage(axios.isAxiosError(error) ? (error.response?.data?.message || error.message) : "Error al probar el video", "error");
    } finally {
        probandoVideo.value = false;
    }
};

const close = () => {
    registerModal.hide();
    editedIndex.value = -1;
};

const closeDelete = () => {
    deleteModal.hide();
    initializeCreate();
};

const initializeCreate = () => {
    form.value = {
        tipo: '',
        nombre: '',
        ip_url: '',
        puerto: null,
        usuario: '',
        password: '',
        url_snapshot: '',
        modelo: '',
        activo: true,
    };
    errors.value = {};
    editedIndex.value = -1;
};

const initialize = async () => {
    isLoading.value = true;
    try {
        const response = await ConfiguracionHardwareRepository.getAll();
        hardware.value = response.data || [];
    } catch (error) {
        console.log(error);
    } finally {
        isLoading.value = false;
    }
};

const refrescar = () => {
    initialize();
    showMessage("Completado correctamente");
};

const showMessage = (msg = "", type = "success") => {
    const toast = window.Swal.mixin({
        toast: true,
        position: "top",
        showConfirmButton: false,
        timer: 3000,
    });
    toast.fire({
        icon: type,
        title: msg,
        padding: "10px 20px",
    });
};

onMounted(async () => {
    registerModal = new window.bootstrap.Modal(document.getElementById("registerModal"));
    deleteModal = new window.bootstrap.Modal(document.getElementById("deleteModal"));
    await initialize();
});
</script>

