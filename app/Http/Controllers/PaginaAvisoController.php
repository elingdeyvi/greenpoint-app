<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePaginaAvisoRequest;
use App\Models\PaginaAviso;
use App\Services\PaginaAvisoService;
use App\Services\PublicSiteCacheService;
use Illuminate\Http\JsonResponse;

class PaginaAvisoController extends Controller
{
    public function __construct(
        private readonly PaginaAvisoService $service,
        private readonly PublicSiteCacheService $publicCache
    ) {
    }

    public function show(): JsonResponse
    {
        $pagina = PaginaAviso::query()
            ->with(['secciones.listas' => fn ($q) => $q->orderBy('orden')])
            ->first();

        if (!$pagina) {
            $pagina = PaginaAviso::create([
                'titulo' => 'Aviso de Privacidad',
                'estado' => true,
            ])->load('secciones.listas');
        }

        return response()->json($pagina, JsonResponse::HTTP_OK);
    }

    public function update(UpdatePaginaAvisoRequest $request): JsonResponse
    {
        $pagina = PaginaAviso::query()->first();

        if (!$pagina) {
            $pagina = PaginaAviso::create([
                'titulo' => $request->input('titulo', 'Aviso de Privacidad'),
                'estado' => $request->boolean('estado', true),
            ]);
        }

        $paginaActualizada = $this->service->updateFromRequest($pagina, $request);
        $this->publicCache->invalidatePaginaAviso();

        return response()->json($paginaActualizada, JsonResponse::HTTP_OK);
    }
}
