<?php

use App\Http\Controllers\Api\PublicSiteController;
use Illuminate\Support\Facades\Route;

Route::prefix('public')->group(function () {
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
