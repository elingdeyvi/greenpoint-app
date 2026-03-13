<template>
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
                <div class="panel p-3">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-sm-12">
                            <div class="text-center titulo-dorado">
                                <h3 class="pull-left">Productos</h3>
                                <button class="btn btn-black pull-right m-1" @click="refrescar">Refrescar</button>
                                <button v-if="canAdmin" class="btn btn-black pull-right m-1" data-bs-toggle="modal" data-bs-target="#registerModal" @click="initializeCreate">Nuevo</button>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label>Estado</label>
                            <select class="form-control" v-model="filtroActivo" @change="initialize">
                                <option value="1">Activos</option>
                                <option value="0">Inactivos</option>
                                <option value="">Todos</option>
                            </select>
                        </div>
                    </div>
                    <div class="custom-table">
                        <v-client-table :data="productos" :columns="columns" :options="table_option">
                            <template #unidad_medida="item">
                                <span>m³</span>
                            </template>
                            <template #precio_unitario="item">
                                <span>$ {{ Number(item.row.precio_unitario || 0).toFixed(2) }}</span>
                            </template>
                            <template #activo="item">
                                <span :class="item.row.activo ? 'badge bg-success' : 'badge bg-danger'">
                                    {{ item.row.activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </template>
                            <template #actions="item">
                                <span v-if="canAdmin">
                                    <a href="javascript:void(0);" title="Editar" @click="getProducto(item.row.id)" class="me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="feather feather-edit-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                    </a>
                                    <a href="javascript:void(0);" title="Eliminar" @click="deleteItem(item.row.id)" v-if="item.row.activo">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </a>
                                </span>
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
                        <h4 class="modal-title">{{ editedIndex === -1 ? 'Nuevo' : 'Editar' }} Producto</h4>
                        <button type="button" data-bs-dismiss="modal" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.stop.prevent="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nombre *</label>
                                        <input type="text" class="form-control mb-2" placeholder="Nombre del producto" v-model="form.nombre" :class="[is_entrada ? (!errors.nombre ? 'is-valid' : 'is-invalid') : '']" />
                                        <div class="invalid-feedback"><li v-for="(item, index) in errors.nombre" :key="index">{{ item }}</li></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <textarea class="form-control mb-2" placeholder="Descripción" v-model="form.descripcion" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Precio unitario *</label>
                                        <input type="number" step="0.01" min="0" class="form-control mb-2" placeholder="0.00" v-model="form.precio_unitario" />
                                        <div class="invalid-feedback"><li v-for="(item, index) in errors.precio_unitario" :key="index">{{ item }}</li></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Stock actual *</label>
                                        <input type="number" step="0.01" min="0" class="form-control mb-2" placeholder="0" v-model="form.stock_actual" />
                                        <div class="invalid-feedback"><li v-for="(item, index) in errors.stock_actual" :key="index">{{ item }}</li></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Unidad de medida</label>
                                        <input type="text" class="form-control mb-2" value="m³" readonly disabled />
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
                                    <button type="button" class="btn btn-success w-100" v-show="editedIndex === -1" @click="createProducto">Guardar</button>
                                    <button type="button" class="btn btn-default w-100" v-show="editedIndex !== -1" @click="updateProducto">Actualizar</button>
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
                        <h5 class="modal-title">¿Desactivar este producto?</h5>
                        <button type="button" data-bs-dismiss="modal" class="btn-close"></button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-black" @click="deleteProducto">Aceptar</button>
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
import * as ProductoRepository from "@/repositories/ProductoRepository";
import * as SucursalRepository from "@/repositories/SucursalRepository";
import ApiService from "@/services/ApiService";
import Loading from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
import axios from "axios";

useMeta({ title: "Productos" });

const { hasPermission } = usePermissions();
const canAdmin = computed(() => hasPermission("productos.administrar"));
const canModificarPrecios = computed(() => hasPermission("precios.modificar"));

const productos = ref([]);
const unidadesMedida = ref([]);
const idUnidadM3 = ref(null);
let registerModal = null;
let deleteModal = null;
const is_entrada = ref(false);
const editedIndex = ref(-1);
const errors = ref({});
const filtroSucursalId = ref("");
const filtroActivo = ref("1");
const form = ref({
    nombre: "",
    descripcion: "",
    precio_unitario: 0,
    stock_actual: 0,
    unidad_medida_id: null,
    sucursal_id: null,
    activo: true,
});
const isLoading = ref(true);

const columns = ref(["id", "nombre", "precio_unitario", "stock_actual", "unidad_medida", "activo", "actions"]);
const table_option = ref({
    headings: {
        id: () => "ID",
        nombre: () => "Nombre",
        precio_unitario: () => "Precio unit.",
        stock_actual: () => "Stock",
        unidad_medida: () => "Unidad",
        activo: () => "Estado",
        actions: () => "",
    },
    perPage: 20,
    perPageValues: [20, 50, 100],
    skin: "table table-hover",
    columnsClasses: { actions: "text-center" },
    sortable: ["id", "nombre", "precio_unitario", "stock_actual"],
    pagination: { nav: "scroll", chunk: 5 },
    texts: { count: "Mostrando {from} a {to} de {count}", filter: "", filterPlaceholder: "Buscar...", limit: "Resultados:", noResults: "No hay registros" },
});

const createProducto = async () => {
    try {
        isLoading.value = true;
        is_entrada.value = true;
        await ProductoRepository.createProducto(form.value);
        showMessage("Producto guardado correctamente");
        initializeCreate();
        registerModal.hide();
        initialize();
    } catch (error) {
        if (axios.isAxiosError(error)) errors.value = error.response?.data?.errors || {};
        isLoading.value = false;
    }
};

const updateProducto = async () => {
    try {
        isLoading.value = true;
        is_entrada.value = true;
        await ProductoRepository.updateProducto(editedIndex.value, form.value);
        showMessage("Producto actualizado correctamente");
        initializeCreate();
        registerModal.hide();
        initialize();
    } catch (error) {
        if (axios.isAxiosError(error)) errors.value = error.response?.data?.errors || {};
        isLoading.value = false;
    }
};

const getProducto = async (id) => {
    try {
        const res = await ProductoRepository.getProductoById(id);
        const d = res.data || res;
        form.value = {
            nombre: d.nombre,
            descripcion: d.descripcion || "",
            precio_unitario: Number(d.precio_unitario) || 0,
            stock_actual: Number(d.stock_actual) || 0,
            unidad_medida_id: idUnidadM3.value ?? d.unidad_medida_id ?? null,
            sucursal_id: d.sucursal_id,
            activo: d.activo !== false,
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

const deleteProducto = async () => {
    try {
        isLoading.value = true;
        await ProductoRepository.deleteProducto(editedIndex.value);
        showMessage("Producto desactivado correctamente");
        deleteModal.hide();
        initialize();
    } catch (error) {
        console.error(error);
        isLoading.value = false;
    }
};

const initializeCreate = () => {
        form.value = {
            nombre: "",
            descripcion: "",
            precio_unitario: 0,
            stock_actual: 0,
            unidad_medida_id: idUnidadM3.value,
            activo: true,
        };
    errors.value = {};
    editedIndex.value = -1;
};

const initialize = async () => {
    isLoading.value = true;
    try {
        const params = {};
        if (filtroActivo.value === "") params.todos = 1;
        else params.activo = filtroActivo.value === "1";
        const response = await ProductoRepository.getAll(params);
        productos.value = response.data || [];
        productos.value.forEach((p) => {
            p.unidad_medida_nombre = p.unidad_medida?.nombre ?? p.unidad_medida;
        });
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
    try {
        const umRes = await ApiService.get("unidades-medida");
        const list = umRes?.data?.data || umRes?.data || [];
        unidadesMedida.value = list;
        const m3 = Array.isArray(list) && list.find((u) => (u.nombre || "").toLowerCase().replace("³", "3") === "m3" || u.codigo === 1);
        if (m3?.id) idUnidadM3.value = m3.id;
    } catch (e) {
        console.error(e);
    }
    await initialize();
});
</script>
