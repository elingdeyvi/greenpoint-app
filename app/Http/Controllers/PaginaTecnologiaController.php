<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePaginaTecnologiaRequest;
use App\Models\PaginaTecnologia;
use App\Services\PaginaTecnologiaService;
use App\Services\PublicSiteCacheService;
use Illuminate\Http\JsonResponse;

class PaginaTecnologiaController extends Controller
{
    public function __construct(
        private readonly PaginaTecnologiaService $service,
        private readonly PublicSiteCacheService $publicCache
    ) {
    }

    public function show(): JsonResponse
    {
        $pagina = PaginaTecnologia::query()
            ->with(['secciones' => fn ($q) => $q->orderBy('orden')])
            ->first();

        if (!$pagina) {
            $pagina = PaginaTecnologia::create([
                'titulo' => 'Tecnología',
                'estado' => true,
            ])->load('secciones');
        }

        return response()->json($pagina, JsonResponse::HTTP_OK);
    }

    public function update(UpdatePaginaTecnologiaRequest $request): JsonResponse
    {
        $pagina = PaginaTecnologia::query()->first();

        if (!$pagina) {
            $pagina = PaginaTecnologia::create([
                'titulo' => $request->input('titulo', 'Tecnología'),
                'estado' => $request->boolean('estado', true),
            ]);
        }

        $paginaActualizada = $this->service->updateFromRequest($pagina, $request);
        $this->publicCache->invalidatePaginaTecnologia();

        return response()->json($paginaActualizada, JsonResponse::HTTP_OK);
    }
}
