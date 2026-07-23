<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const products = [
    { sku: 'PRD-001', name: 'Laptop Pro 14"', category: 'Electrónica', stock: 12, price: '$1,299' },
    { sku: 'PRD-002', name: 'Mouse inalámbrico', category: 'Accesorios', stock: 84, price: '$29' },
    { sku: 'PRD-003', name: 'Teclado mecánico', category: 'Accesorios', stock: 41, price: '$89' },
    { sku: 'PRD-004', name: 'Monitor 27" 4K', category: 'Electrónica', stock: 18, price: '$449' },
    { sku: 'PRD-005', name: 'Silla ergonómica', category: 'Oficina', stock: 7, price: '$320' },
    { sku: 'PRD-006', name: 'Hub USB-C', category: 'Accesorios', stock: 56, price: '$45' },
];
</script>

<template>
    <Head title="Tablas" />

    <AuthenticatedLayout>
        <template #header>
            <h3 class="mb-0">Tablas</h3>
        </template>
        <template #breadcrumb>
            <li class="breadcrumb-item"><Link :href="route('dashboard')">Home</Link></li>
            <li class="breadcrumb-item active">Tablas</li>
        </template>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Inventario de productos</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Stock</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in products" :key="item.sku">
                                    <td><code>{{ item.sku }}</code></td>
                                    <td>{{ item.name }}</td>
                                    <td>{{ item.category }}</td>
                                    <td>
                                        <span
                                            class="badge"
                                            :class="item.stock < 15 ? 'text-bg-danger' : 'text-bg-success'"
                                        >
                                            {{ item.stock }}
                                        </span>
                                    </td>
                                    <td>{{ item.price }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
