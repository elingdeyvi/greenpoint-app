<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormularioContactoRequest;
use App\Models\FormularioContacto;
use App\Services\PublicSiteCacheService;
use Illuminate\Http\JsonResponse;

/**
 * API pública del sitio. Respuestas cacheadas (PublicSiteCacheService, TTL 10 min).
 *
 * SEO: Los endpoints devuelven los campos necesarios para meta tags y URLs amigables:
 * - pagina-nosotros, pagina-historia, pagina-tecnologia, pagina-aviso: titulo, meta_descripcion, meta_keywords.
 * - servicios (listado y detalle): nombre, slug (para URLs amigables), descripcion; slug también en GET /api/public/servicios/slug/{slug}.
 * Estructura de respuesta: el recurso completo (incl. meta_* y slug) según el modelo.
 */
class PublicSiteController extends Controller
{
    public function __construct(private readonly PublicSiteCacheService $cache)
    {
    }

    public function configuracion(): JsonResponse
    {
        return response()->json($this->cache->getConfiguracionPublic(), JsonResponse::HTTP_OK);
    }

    public function home(): JsonResponse
    {
        return response()->json([
            'banners' => $this->cache->getBanners(),
            'servicios' => $this->cache->getServicios(),
        ], JsonResponse::HTTP_OK);
    }

    public function serviciosIndex(): JsonResponse
    {
        return response()->json($this->cache->getServicios(), JsonResponse::HTTP_OK);
    }

    public function servicioBySlug(string $slug): JsonResponse
    {
        $servicio = \App\Models\Servicio::query()->where('slug', $slug)->where('activo', true)->first();
        if (!$servicio) {
            return response()->json(['message' => 'Not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $cached = $this->cache->getServicio($servicio->id);
        return response()->json($cached ?? $servicio, JsonResponse::HTTP_OK);
    }

    public function serviciosShow(\App\Models\Servicio $servicio): JsonResponse
    {
        $cached = $this->cache->getServicio($servicio->id);
        if (!$cached || !$cached->activo) {
            return response()->json(['message' => 'Not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        return response()->json($cached, JsonResponse::HTTP_OK);
    }

    public function clientesIndex(): JsonResponse
    {
        return response()->json($this->cache->getClientes(), JsonResponse::HTTP_OK);
    }

    public function galeriaIndex(): JsonResponse
    {
        return response()->json($this->cache->getGaleria(), JsonResponse::HTTP_OK);
    }

    public function contactosIndex(): JsonResponse
    {
        return response()->json($this->cache->getContactos(), JsonResponse::HTTP_OK);
    }

    public function paginaNosotros(): JsonResponse
    {
        return response()->json($this->cache->getPaginaNosotros(), JsonResponse::HTTP_OK);
    }

    public function paginaHistoria(): JsonResponse
    {
        return response()->json($this->cache->getPaginaHistoria(), JsonResponse::HTTP_OK);
    }

    public function paginaTecnologia(): JsonResponse
    {
        return response()->json($this->cache->getPaginaTecnologia(), JsonResponse::HTTP_OK);
    }

    public function paginaAviso(): JsonResponse
    {
        return response()->json($this->cache->getPaginaAviso(), JsonResponse::HTTP_OK);
    }

    public function enviarFormularioContacto(StoreFormularioContactoRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $mensaje = $validated['mensaje'];
        if (!empty($validated['asunto'] ?? '')) {
            $mensaje = 'Asunto: ' . $validated['asunto'] . "\n\n" . $mensaje;
        }
        $formulario = FormularioContacto::create([
            'nombre' => $validated['nombre'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
            'mensaje' => $mensaje,
            'leido' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $formulario,
        ], JsonResponse::HTTP_CREATED);
    }
}
