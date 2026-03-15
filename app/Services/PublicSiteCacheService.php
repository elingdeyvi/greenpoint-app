<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Cliente;
use App\Models\Contacto;
use App\Models\Galeria;
use App\Models\PaginaAviso;
use App\Models\PaginaHistoria;
use App\Models\PaginaNosotros;
use App\Models\PaginaTecnologia;
use App\Models\Configuracion;
use App\Models\Servicio;
use Illuminate\Support\Facades\Cache;

/**
 * Cache para la API pública del sitio. TTL por defecto 10 minutos.
 * Las claves se invalidan al actualizar datos desde el panel (controladores admin).
 */
class PublicSiteCacheService
{
    private const TTL_MINUTES = 10;

    private const KEY_BANNERS = 'public_banners';
    private const KEY_SERVICIOS = 'public_servicios';
    private const KEY_SERVICIO = 'public_servicio_';
    private const KEY_CLIENTES = 'public_clientes';
    private const KEY_GALERIA = 'public_galeria';
    private const KEY_CONTACTOS = 'public_contactos';
    private const KEY_PAGINA_NOSOTROS = 'public_pagina_nosotros';
    private const KEY_PAGINA_HISTORIA = 'public_pagina_historia';
    private const KEY_PAGINA_TECNOLOGIA = 'public_pagina_tecnologia';
    private const KEY_PAGINA_AVISO = 'public_pagina_aviso';
    private const KEY_CONFIGURACION = 'public_configuracion';

    /** Claves de configuración expuestas al sitio público (footer, WhatsApp, etc.) */
    private const PUBLIC_CONFIG_KEYS = [
        'telefono_general',
        'email_general',
        'whatsapp_url',
        'direccion_matriz',
        'footer_texto_empresa',
    ];

    public function getConfiguracionPublic(): array
    {
        return Cache::remember(self::KEY_CONFIGURACION, now()->addMinutes(self::TTL_MINUTES), function () {
            $rows = Configuracion::query()
                ->whereIn('clave', self::PUBLIC_CONFIG_KEYS)
                ->get(['clave', 'valor']);
            return $rows->pluck('valor', 'clave')->toArray();
        });
    }

    public function getBanners(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::KEY_BANNERS, now()->addMinutes(self::TTL_MINUTES), function () {
            return Banner::query()->where('activo', true)->orderBy('orden')->get();
        });
    }

    public function getServicios(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::KEY_SERVICIOS, now()->addMinutes(self::TTL_MINUTES), function () {
            return Servicio::query()->where('activo', true)->orderBy('orden')->get();
        });
    }

    public function getServicio(int $id): ?Servicio
    {
        return Cache::remember(
            self::KEY_SERVICIO . $id,
            now()->addMinutes(self::TTL_MINUTES),
            function () use ($id) {
                return Servicio::query()->where('activo', true)->find($id);
            }
        );
    }

    public function getClientes(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::KEY_CLIENTES, now()->addMinutes(self::TTL_MINUTES), function () {
            return Cliente::query()->where('activo', true)->orderBy('orden')->get();
        });
    }

    public function getGaleria(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::KEY_GALERIA, now()->addMinutes(self::TTL_MINUTES), function () {
            return Galeria::query()->where('activo', true)->orderBy('orden')->get();
        });
    }

    public function getContactos(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::KEY_CONTACTOS, now()->addMinutes(self::TTL_MINUTES), function () {
            return Contacto::query()->orderBy('orden')->get();
        });
    }

    public function getPaginaNosotros(): ?PaginaNosotros
    {
        return Cache::remember(self::KEY_PAGINA_NOSOTROS, now()->addMinutes(self::TTL_MINUTES), function () {
            return PaginaNosotros::query()
                ->with([
                    'imagenes' => fn ($q) => $q->orderBy('orden'),
                    'progreso' => fn ($q) => $q->orderBy('orden'),
                ])
                ->first();
        });
    }

    public function getPaginaHistoria(): ?PaginaHistoria
    {
        return Cache::remember(self::KEY_PAGINA_HISTORIA, now()->addMinutes(self::TTL_MINUTES), function () {
            return PaginaHistoria::query()
                ->with([
                    'eventos' => fn ($q) => $q->orderBy('orden'),
                    'imagenes' => fn ($q) => $q->orderBy('orden'),
                ])
                ->first();
        });
    }

    public function getPaginaTecnologia(): ?PaginaTecnologia
    {
        return Cache::remember(self::KEY_PAGINA_TECNOLOGIA, now()->addMinutes(self::TTL_MINUTES), function () {
            return PaginaTecnologia::query()
                ->with(['secciones' => fn ($q) => $q->orderBy('orden')])
                ->first();
        });
    }

    public function getPaginaAviso(): ?PaginaAviso
    {
        return Cache::remember(self::KEY_PAGINA_AVISO, now()->addMinutes(self::TTL_MINUTES), function () {
            return PaginaAviso::query()
                ->with(['secciones.listas' => fn ($q) => $q->orderBy('orden')])
                ->first();
        });
    }

    public function invalidateBanners(): void
    {
        Cache::forget(self::KEY_BANNERS);
    }

    public function invalidateServicios(): void
    {
        Cache::forget(self::KEY_SERVICIOS);
        $ids = Servicio::query()->pluck('id');
        foreach ($ids as $id) {
            Cache::forget(self::KEY_SERVICIO . $id);
        }
    }

    public function invalidateServicio(int $id): void
    {
        Cache::forget(self::KEY_SERVICIOS);
        Cache::forget(self::KEY_SERVICIO . $id);
    }

    public function invalidateClientes(): void
    {
        Cache::forget(self::KEY_CLIENTES);
    }

    public function invalidateGaleria(): void
    {
        Cache::forget(self::KEY_GALERIA);
    }

    public function invalidateContactos(): void
    {
        Cache::forget(self::KEY_CONTACTOS);
    }

    public function invalidatePaginaNosotros(): void
    {
        Cache::forget(self::KEY_PAGINA_NOSOTROS);
    }

    public function invalidatePaginaHistoria(): void
    {
        Cache::forget(self::KEY_PAGINA_HISTORIA);
    }

    public function invalidatePaginaTecnologia(): void
    {
        Cache::forget(self::KEY_PAGINA_TECNOLOGIA);
    }

    public function invalidatePaginaAviso(): void
    {
        Cache::forget(self::KEY_PAGINA_AVISO);
    }

    public function invalidateConfiguracion(): void
    {
        Cache::forget(self::KEY_CONFIGURACION);
    }

    public function invalidateAll(): void
    {
        $this->invalidateBanners();
        $this->invalidateServicios();
        $this->invalidateClientes();
        $this->invalidateGaleria();
        $this->invalidateContactos();
        $this->invalidatePaginaNosotros();
        $this->invalidatePaginaHistoria();
        $this->invalidatePaginaTecnologia();
        $this->invalidatePaginaAviso();
        $this->invalidateConfiguracion();
    }
}
