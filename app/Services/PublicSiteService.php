<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\Contacto;
use App\Models\FormularioContacto;
use App\Models\Galeria;
use App\Models\PaginaAviso;
use App\Models\PaginaHistoria;
use App\Models\PaginaNosotros;
use App\Models\PaginaTecnologia;
use App\Models\RedSocial;
use App\Models\Servicio;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PublicSiteService
{
    public const CACHE_KEYS = [
        'public_banners',
        'public_servicios',
        'public_clientes',
        'public_galeria',
        'public_contactos',
        'public_redes_sociales',
        'public_configuracion',
        'public_pagina_nosotros',
        'public_pagina_historia',
        'public_pagina_tecnologia',
        'public_pagina_aviso',
    ];

    public function forgetCache(): void
    {
        foreach (self::CACHE_KEYS as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Mapa clave => valor de configuración del sitio.
     */
    public function configuracion(): array
    {
        return Cache::remember('public_configuracion', now()->addMinutes(10), function () {
            return Configuracion::query()
                ->pluck('valor', 'clave')
                ->all();
        });
    }

    public function config(string $clave, ?string $default = null): ?string
    {
        $config = $this->configuracion();

        return array_key_exists($clave, $config) && $config[$clave] !== null && $config[$clave] !== ''
            ? (string) $config[$clave]
            : $default;
    }

    /**
     * Datos para la portada: banners, servicios y preview de nosotros.
     */
    public function home(): array
    {
        $banners = Cache::remember('public_banners', now()->addMinutes(10), function () {
            return Banner::query()
                ->where('activo', true)
                ->orderBy('orden')
                ->get();
        });

        $servicios = Cache::remember('public_servicios', now()->addMinutes(10), function () {
            return Servicio::query()
                ->where('activo', true)
                ->orderBy('orden')
                ->get();
        });

        return [
            'banners' => $banners,
            'servicios' => $servicios,
            'nosotros' => $this->paginaNosotros(),
            'config' => $this->configuracion(),
        ];
    }

    public function servicios(): Collection
    {
        return Cache::remember('public_servicios', now()->addMinutes(10), function () {
            return Servicio::query()
                ->where('activo', true)
                ->orderBy('orden')
                ->get();
        });
    }

    public function servicio(Servicio $servicio): ?Servicio
    {
        if (! $servicio->activo) {
            return null;
        }

        return $servicio;
    }

    public function clientes(): Collection
    {
        return Cache::remember('public_clientes', now()->addMinutes(10), function () {
            return Cliente::query()
                ->where('activo', true)
                ->orderBy('orden')
                ->get();
        });
    }

    public function galeria(): Collection
    {
        return Cache::remember('public_galeria', now()->addMinutes(10), function () {
            return Galeria::query()
                ->where('activo', true)
                ->orderBy('orden')
                ->get();
        });
    }

    public function contactos(): Collection
    {
        return Cache::remember('public_contactos', now()->addMinutes(10), function () {
            return Contacto::query()
                ->orderBy('orden')
                ->get();
        });
    }

    public function redesSociales(): Collection
    {
        return Cache::remember('public_redes_sociales', now()->addMinutes(10), function () {
            return RedSocial::query()
                ->orderBy('orden')
                ->get(['id', 'nombre', 'url', 'icono']);
        });
    }

    public function paginaNosotros(): ?PaginaNosotros
    {
        return Cache::remember('public_pagina_nosotros', now()->addMinutes(10), function () {
            $pagina = PaginaNosotros::query()
                ->with([
                    'imagenes' => fn ($q) => $q->orderBy('orden'),
                    'progreso' => fn ($q) => $q->orderBy('orden'),
                ])
                ->first();

            return $pagina && $pagina->estado ? $pagina : null;
        });
    }

    public function paginaHistoria(): ?PaginaHistoria
    {
        return Cache::remember('public_pagina_historia', now()->addMinutes(10), function () {
            $pagina = PaginaHistoria::query()
                ->with([
                    'eventos' => fn ($q) => $q->orderBy('orden'),
                    'imagenes' => fn ($q) => $q->orderBy('orden'),
                ])
                ->first();

            return $pagina && $pagina->estado ? $pagina : null;
        });
    }

    public function paginaTecnologia(): ?PaginaTecnologia
    {
        return Cache::remember('public_pagina_tecnologia', now()->addMinutes(10), function () {
            $pagina = PaginaTecnologia::query()
                ->with([
                    'secciones' => fn ($q) => $q->orderBy('orden'),
                ])
                ->first();

            return $pagina && $pagina->estado ? $pagina : null;
        });
    }

    public function paginaAviso(): ?PaginaAviso
    {
        return Cache::remember('public_pagina_aviso', now()->addMinutes(10), function () {
            $pagina = PaginaAviso::query()
                ->with([
                    'secciones' => fn ($q) => $q->orderBy('orden'),
                    'secciones.listas' => fn ($q) => $q->orderBy('orden'),
                ])
                ->first();

            return $pagina && $pagina->estado ? $pagina : null;
        });
    }

    public function enviarContacto(array $validated): FormularioContacto
    {
        return FormularioContacto::create($validated + ['leido' => false]);
    }
}
