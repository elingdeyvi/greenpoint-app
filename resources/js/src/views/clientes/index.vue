<template>
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="panel p-3">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-sm-12">
                            <div class="text-center titulo-dorado">
                                <h3 class="pull-left">Clientes</h3>
                                <button class="btn btn-black pull-right m-1" @click="refrescar">Refrescar</button>
                                <button v-if="canAdmin" class="btn btn-black pull-right m-1" data-bs-toggle="modal" data-bs-target="#registerModal" @click="initializeCreate">Nuevo</button>
                            </div>
                        </div>
                    </div>
                    <div class="custom-table">
                        <v-client-table :data="clientes" :columns="columns" :options="table_option">
                            <template #es_mostrador="item">
                                <span v-if="item.row.es_mostrador" class="badge bg-info">Mostrador</span>
                                <span v-else class="text-muted">—</span>
                            </template>
                            <template #activo="item">
                                <span :class="item.row.activo ? 'badge bg-success' : 'badge bg-danger'">
                                    {{ item.row.activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </template>
                            <template #actions="item">
                                <span v-if="canAdmin && !item.row.es_mostrador">
                                    <a href="javascript:void(0);" title="Editar" @click="getCliente(item.row.id)" class="me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                    </a>
                                    <a href="javascript:void(0);" title="Eliminar" @click="deleteItem(item.row.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </a>
                                </span>
                            </template>
                        </v-client-table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="registerModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header titulo-dorado">
                        <h4 class="modal-title">{{ editedIndex === -1 ? 'Nuevo' : 'Editar' }} Cliente</h4>
                        <button type="button" data-bs-dismiss="modal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.stop.prevent="">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Nombre *</label>
                                        <input type="text" class="form-control mb-2" placeholder="Nombre del cliente" v-model="form.nombre" :class="[is_entrada ? (!errors.nombre ? 'is-valid' : 'is-invalid') : '']" :readonly="form.es_mostrador" />
                                        <div class="invalid-feedback"><li v-for="(item, index) in errors.nombre" :key="index">{{ item }}</li></div>
                                    </div>
                                </div>
                                <div class="col-md-12" v-if="editedIndex !== -1 && !form.es_mostrador">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select class="form-control mb-2" v-model="form.activo">
                                            <option :value="true">Activo</option>
                                            <option :value="false">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-success w-100" v-show="editedIndex === -1" @click="createCliente">Guardar</button>
                                    <button type="button" class="btn btn-default w-100" v-show="editedIndex !== -1 && !form.es_mostrador" @click="updateCliente">Actualizar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">¿Eliminar este cliente?</h5>
                        <button type="button" data-bs-dismiss="modal" class="btn-close"></button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-black" @click="deleteCliente">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <loading v-model:active="isLoading" :can-cancel="false" :is-full-page="true" />
    </div>
</template>

<script setup>
import { onMounted, ref, computed } from "vue";
import { useMeta } from "@/composables/use-meta";
import { usePermissions } from "@/composables/use-permissions";
import * as ClienteRepository from "@/repositories/ClienteRepository";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
import axios from "axios";

useMeta({ title: "Clientes" });

const { hasPermission } = usePermissions();
const canAdmin = computed(() => hasPermission("clientes.administrar"));

const clientes = ref([]);
let registerModal = null;
let deleteModal = null;
const is_entrada = ref(false);
const editedIndex = ref(-1);
const errors = ref({});
const form = ref({
    nombre: "",
    activo: true,
    es_mostrador: false,
});
const isLoading = ref(true);

const columns = ref(["id", "nombre", "es_mostrador", "activo", "actions"]);
const table_option = ref({
    headings: {
        id: () => "ID",
        nombre: () => "Nombre",
        es_mostrador: () => "Tipo",
        activo: () => "Estado",
        actions: () => "",
    },
    perPage: 20,
    perPageValues: [20, 50, 100],
    skin: "table table-hover",
    columnsClasses: { actions: "text-center" },
    sortable: ["id", "nombre"],
    pagination: { nav: "scroll", chunk: 5 },
    texts: { count: "Mostrando {from} a {to} de {count}", filter: "", filterPlaceholder: "Buscar...", limit: "Resultados:", noResults: "No hay registros" },
});

const createCliente = async () => {
    try {
        isLoading.value = true;
        is_entrada.value = true;
        await ClienteRepository.createCliente({ nombre: form.value.nombre });
        showMessage("Cliente creado correctamente");
        initializeCreate();
        registerModal.hide();
        initialize();
    } catch (error) {
        if (axios.isAxiosError(error)) errors.value = error.response?.data?.errors || {};
        isLoading.value = false;
    }
};

const updateCliente = async () => {
    try {
        isLoading.value = true;
        is_entrada.value = true;
        await ClienteRepository.updateCliente(editedIndex.value, { nombre: form.value.nombre, activo: form.value.activo });
        showMessage("Cliente actualizado correctamente");
        initializeCreate();
        registerModal.hide();
        initialize();
    } catch (error) {
        if (axios.isAxiosError(error)) errors.value = error.response?.data?.errors || {};
        isLoading.value = false;
    }
};

const getCliente = async (id) => {
    try {
        const res = await ClienteRepository.getClienteById(id);
        const d = res.data || res;
        form.value = {
            nombre: d.nombre,
            activo: d.activo !== false,
            es_mostrador: d.es_mostrador === true,
        };
        errors.value = {};
        editedIndex.value = id;
        registerModal.show();
    } catch (error) {
        console.error(error);
    }
};

const deleteItem = (id) => {
    editedIndex.value = id;
    deleteModal.show();
};

const deleteCliente = async () => {
    try {
        isLoading.value = true;
        await ClienteRepository.deleteCliente(editedIndex.value);
        showMessage("Cliente eliminado correctamente");
        deleteModal.hide();
        initialize();
    } catch (error) {
        if (axios.isAxiosError(error)) showMessage(error.response?.data?.errors?.cliente?.[0] || "Error al eliminar", "error");
        isLoading.value = false;
    }
};

const initializeCreate = () => {
    form.value = { nombre: "", activo: true, es_mostrador: false };
    errors.value = {};
    editedIndex.value = -1;
};

const initialize = async () => {
    isLoading.value = true;
    try {
        const response = await ClienteRepository.getAll();
        clientes.value = response.data || [];
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
};

const refrescar = () => {
    initialize();
    showMessage("Lista actualizada");
};

const showMessage = (msg = "", type = "success") => {
    const toast = window.Swal.mixin({ toast: true, position: "top", showConfirmButton: false, timer: 3000 });
    toast.fire({ icon: type, title: msg, padding: "10px 20px" });
};

onMounted(async () => {
    registerModal = new window.bootstrap.Modal(document.getElementById("registerModal"));
    deleteModal = new window.bootstrap.Modal(document.getElementById("deleteModal"));
    await initialize();
});
</script>
