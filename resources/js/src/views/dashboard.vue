<template>
    <div class="layout-px-spacing dash_1">
        <teleport to="#breadcrumb">
            <ul class="navbar-nav flex-row">
                <li>
                    <div class="page-header">
                        <nav class="breadcrumb-one" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:;">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <span>{{ perfil === 'VENTA' ? 'Ventas' : 'Control de Salidas' }}</span>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </li>
            </ul>
        </teleport>

        <div class="row layout-top-spacing">
            <!-- Dashboard VENTA (Villahermosa): ingresos, volumen, gráfica ventas -->
            <template v-if="perfil === 'VENTA'">
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                    <div class="widget widget-card-one">
                        <div class="widget-content">
                            <div class="media">
                                <div class="w-img">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                </div>
                                <div class="media-body">
                                    <h6>Ingresos (período)</h6>
                                    <p class="meta-date-time text-success">$ {{ formatMoney(ventasStats?.total_ventas || 0) }}</p>
                                </div>
                            </div>
                            <p class="meta-date-time">Ventas no donativos</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                    <div class="widget widget-card-one">
                        <div class="widget-content">
                            <div class="media">
                                <div class="w-img">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
                                </div>
                                <div class="media-body">
                                    <h6>Volumen de ventas</h6>
                                    <p class="meta-date-time text-primary">{{ ventasStats?.cantidad_ventas || 0 }}</p>
                                </div>
                            </div>
                            <p class="meta-date-time">Nº de ventas en el período</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                    <div class="widget widget-revenue">
                        <div class="widget-heading">
                            <h5>Ingresos por día</h5>
                            <div class="dropdown btn-group">
                                <a href="javascript:;" id="ddlRevenueVentas" class="btn dropdown-toggle btn-icon-only" data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="ddlRevenueVentas">
                                    <li><a href="javascript:;" class="dropdown-item" @click="cambiarPeriodo('7')">Últimos 7 días</a></li>
                                    <li><a href="javascript:;" class="dropdown-item" @click="cambiarPeriodo('30')">Últimos 30 días</a></li>
                                    <li><a href="javascript:;" class="dropdown-item" @click="cambiarPeriodo('90')">Últimos 90 días</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="widget-content">
                            <div class="chart-title">Total ingresos <span class="text-primary ms-1">$ {{ formatMoney(ventasStats?.total_ventas || 0) }}</span></div>
                            <apex-chart v-if="ventas_series.length" height="325" type="area" :options="ventas_chart_options" :series="ventas_series"></apex-chart>
                            <div v-else class="text-center text-muted py-5">Sin datos de ventas en el período</div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Dashboard VENTA_ALMACEN (Macuspana): boletos, inventario, donativos, accesos -->
            <template v-else-if="perfil === 'VENTA_ALMACEN'">
            <!-- Estadísticas principales (boletos) -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>
                            </div>
                            <div class="media-body">
                                <h6>Total de Boletos</h6>
                                <p class="meta-date-time">{{ estadisticas.total || 0 }}</p>
                            </div>
                        </div>
                        <p class="meta-date-time">Hoy: {{ estadisticas.hoy || 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <div class="media-body">
                                <h6>Pendientes</h6>
                                <p class="meta-date-time text-warning">{{ estadisticas.pendientes || 0 }}</p>
                            </div>
                        </div>
                        <p class="meta-date-time">Boletos sin validar</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <div class="media-body">
                                <h6>Utilizados</h6>
                                <p class="meta-date-time text-success">{{ estadisticas.utilizados || 0 }}</p>
                            </div>
                        </div>
                        <p class="meta-date-time">Boletos validados</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck">
                                    <rect x="1" y="3" width="15" height="13"></rect>
                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                </svg>
                            </div>
                            <div class="media-body">
                                <h6>Salidas Hoy</h6>
                                <p class="meta-date-time text-primary">{{ estadisticas.salidas_hoy || 0 }}</p>
                            </div>
                        </div>
                        <p class="meta-date-time">Volteos autorizados</p>
                    </div>
                </div>
            </div>
            <!-- Pedidos pendientes de cobro (Macuspana) -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h18v4H3z"/><path d="M3 9h18v12H3z"/><path d="M7 13h4v4H7z"/></svg>
                            </div>
                            <div class="media-body">
                                <h6>Pedidos pendientes de cobro</h6>
                                <p class="meta-date-time text-warning">{{ pendientesCobro ?? 0 }}</p>
                            </div>
                        </div>
                        <p class="meta-date-time">Ventas con estatus pendiente_pago</p>
                        <router-link to="/cajas/validar-pedidos" class="btn btn-sm btn-outline-warning mt-2">Ir a validar pedidos</router-link>
                    </div>
                </div>
            </div>
            <!-- Donativos, alertas e entregas (solo VENTA_ALMACEN) -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 12v10H4V12"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/></svg>
                            </div>
                            <div class="media-body">
                                <h6>Donativos (período)</h6>
                                <p class="meta-date-time text-info">$ {{ formatMoney(donativosTotal || 0) }}</p>
                            </div>
                        </div>
                        <p class="meta-date-time">Total donativos</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            </div>
                            <div class="media-body">
                                <h6>Alertas inventario</h6>
                                <p class="meta-date-time" :class="(alertasInventario || 0) > 0 ? 'text-danger' : 'text-muted'">{{ alertasInventario || 0 }}</p>
                            </div>
                        </div>
                        <p class="meta-date-time">Productos con stock agotado</p>
                        <router-link v-if="(alertasInventario || 0) > 0" to="/inventario/alertas" class="btn btn-sm btn-outline-danger mt-2">Ver alertas</router-link>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                            <div class="media-body">
                                <h6>Productos con stock bajo</h6>
                                <p class="meta-date-time text-warning">{{ productosStockBajo ?? 0 }}</p>
                            </div>
                        </div>
                        <p class="meta-date-time">Stock actual por debajo del mínimo configurado</p>
                        <router-link v-if="(productosStockBajo || 0) > 0" to="/inventario" class="btn btn-sm btn-outline-warning mt-2">Ver inventario</router-link>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M16 10l-4 4-2-2-3 3"/></svg>
                            </div>
                            <div class="media-body">
                                <h6>Entregas hoy</h6>
                                <p class="meta-date-time text-primary">{{ entregasHoy ?? 0 }} viajes</p>
                            </div>
                        </div>
                        <p class="meta-date-time">Volumen entregado: {{ (volumenEntregadoHoy ?? 0).toFixed ? volumenEntregadoHoy.toFixed(2) : Number(volumenEntregadoHoy || 0).toFixed(2) }} m³</p>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                <div class="widget widget-card-one">
                    <div class="widget-content">
                        <div class="media">
                            <div class="w-img">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            </div>
                            <div class="media-body">
                                <h6>Valor inventario</h6>
                                <p class="meta-date-time text-success">$ {{ formatMoney(valorInventario ?? 0) }}</p>
                            </div>
                        </div>
                        <p class="meta-date-time">Suma de precio_unitario × stock_actual</p>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Boletos por Día -->
            <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-revenue">
                    <div class="widget-heading">
                        <h5>Boletos Generados por Día</h5>
                        <div class="dropdown btn-group">
                            <a href="javascript:;" id="ddlRevenue" class="btn dropdown-toggle btn-icon-only" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="19" cy="12" r="1"></circle>
                                    <circle cx="5" cy="12" r="1"></circle>
                                </svg>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="ddlRevenue">
                                <li><a href="javascript:;" class="dropdown-item" @click="cambiarPeriodo('7')">Últimos 7 días</a></li>
                                <li><a href="javascript:;" class="dropdown-item" @click="cambiarPeriodo('30')">Últimos 30 días</a></li>
                                <li><a href="javascript:;" class="dropdown-item" @click="cambiarPeriodo('90')">Últimos 90 días</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="widget-content">
                        <div class="chart-title">Total de Boletos <span class="text-primary ms-1">{{ estadisticas.total || 0 }}</span></div>
                        <apex-chart v-if="boletos_series" height="325" type="area" :options="boletos_options" :series="boletos_series"></apex-chart>
                    </div>
                </div>
            </div>

            <!-- Distribución por Estatus -->
            <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-sales-category">
                    <div class="widget-heading">
                        <h5>Distribución por Estatus</h5>
                    </div>
                    <div class="widget-content">
                        <apex-chart v-if="estatus_donut_options" height="460" type="donut" :options="estatus_donut_options" :series="estatus_donut_series"></apex-chart>
                    </div>
                </div>
            </div>

            <!-- Boletos por Día de la Semana -->
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                <div class="widget widget-dailysales">
                    <div class="widget-heading">
                        <div>
                            <h5>Boletos por Día de la Semana</h5>
                            <span class="sub-title">Última semana</span>
                        </div>
                        <div class="w-icon text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                    </div>
                    <div class="widget-content">
                        <apex-chart v-if="semana_options" height="160" type="bar" :options="semana_options" :series="semana_series"></apex-chart>
                    </div>
                </div>
            </div>

            <!-- Resumen de Operaciones -->
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                <div class="widget widget-summary">
                    <div class="widget-heading">
                        <h5>Resumen de Operaciones</h5>
                    </div>
                    <div class="widget-content">
                        <div class="summary-list">
                            <div class="w-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                </svg>
                            </div>
                            <div class="w-summary-details">
                                <div class="w-summary-info">
                                    <h6>Boletos Generados</h6>
                                    <p class="summary-count">{{ estadisticas.total || 0 }}</p>
                                </div>
                                <div class="w-summary-stats">
                                    <div class="progress">
                                        <div role="progressbar" aria-valuemin="0" aria-valuemax="100" :aria-valuenow="porcentajeGenerados" class="progress-bar bg-gradient-primary" :style="`width: ${porcentajeGenerados}%`"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="summary-list">
                            <div class="w-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <div class="w-summary-details">
                                <div class="w-summary-info">
                                    <h6>Boletos Validados</h6>
                                    <p class="summary-count">{{ estadisticas.utilizados || 0 }}</p>
                                </div>
                                <div class="w-summary-stats">
                                    <div class="progress">
                                        <div role="progressbar" aria-valuemin="0" aria-valuemax="100" :aria-valuenow="porcentajeValidados" class="progress-bar bg-gradient-success" :style="`width: ${porcentajeValidados}%`"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="summary-list">
                            <div class="w-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <div class="w-summary-details">
                                <div class="w-summary-info">
                                    <h6>Pendientes</h6>
                                    <p class="summary-count">{{ estadisticas.pendientes || 0 }}</p>
                                </div>
                                <div class="w-summary-stats">
                                    <div class="progress">
                                        <div role="progressbar" aria-valuemin="0" aria-valuemax="100" :aria-valuenow="porcentajePendientes" class="progress-bar bg-gradient-warning" :style="`width: ${porcentajePendientes}%`"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total de Salidas -->
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-total-order">
                    <div class="widget-heading">
                        <div class="w-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck">
                                <rect x="1" y="3" width="15" height="13"></rect>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                        </div>
                        <div>
                            <div class="w-value">{{ estadisticas.utilizados || 0 }}</div>
                            <div class="w-numeric-title">Salidas Autorizadas</div>
                        </div>
                    </div>
                    <div class="widget-content p-0">
                        <apex-chart v-if="salidas_options" height="290" type="area" :options="salidas_options" :series="salidas_series"></apex-chart>
                    </div>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-recent-activity">
                    <div class="widget-heading">
                        <h5>Actividad Reciente de Boletos</h5>
                        <div class="task-action">
                            <router-link to="/boletos/lista" class="btn btn-sm btn-primary">Ver Todos</router-link>
                        </div>
                    </div>
                    <div class="widget-content">
                        <perfect-scrollbar class="timeline-line">
                            <div v-for="(actividad, index) in actividadesRecientes" :key="index" :class="`item-timeline timeline-${actividad.color}`">
                                <div :class="`badge badge-${actividad.color}`"></div>
                                <div class="t-text">
                                    <p>
                                        <span>{{ actividad.accion }}</span> 
                                        Boleto <strong>{{ actividad.folio }}</strong>
                                    </p>
                                    <span :class="`badge badge-outline-${actividad.color} outline-badge-${actividad.color} icon-fill-${actividad.color}`">
                                        {{ actividad.estatus }}
                                    </span>
                                    <p class="t-time">{{ actividad.tiempo }}</p>
                                </div>
                            </div>
                            <div v-if="actividadesRecientes.length === 0" class="text-center p-4">
                                <p class="text-muted">No hay actividad reciente</p>
                            </div>
                        </perfect-scrollbar>
                    </div>
                </div>
            </div>

            <!-- Boletos Recientes -->
            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-recent-orders">
                    <div class="widget-heading">
                        <h5>Boletos Recientes</h5>
                        <div class="task-action">
                            <router-link to="/boletos/lista" class="btn btn-sm btn-primary">Ver Todos</router-link>
                        </div>
                    </div>
                    <div class="widget-content table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><div class="th-content">Folio</div></th>
                                    <th><div class="th-content">Placa</div></th>
                                    <th><div class="th-content th-heading">Fecha</div></th>
                                    <th><div class="th-content">Estatus</div></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="boleto in boletosRecientes" :key="boleto.id">
                                    <td>
                                        <div class="td-content">
                                            <span class="text-primary">{{ boleto.folio }}</span>
                                        </div>
                                    </td>
                                    <td><div class="td-content">{{ boleto.placa }}</div></td>
                                    <td>
                                        <div class="td-content">
                                            <span>{{ formatearFecha(boleto.fecha_generacion) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="td-content">
                                            <span :class="getEstatusBadgeClass(boleto.estatus)">
                                                {{ getEstatusLabel(boleto.estatus) }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="boletosRecientes.length === 0">
                                    <td colspan="4" class="text-center p-4">
                                        <p class="text-muted">No hay boletos recientes</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </template>
            <!-- Sin sucursal configurada -->
            <div v-else class="col-12 layout-spacing">
                <div class="alert alert-warning mb-0">
                    Configure la sucursal en <strong>Configuración de la empresa</strong> para ver el dashboard.
                </div>
            </div>
        </div>
    </div>
    <Loading
        v-model:active="isLoading"
        :can-cancel="false"
        :is-full-page="true"
    />
</template>

<script setup>
    import "../assets/sass/widgets/widgets.scss";
    import { computed, ref, onMounted, onBeforeUnmount } from "vue";
    import { useStore } from "vuex";
    import ApexChart from "vue3-apexcharts";
    import { useMeta } from "../composables/use-meta";
    import * as ReporteRepository from "@/repositories/ReporteRepository";
    import * as BoletoRepository from "@/repositories/BoletoRepository";
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/css/index.css';

    useMeta({ title: "Dashboard - SCV" });

    const store = useStore();
    const isLoading = ref(false);
    // Perfil desde API o store (VENTA | VENTA_ALMACEN)
    const perfil = ref(store.getters?.perfilInterfaz || null);
    const estadisticas = ref({
        total: 0,
        pendientes: 0,
        utilizados: 0,
        cancelados: 0,
        hoy: 0,
        salidas_hoy: 0,
        por_dia: []
    });
    const ventasStats = ref(null);
    const donativosTotal = ref(null);
    const alertasInventario = ref(null);
    const pendientesCobro = ref(null);
    const entregasHoy = ref(null);
    const volumenEntregadoHoy = ref(null);
    const productosStockBajo = ref(null);
    const valorInventario = ref(null);

    const boletosRecientes = ref([]);
    const actividadesRecientes = ref([]);
    const periodo = ref(30);
    let timeoutIdRef = null;

    const ventas_series = ref([]);
    const ventas_chart_options = computed(() => {
        const is_dark = store.state.is_dark_mode;
        const fechas = (ventasStats.value?.fechas || []).map(f => {
            const d = new Date(f);
            return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
        });
        return {
            chart: { fontFamily: 'Nunito, sans-serif', zoom: { enabled: false }, toolbar: { show: false } },
            dataLabels: { enabled: false },
            stroke: { show: true, curve: 'smooth', width: 2 },
            colors: is_dark ? ['#1abc9c'] : ['#1abc9c'],
            xaxis: { categories: fechas, labels: { style: { fontSize: '12px' } } },
            yaxis: { labels: { formatter: v => '$ ' + Number(v).toFixed(0) } },
            grid: { borderColor: is_dark ? '#191e3a' : '#e0e6ed', strokeDashArray: 5 },
            fill: { type: 'gradient', gradient: { type: 'vertical', shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
            tooltip: { theme: 'dark', y: { formatter: v => '$ ' + Number(v).toFixed(2) } },
        };
    });

    const formatMoney = (n) => Number(n).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // Series y opciones para gráficos
    const boletos_series = ref([]);
    const estatus_donut_series = ref([]);
    const semana_series = ref([]);
    const salidas_series = ref([]);

    const boletos_options = computed(() => {
        const is_dark = store.state.is_dark_mode;
        const fechas = estadisticas.value.por_dia?.map(d => {
            const fecha = new Date(d.fecha);
            return fecha.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
        }) || [];
        
        return {
            chart: {
                fontFamily: "Nunito, sans-serif",
                zoom: { enabled: false },
                toolbar: { show: false },
            },
            dataLabels: { enabled: false },
            stroke: { show: true, curve: "smooth", width: 2, lineCap: "square" },
            dropShadow: { enabled: true, opacity: 0.2, blur: 10, left: -7, top: 22 },
            colors: is_dark ? ["#2196f3"] : ["#1b55e2"],
            xaxis: {
                axisBorder: { show: false },
                axisTicks: { show: false },
                crosshairs: { show: true },
                labels: { offsetX: 0, offsetY: 5, style: { fontSize: "12px", fontFamily: "Nunito, sans-serif" } },
                categories: fechas
            },
            yaxis: {
                tickAmount: 7,
                labels: {
                    formatter: function (value) {
                        return Math.round(value);
                    },
                    offsetX: -10,
                    offsetY: 0,
                    style: { fontSize: "12px", fontFamily: "Nunito, sans-serif" },
                },
            },
            grid: {
                borderColor: is_dark ? "#191e3a" : "#e0e6ed",
                strokeDashArray: 5,
                xaxis: { lines: { show: true } },
                yaxis: { lines: { show: false } },
                padding: { top: 0, right: 0, bottom: 0, left: 0 },
            },
            legend: {
                position: "top",
                horizontalAlign: "right",
                offsetY: 0,
                fontSize: "16px",
                fontFamily: "Nunito, sans-serif",
            },
            tooltip: { theme: "dark", marker: { show: true }, x: { show: false } },
            fill: {
                type: "gradient",
                gradient: {
                    type: "vertical",
                    shadeIntensity: 1,
                    inverseColors: !1,
                    opacityFrom: is_dark ? 0.19 : 0.28,
                    opacityTo: 0.05,
                    stops: is_dark ? [100, 100] : [45, 100],
                },
            },
        };
    });

    const estatus_donut_options = computed(() => {
        const is_dark = store.state.is_dark_mode;
        return {
            chart: {},
            dataLabels: { enabled: false },
            expandOnClick: is_dark ? false : true,
            stroke: { show: true, width: 25, colors: is_dark ? "#0e1726" : "#fff" },
            colors: is_dark ? ["#e2a03f", "#1abc9c", "#e7515a"] : ["#e2a03f", "#1abc9c", "#e7515a"],
            legend: {
                position: "bottom",
                horizontalAlign: "center",
                fontSize: "14px",
                markers: { width: 10, height: 10 },
                height: 50,
                offsetY: 20,
                itemMargin: { horizontal: 8, vertical: 0 },
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: "65%",
                        background: "transparent",
                        labels: {
                            show: true,
                            name: { show: true, fontSize: "29px", fontFamily: "Nunito, sans-serif", offsetY: -10 },
                            value: {
                                show: true,
                                fontSize: "26px",
                                fontFamily: "Nunito, sans-serif",
                                color: is_dark ? "#bfc9d4" : undefined,
                                offsetY: 16,
                            },
                            total: {
                                show: true,
                                label: "Total",
                                color: "#888ea8",
                                fontSize: "29px",
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce(function (a, b) {
                                        return a + b;
                                    }, 0);
                                },
                            },
                        },
                    },
                },
            },
            labels: ["Pendientes", "Utilizados", "Cancelados"],
        };
    });

    const semana_options = computed(() => {
        return {
            chart: { toolbar: { show: false }, stacked: false },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 1 },
            colors: ["#1b55e2"],
            xaxis: { 
                labels: { show: true }, 
                categories: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"] 
            },
            yaxis: { show: true },
            fill: { opacity: 1 },
            plotOptions: { bar: { horizontal: false, columnWidth: "40%" } },
            legend: { show: false },
            grid: {
                show: true,
                borderColor: "#e0e6ed",
                strokeDashArray: 5,
            },
        };
    });

    const salidas_options = computed(() => {
        const is_dark = store.state.is_dark_mode;
        return {
            chart: { sparkline: { enabled: true } },
            stroke: { curve: "smooth", width: 2 },
            colors: is_dark ? ["#1abc9c"] : ["#1abc9c"],
            labels: Array.from({ length: 10 }, (_, i) => (i + 1).toString()),
            yaxis: { min: 0, show: false },
            grid: { padding: { top: 125, right: 0, bottom: 0, left: 0 } },
            fill: {
                opacity: 1,
                type: "gradient",
                gradient: {
                    type: "vertical",
                    shadeIntensity: 1,
                    inverseColors: !1,
                    opacityFrom: is_dark ? 0.3 : 0.4,
                    opacityTo: 0.05,
                    stops: is_dark ? [100, 100] : [45, 100],
                },
            },
            tooltip: { x: { show: false }, theme: "dark" },
        };
    });

    const porcentajeGenerados = computed(() => {
        if (estadisticas.value.total === 0) return 0;
        return 100;
    });

    const porcentajeValidados = computed(() => {
        if (estadisticas.value.total === 0) return 0;
        return Math.round((estadisticas.value.utilizados / estadisticas.value.total) * 100);
    });

    const porcentajePendientes = computed(() => {
        if (estadisticas.value.total === 0) return 0;
        return Math.round((estadisticas.value.pendientes / estadisticas.value.total) * 100);
    });

    const cambiarPeriodo = (dias) => {
        periodo.value = parseInt(dias);
        cargarEstadisticas();
    };

    const cargarEstadisticas = async () => {
        isLoading.value = true;
        const fechaHasta = new Date().toISOString().split('T')[0];
        const fechaDesde = new Date();
        fechaDesde.setDate(fechaDesde.getDate() - periodo.value);
        const fechaDesdeStr = fechaDesde.toISOString().split('T')[0];

        timeoutIdRef = setTimeout(() => {
            isLoading.value = false;
        }, 20000);

        try {
            const response = await ReporteRepository.getDashboardEstadisticas({
                fecha_desde: fechaDesdeStr,
                fecha_hasta: fechaHasta
            });
            const payload = response?.data ?? response ?? {};
            perfil.value = payload.perfil ?? store.getters?.perfilInterfaz ?? null;
            const stats = payload.boletos ?? {};
            estadisticas.value = stats;

            ventasStats.value = payload.ventas ?? null;
            donativosTotal.value = payload.donativos_total ?? null;
            alertasInventario.value = payload.alertas_inventario ?? null;
            pendientesCobro.value = payload.pendientes_cobro ?? null;
            entregasHoy.value = payload.entregas_hoy ?? null;
            volumenEntregadoHoy.value = payload.volumen_entregado_hoy ?? null;
            productosStockBajo.value = payload.productos_stock_bajo ?? null;
            valorInventario.value = payload.valor_inventario ?? null;

            if (ventasStats.value?.series_por_sucursal?.length) {
                ventas_series.value = ventasStats.value.series_por_sucursal.map(s => ({
                    name: s.name || 'Ventas',
                    data: (s.data || []).map(v => Number(v) || 0)
                }));
            } else {
                ventas_series.value = [];
            }

            const porDia = Array.isArray(stats.por_dia) ? stats.por_dia : [];
            boletos_series.value = [{
                name: "Boletos Generados",
                data: porDia.map(d => Number(d.total) || 0)
            }];
            estatus_donut_series.value = [
                Number(stats.pendientes) || 0,
                Number(stats.utilizados) || 0,
                Number(stats.cancelados) || 0
            ];
            const porDiaSemana = Array.isArray(stats.por_dia_semana) ? stats.por_dia_semana : [0, 0, 0, 0, 0, 0, 0];
            semana_series.value = [{
                name: "Boletos",
                data: porDiaSemana.slice(0, 7).map(v => Number(v) || 0)
            }];
            const salidasPorDia = Array.isArray(stats.salidas_por_dia) ? stats.salidas_por_dia : Array(10).fill(0);
            salidas_series.value = [{
                name: "Salidas",
                data: salidasPorDia.slice(0, 10).map(v => Number(v) || 0)
            }];
        } catch (error) {
            console.error('Error cargando estadísticas:', error);
            isLoading.value = false;
        }

        try {
            const boletosResponse = await BoletoRepository.getAll({
                per_page: 10,
                sort: 'fecha_generacion',
                order: 'desc'
            });
            const boletosData = boletosResponse?.data?.data ?? boletosResponse?.data ?? [];
            const listaBoletos = Array.isArray(boletosData) ? boletosData : [];
            boletosRecientes.value = listaBoletos.slice(0, 5);
            actividadesRecientes.value = boletosRecientes.value.map(boleto => ({
                folio: boleto.folio,
                accion: boleto.estatus === 'utilizado' ? 'Validado' : boleto.estatus === 'pendiente' ? 'Generado' : 'Cancelado',
                estatus: getEstatusLabel(boleto.estatus),
                tiempo: formatearTiempo(boleto.fecha_generacion),
                color: boleto.estatus === 'utilizado' ? 'success' : boleto.estatus === 'pendiente' ? 'warning' : 'danger'
            }));
        } catch (error) {
            console.error('Error cargando boletos recientes:', error);
            boletosRecientes.value = [];
            actividadesRecientes.value = [];
        } finally {
            if (timeoutIdRef) clearTimeout(timeoutIdRef);
            timeoutIdRef = null;
            isLoading.value = false;
        }
    };

    const getEstatusLabel = (estatus) => {
        const labels = {
            'pendiente': 'Pendiente',
            'utilizado': 'Utilizado',
            'cancelado': 'Cancelado'
        };
        return labels[estatus] || estatus;
    };

    const getEstatusBadgeClass = (estatus) => {
        const classes = {
            'pendiente': 'badge badge-warning',
            'utilizado': 'badge badge-success',
            'cancelado': 'badge badge-danger'
        };
        return classes[estatus] || 'badge badge-secondary';
    };

    const formatearFecha = (fecha) => {
        if (!fecha) return 'N/A';
        const date = new Date(fecha);
        return date.toLocaleDateString('es-ES', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const formatearTiempo = (fecha) => {
        if (!fecha) return 'N/A';
        const date = new Date(fecha);
        const ahora = new Date();
        const diffMs = ahora - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Hace un momento';
        if (diffMins < 60) return `Hace ${diffMins} min`;
        if (diffHours < 24) return `Hace ${diffHours} h`;
        if (diffDays < 7) return `Hace ${diffDays} días`;
        return date.toLocaleDateString('es-ES');
    };

    onMounted(async () => {
        await cargarEstadisticas();
    });
    onBeforeUnmount(() => {
        if (timeoutIdRef) clearTimeout(timeoutIdRef);
        isLoading.value = false;
    });
</script>
