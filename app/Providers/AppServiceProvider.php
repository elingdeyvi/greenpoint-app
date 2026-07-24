<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\Contacto;
use App\Models\Galeria;
use App\Models\PaginaAviso;
use App\Models\PaginaAvisoLista;
use App\Models\PaginaAvisoSeccion;
use App\Models\PaginaHistoria;
use App\Models\PaginaHistoriaEvento;
use App\Models\PaginaHistoriaImagen;
use App\Models\PaginaNosotros;
use App\Models\PaginaNosotrosImagen;
use App\Models\PaginaNosotrosProgreso;
use App\Models\PaginaTecnologia;
use App\Models\PaginaTecnologiaSeccion;
use App\Models\RedSocial;
use App\Models\Servicio;
use App\Services\PublicSiteService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        $clearPublicCache = function () {
            app(PublicSiteService::class)->forgetCache();
        };

        $models = [
            Banner::class,
            Cliente::class,
            Configuracion::class,
            Contacto::class,
            Galeria::class,
            RedSocial::class,
            Servicio::class,
            PaginaNosotros::class,
            PaginaNosotrosImagen::class,
            PaginaNosotrosProgreso::class,
            PaginaHistoria::class,
            PaginaHistoriaEvento::class,
            PaginaHistoriaImagen::class,
            PaginaTecnologia::class,
            PaginaTecnologiaSeccion::class,
            PaginaAviso::class,
            PaginaAvisoSeccion::class,
            PaginaAvisoLista::class,
        ];

        foreach ($models as $model) {
            $model::saved($clearPublicCache);
            $model::deleted($clearPublicCache);
        }
    }
}
