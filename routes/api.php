<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ConfiguracionEmpresaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ConfiguracionHardwareController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\ImpresionController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\UnidadMedidaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/registro', [UserController::class, 'registro']);

// Ruta pública para configuración básica (logo, favicon, etc.) - no requiere autenticación
Route::get('/configuracion-empresa/public', [ConfiguracionEmpresaController::class, 'publicConfig']);

// Ruta pública para ejecutar storage:link
Route::get('/storage-link', function () {
    try {
        Artisan::call('storage:link');
        $output = Artisan::output();
        return response()->json([
            'success' => true,
            'message' => 'Enlace simbólico de storage creado correctamente',
            'output' => $output
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error ejecutando storage:link: ' . $e->getMessage()
        ], 500);
    }
});

// Ruta pública para generar la clave de aplicación
Route::get('/generate-key', function () {
    try {
        Artisan::call('key:generate');
        $output = Artisan::output();
        return response()->json([
            'success' => true,
            'message' => 'Clave de aplicación generada correctamente',
            'output' => $output
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error generando clave de aplicación: ' . $e->getMessage()
        ], 500);
    }
});

Route::prefix('/tokens')->group(function (): void {
    Route::post('/create', [TokenController::class, 'createToken']);
    Route::post('/pw', [TokenController::class, 'createResetPW']);
    Route::post('/savepw', [TokenController::class, 'saveResetPW']);
    Route::post('/getpwuuid', [TokenController::class, 'getUserUuid']);
});

Route::prefix('/configuracion-empresa')->group(function (): void {
    Route::get('/', [ConfiguracionEmpresaController::class, 'index']);
});

// Solo pruebas: snapshot estático sin login (HiLook NVR no tiene ISAPI snapshot)
// Solo pruebas: probar video con valores estáticos (sin login, sin BD)
Route::get('/probar-video-test', [ConfiguracionHardwareController::class, 'probarVideoTest']);

