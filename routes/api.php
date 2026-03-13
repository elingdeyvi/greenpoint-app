<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\FormularioContactoController;
use App\Http\Controllers\GaleriaController;
use App\Http\Controllers\PaginaAvisoController;
use App\Http\Controllers\PaginaHistoriaController;
use App\Http\Controllers\PaginaNosotrosController;
use App\Http\Controllers\PaginaTecnologiaController;
use App\Http\Controllers\RedSocialController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\PublicSiteController;

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

// API pública para el sitio (sin auth)
Route::prefix('public')->group(function (): void {
    Route::get('/home', [PublicSiteController::class, 'home']);
    Route::get('/servicios', [PublicSiteController::class, 'serviciosIndex']);
    Route::get('/servicios/{servicio}', [PublicSiteController::class, 'serviciosShow']);
    Route::get('/clientes', [PublicSiteController::class, 'clientesIndex']);
    Route::get('/galeria', [PublicSiteController::class, 'galeriaIndex']);
    Route::get('/contactos', [PublicSiteController::class, 'contactosIndex']);
    Route::get('/pagina-nosotros', [PublicSiteController::class, 'paginaNosotros']);
    Route::get('/pagina-historia', [PublicSiteController::class, 'paginaHistoria']);
    Route::get('/pagina-tecnologia', [PublicSiteController::class, 'paginaTecnologia']);
    Route::get('/pagina-aviso', [PublicSiteController::class, 'paginaAviso']);
    Route::post('/formulario-contacto', [PublicSiteController::class, 'enviarFormularioContacto'])
        ->middleware('throttle:10,1');
});

Route::prefix('/tokens')->group(function (): void {
    Route::post('/create', [TokenController::class, 'createToken']);
    Route::post('/pw', [TokenController::class, 'createResetPW']);
    Route::post('/savepw', [TokenController::class, 'saveResetPW']);
    Route::post('/getpwuuid', [TokenController::class, 'getUserUuid']);
});

Route::middleware('auth:sanctum')->group(function () {
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
        Route::get('/', fn (Request $request) => $request->user()->load('roles'))
            ->middleware('permission:administracion.usuarios');
        Route::get('/all', [UserController::class, 'getUsers'])
            ->middleware('permission:administracion.usuarios');
        Route::get('/{user}', [UserController::class, 'getUser'])
            ->middleware('permission:administracion.usuarios');
        Route::post('/create', [UserController::class, 'store'])
            ->middleware('permission:administracion.usuarios');
        Route::put('/{user}/edit', [UserController::class, 'update'])
            ->middleware('permission:administracion.usuarios');
        Route::put('/{user}/password', [UserController::class, 'updatePassword'])
            ->middleware('permission:administracion.usuarios');
        Route::put('/perfil', [UserController::class, 'updatePerfil']);
        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:administracion.usuarios');
    });

    Route::prefix('/roles')->group(function (): void {
        Route::get('/all', [RoleController::class, 'getRoles'])
            ->middleware('permission:administracion.roles');
        Route::get('/permissions', [RoleController::class, 'getPermissions'])
            ->middleware('permission:administracion.roles');
        Route::put('/{role}/permissions', [RoleController::class, 'updateRolePermissions'])
            ->middleware('permission:administracion.roles');
    });

    // Catálogos
    Route::apiResource('servicios', ServicioController::class)
        ->middleware('permission:catalogos.servicios');

    Route::apiResource('clientes', ClienteController::class)
        ->middleware('permission:catalogos.clientes');

    Route::apiResource('galeria', GaleriaController::class)
        ->middleware('permission:catalogos.galeria');

    Route::apiResource('banners', BannerController::class)
        ->middleware('permission:catalogos.banners');

    Route::apiResource('contactos', ContactoController::class)
        ->middleware('permission:catalogos.contactos');

    Route::get('configuracion', [ConfiguracionController::class, 'index'])
        ->middleware('permission:administracion.configuracion_critica');
    Route::put('configuracion', [ConfiguracionController::class, 'update'])
        ->middleware('permission:administracion.configuracion_critica');

    Route::apiResource('redes-sociales', RedSocialController::class)
        ->middleware('permission:catalogos.redes_sociales');

    Route::prefix('formularios-contacto')->group(function (): void {
        Route::get('/', [FormularioContactoController::class, 'index'])
            ->middleware('permission:formularios_contacto.ver');
        Route::get('/{formularioContacto}', [FormularioContactoController::class, 'show'])
            ->middleware('permission:formularios_contacto.ver');
        Route::put('/{formularioContacto}', [FormularioContactoController::class, 'update'])
            ->middleware('permission:formularios_contacto.ver');
    });

    // Módulos administrables
    Route::prefix('pagina-nosotros')->group(function (): void {
        Route::get('/', [PaginaNosotrosController::class, 'show'])
            ->middleware('permission:modulos.nosotros');
        Route::put('/', [PaginaNosotrosController::class, 'update'])
            ->middleware('permission:modulos.nosotros');
    });

    Route::prefix('pagina-historia')->group(function (): void {
        Route::get('/', [PaginaHistoriaController::class, 'show'])
            ->middleware('permission:modulos.historia');
        Route::put('/', [PaginaHistoriaController::class, 'update'])
            ->middleware('permission:modulos.historia');
    });

    Route::prefix('pagina-tecnologia')->group(function (): void {
        Route::get('/', [PaginaTecnologiaController::class, 'show'])
            ->middleware('permission:modulos.tecnologia');
        Route::put('/', [PaginaTecnologiaController::class, 'update'])
            ->middleware('permission:modulos.tecnologia');
    });

    Route::prefix('pagina-aviso')->group(function (): void {
        Route::get('/', [PaginaAvisoController::class, 'show'])
            ->middleware('permission:modulos.aviso');
        Route::put('/', [PaginaAvisoController::class, 'update'])
            ->middleware('permission:modulos.aviso');
    });

}); 