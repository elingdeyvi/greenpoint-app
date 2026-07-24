<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ClienteController as AdminClienteController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\ContactoController as AdminContactoController;
use App\Http\Controllers\Admin\FormularioContactoController;
use App\Http\Controllers\Admin\GaleriaController as AdminGaleriaController;
use App\Http\Controllers\Admin\PaginaAvisoController;
use App\Http\Controllers\Admin\PaginaHistoriaController;
use App\Http\Controllers\Admin\PaginaNosotrosController;
use App\Http\Controllers\Admin\PaginaTecnologiaController;
use App\Http\Controllers\Admin\RedSocialController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServicioController as AdminServicioController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\ClienteController as PublicClienteController;
use App\Http\Controllers\Public\ContactoController as PublicContactoController;
use App\Http\Controllers\Public\GaleriaController as PublicGaleriaController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PaginaController;
use App\Http\Controllers\Public\ServicioController as PublicServicioController;
use App\Models\FormularioContacto;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Sitio público (equivalente a la API /api/public/* del origen)
|--------------------------------------------------------------------------
*/
Route::name('public.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/nosotros', [PaginaController::class, 'nosotros'])->name('nosotros');
    Route::get('/historia', [PaginaController::class, 'historia'])->name('historia');
    Route::get('/servicios', [PublicServicioController::class, 'index'])->name('servicios.index');
    Route::get('/servicios/{servicio}', [PublicServicioController::class, 'show'])->name('servicios.show');
    Route::get('/clientes', [PublicClienteController::class, 'index'])->name('clientes');
    Route::get('/galeria', [PublicGaleriaController::class, 'index'])->name('galeria');
    Route::get('/tecnologia', [PaginaController::class, 'tecnologia'])->name('tecnologia');
    Route::get('/contacto', [PublicContactoController::class, 'index'])->name('contacto');
    Route::post('/contacto', [PublicContactoController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('contacto.store');
    Route::get('/aviso-de-privacidad', [PaginaController::class, 'aviso'])->name('aviso');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'stats' => [
            'servicios' => Servicio::query()->count(),
            'usuarios' => User::query()->where('estatus', 'activo')->count(),
            'mensajes' => FormularioContacto::query()->where('leido', false)->count(),
            'visitors' => 0,
        ],
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('permission:catalogos.servicios')->group(function () {
        Route::resource('servicios', AdminServicioController::class)->except(['show']);
    });

    Route::middleware('permission:catalogos.clientes')->group(function () {
        Route::resource('clientes', AdminClienteController::class)->except(['show']);
    });

    Route::middleware('permission:catalogos.galeria')->group(function () {
        Route::resource('galeria', AdminGaleriaController::class)
            ->parameters(['galeria' => 'galeria'])
            ->except(['show']);
    });

    Route::middleware('permission:catalogos.banners')->group(function () {
        Route::resource('banners', BannerController::class)->except(['show']);
    });

    Route::middleware('permission:catalogos.contactos')->group(function () {
        Route::resource('contactos', AdminContactoController::class)->except(['show']);
    });

    Route::middleware('permission:catalogos.redes_sociales')->group(function () {
        Route::resource('redes-sociales', RedSocialController::class)
            ->parameters(['redes-sociales' => 'redSocial'])
            ->except(['show']);
    });

    Route::middleware('permission:administracion.configuracion_critica')->group(function () {
        Route::get('configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::put('configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');
    });

    Route::middleware('permission:formularios_contacto.ver')->group(function () {
        Route::get('formularios-contacto', [FormularioContactoController::class, 'index'])->name('formularios-contacto.index');
        Route::get('formularios-contacto/{formularioContacto}', [FormularioContactoController::class, 'show'])->name('formularios-contacto.show');
        Route::put('formularios-contacto/{formularioContacto}', [FormularioContactoController::class, 'update'])->name('formularios-contacto.update');
    });

    Route::middleware('permission:modulos.nosotros')->group(function () {
        Route::get('paginas/nosotros', [PaginaNosotrosController::class, 'edit'])->name('paginas.nosotros.edit');
        Route::put('paginas/nosotros', [PaginaNosotrosController::class, 'update'])->name('paginas.nosotros.update');
    });

    Route::middleware('permission:modulos.historia')->group(function () {
        Route::get('paginas/historia', [PaginaHistoriaController::class, 'edit'])->name('paginas.historia.edit');
        Route::put('paginas/historia', [PaginaHistoriaController::class, 'update'])->name('paginas.historia.update');
    });

    Route::middleware('permission:modulos.tecnologia')->group(function () {
        Route::get('paginas/tecnologia', [PaginaTecnologiaController::class, 'edit'])->name('paginas.tecnologia.edit');
        Route::put('paginas/tecnologia', [PaginaTecnologiaController::class, 'update'])->name('paginas.tecnologia.update');
    });

    Route::middleware('permission:modulos.aviso')->group(function () {
        Route::get('paginas/aviso', [PaginaAvisoController::class, 'edit'])->name('paginas.aviso.edit');
        Route::put('paginas/aviso', [PaginaAvisoController::class, 'update'])->name('paginas.aviso.update');
    });

    Route::middleware('permission:administracion.usuarios')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    Route::middleware('permission:administracion.roles')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