Route::middleware('auth:sanctum')->group(function () {
    // Dashboard: estadísticas unificadas por sucursal activa (perfil, boletos, ventas, donativos, alertas)
    Route::get('/dashboard/estadisticas', [ReporteController::class, 'dashboardEstadisticas']);

    Route::prefix('/tokens')->group(function (): void {
        Route::delete('/{token}', [TokenController::class, 'expire']);
        Route::get('/permissions', function () {
            if (!auth()->check()) {
                return response()->json(['all' => false, 'permissions' => []], 401);
            }
            
            $user = auth()->user();
            $permissions = $user->getAllPermissions()->pluck('name')->toArray();
            $roles = $user->getRoleNames()->toArray();
            
            // Si es Administrador, tiene todos los permisos
            $isAdmin = in_array('Administrador', $roles);
            
            return response()->json([
                'all' => $isAdmin,
                'permissions' => $permissions,
                'roles' => $roles
            ]);
        });
    });

    Route::prefix('/users')->group(function (): void {
        Route::get('/', fn (Request $request) => $request->user()->load('roles'));
        Route::get('/all', [UserController::class, 'getUsers']);
        Route::get('/{user}', [UserController::class, 'getUser']);
        Route::post('/create', [UserController::class, 'store']);
        Route::put('/{user}/edit', [UserController::class, 'update']);
        Route::put('/{user}/password', [UserController::class, 'updatePassword']);
        Route::get('/{user}/mediciones', [UserController::class, 'getMediciones']);
        Route::get('/{user}/sucursales', [UserController::class, 'getSucursales']);
        Route::put('/perfil', [UserController::class, 'updatePerfil']);
        Route::get('/config/setconfig', [UserController::class, 'setConfigSucursal']);
        Route::get('/config/sucursal', [UserController::class, 'getConfigSucursal']);
        Route::get('/get/terapista', [UserController::class, 'getTerapistas']);
        Route::get('/get/revision', [UserController::class, 'getRevision']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
    });

    Route::prefix('/roles')->group(function (): void {
        Route::get('/all', [RoleController::class, 'getRoles']);
        Route::get('/permissions', [RoleController::class, 'getPermissions']);
        Route::put('/{role}/permissions', [RoleController::class, 'updateRolePermissions']);
    });

    Route::prefix('/configuracion-empresa')->group(function (): void {
        Route::get('/sucursal', [ConfiguracionEmpresaController::class, 'sucursal']);
        Route::put('/update', [ConfiguracionEmpresaController::class, 'update']);
        Route::post('/upload-logo', [ConfiguracionEmpresaController::class, 'uploadLogo']);
        Route::post('/upload-favicon', [ConfiguracionEmpresaController::class, 'uploadFavicon']);
        Route::post('/upload-icono-mensaje', [ConfiguracionEmpresaController::class, 'uploadIconoMensaje']);
        Route::delete('/delete-logo', [ConfiguracionEmpresaController::class, 'deleteLogo']);
        Route::delete('/delete-favicon', [ConfiguracionEmpresaController::class, 'deleteFavicon']);
        Route::delete('/delete-icono-mensaje', [ConfiguracionEmpresaController::class, 'deleteIconoMensaje']);
    });

    Route::prefix('/marcas')->group(function (): void {
        Route::get('/', [MarcaController::class, 'index']);
        Route::post('/', [MarcaController::class, 'store']);
        Route::get('/{marca}', [MarcaController::class, 'show']);
        Route::put('/{marca}', [MarcaController::class, 'update']);
        Route::delete('/{marca}', [MarcaController::class, 'destroy']);
        Route::post('/{marca}/upload-logo', [MarcaController::class, 'uploadLogo']);
        Route::delete('/{marca}/delete-logo', [MarcaController::class, 'deleteLogo']);
    });

    // Módulo de Administración - Configuración Hardware
    Route::prefix('/configuracion-hardware')->group(function (): void {
        Route::get('/', [ConfiguracionHardwareController::class, 'index']);
        Route::post('/', [ConfiguracionHardwareController::class, 'store']);
        Route::get('/{configuracionHardware}', [ConfiguracionHardwareController::class, 'show']);
        Route::put('/{configuracionHardware}', [ConfiguracionHardwareController::class, 'update']);
        Route::delete('/{configuracionHardware}', [ConfiguracionHardwareController::class, 'destroy']);
        Route::post('/{configuracionHardware}/probar-video', [ConfiguracionHardwareController::class, 'probarVideo']);
    });

    // Módulo de Reportes
    Route::prefix('/reportes')->group(function (): void {
        // Reportes legacy de salidas/boletos: solo sucursales tipo almacén (venta_almacen)
        Route::middleware('sucursal.tipo:venta_almacen')->group(function (): void {
            Route::get('/salidas', [ReporteController::class, 'salidas']);
            Route::get('/estadisticas', [ReporteController::class, 'estadisticas']);
            Route::get('/exportar-csv', [ReporteController::class, 'exportarCsv']);
        });

        // Reportes de ventas: disponibles para ambos tipos de sucursal
        Route::get('/ventas/estadisticas', [ReporteController::class, 'estadisticasVentas']);
        Route::get('/ventas/exportar-excel', [ReporteController::class, 'exportarVentasExcel']);
        Route::get('/ventas/exportar-pdf', [ReporteController::class, 'exportarVentasPdf']);
        Route::get('/ventas/tickets-villahermosa-excel', [ReporteController::class, 'exportarTicketsVillahermosaExcel']);
    });

    // Catálogos para ventas y módulos Productos / Clientes
    Route::get('/sucursales', [SucursalController::class, 'index']);
    Route::get('/unidades-medida', [UnidadMedidaController::class, 'index']);
    Route::get('/productos', [ProductoController::class, 'index']);
    Route::get('/productos/{producto}', [ProductoController::class, 'show']);
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::put('/productos/{producto}', [ProductoController::class, 'update']);
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy']);
    Route::get('/clientes', [ClienteController::class, 'index']);
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show']);
    Route::post('/clientes', [ClienteController::class, 'store']);
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update']);
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy']);

    // Ventas (Villahermosa: ticket + QR; Macuspana: importar pedido)
    Route::prefix('/ventas')->group(function (): void {
        Route::get('/', [VentaController::class, 'index']);
        Route::get('/pedidos-pendientes-pago', [VentaController::class, 'pedidosPendientesPago'])
            ->middleware('sucursal.tipo:venta_almacen');

        // Creación de ventas (perfil VENTA / Villahermosa) — requiere caja abierta
        Route::post('/', [VentaController::class, 'store'])
            ->middleware('sucursal.tipo:venta')
            ->middleware('caja.abierta');

        // Importar pedido (solo Macuspana: venta_almacen)
        Route::post('/importar-pedido', [VentaController::class, 'importarPedido'])
            ->middleware('sucursal.tipo:venta_almacen');

        // Flujo Vigilante (solo Macuspana: venta_almacen)
        Route::post('/vigilante/generar-qr-local', [VentaController::class, 'generarQrVigilanteLocal'])
            ->middleware('sucursal.tipo:venta_almacen');
        Route::post('/vigilante/validar-qr', [VentaController::class, 'validarQrVigilante'])
            ->middleware('sucursal.tipo:venta_almacen');

        // Registrar pagos de venta generada en caseta (flujo Oficina Central Macuspana) — requiere caja abierta
        Route::post('/{venta}/registrar-pagos', [VentaController::class, 'registrarPagos'])
            ->middleware('sucursal.tipo:venta_almacen')
            ->middleware('caja.abierta');

        Route::post('/{venta}/cancelar', [VentaController::class, 'cancelar']);
        Route::get('/{venta}/ticket-contenido', [VentaController::class, 'ticketContenido']);
        Route::get('/{venta}', [VentaController::class, 'show']);
    });

    // Entregas (parciales; foto opcional pero se registra por entrega)
    Route::prefix('/entregas')->group(function (): void {
        Route::get('/', [EntregaController::class, 'index']);
        Route::post('/registrar', [EntregaController::class, 'registrar']);
        Route::post('/registrar-acceso', [EntregaController::class, 'validarYRegistrarAcceso']);
    });

    // Inventario: gestión, ajustes, alertas de stock bajo y movimientos
    Route::get('/inventario', [InventarioController::class, 'index']);
    Route::post('/inventario/ajustar', [InventarioController::class, 'ajustar']);
    Route::get('/inventario/alertas', [InventarioController::class, 'alertas']);
    Route::get('/inventario/movimientos', [InventarioController::class, 'movimientos']);

    // Caja (apertura, corte X/Z, registro de gastos)
    Route::prefix('/cajas')->group(function (): void {
        Route::get('/', [CajaController::class, 'index']);
        Route::get('/caja-abierta', [CajaController::class, 'cajaAbierta']);
        Route::post('/apertura', [CajaController::class, 'apertura']);
        Route::post('/corte', [CajaController::class, 'corte']);
        Route::get('/{caja}/gastos', [GastoController::class, 'index']);
        Route::post('/{caja}/gastos', [GastoController::class, 'store']);
    });

    // Impresión: gestión IP/nombre impresora y envío RAW
    Route::prefix('/impresion')->group(function (): void {
        Route::get('/', [ImpresionController::class, 'index']);
        Route::post('/', [ImpresionController::class, 'store']);
        Route::post('/imprimir-raw', [ImpresionController::class, 'imprimirRaw']);
        Route::get('/{configuracionHardware}', [ImpresionController::class, 'show']);
        Route::put('/{configuracionHardware}', [ImpresionController::class, 'update']);
    });

}); 