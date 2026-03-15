<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePaginaHistoriaRequest;
use App\Models\PaginaHistoria;
use App\Services\PaginaHistoriaService;
use App\Services\PublicSiteCacheService;
use Illuminate\Http\JsonResponse;

class PaginaHistoriaController extends Controller
{
    public function __construct(
        private readonly PaginaHistoriaService $service,
        private readonly PublicSiteCacheService $publicCache
    ) {
    }

    public function show(): JsonResponse
    {
        $pagina = PaginaHistoria::query()
            ->with([
                'eventos' => fn ($q) => $q->orderBy('orden'),
                'imagenes' => fn ($q) => $q->orderBy('orden'),
            ])
            ->first();

        if (!$pagina) {
            $pagina = PaginaHistoria::create([
                'titulo' => 'Historia',
                'estado' => true,
            ])->load('eventos', 'imagenes');
        }

        return response()->json($pagina, JsonResponse::HTTP_OK);
    }

    public function update(UpdatePaginaHistoriaRequest $request): JsonResponse
    {
        $pagina = PaginaHistoria::query()->first();

        if (!$pagina) {
            $pagina = PaginaHistoria::create([
                'titulo' => $request->input('titulo', 'Historia'),
                'estado' => $request->boolean('estado', true),
            ]);
        }

        $paginaActualizada = $this->service->updateFromRequest($pagina, $request);
        $this->publicCache->invalidatePaginaHistoria();

        return response()->json($paginaActualizada, JsonResponse::HTTP_OK);
    }
}
