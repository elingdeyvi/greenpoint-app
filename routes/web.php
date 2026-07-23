<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\ContactoController;
use App\Http\Controllers\Admin\FormularioContactoController;
use App\Http\Controllers\Admin\GaleriaController;
use App\Http\Controllers\Admin\PaginaAvisoController;
use App\Http\Controllers\Admin\PaginaHistoriaController;
use App\Http\Controllers\Admin\PaginaNosotrosController;
use App\Http\Controllers\Admin\PaginaTecnologiaController;
use App\Http\Controllers\Admin\RedSocialController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ServicioController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Models\FormularioContacto;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/login');

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
        Route::resource('servicios', ServicioController::class)->except(['show']);
    });

    Route::middleware('permission:catalogos.clientes')->group(function () {
        Route::resource('clientes', ClienteController::class)->except(['show']);
    });

    Route::middleware('permission:catalogos.galeria')->group(function () {
        Route::resource('galeria', GaleriaController::class)
            ->parameters(['galeria' => 'galeria'])
            ->except(['show']);
    });

    Route::middleware('permission:catalogos.banners')->group(function () {
        Route::resource('banners', BannerController::class)->except(['show']);
    });

    Route::middleware('permission:catalogos.contactos')->group(function () {
        Route::resource('contactos', ContactoController::class)->except(['show']);
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
