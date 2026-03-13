<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePaginaNosotrosRequest;
use App\Models\PaginaNosotros;
use App\Services\PaginaNosotrosService;
use Illuminate\Http\JsonResponse;

class PaginaNosotrosController extends Controller
{
    public function __construct(private readonly PaginaNosotrosService $service)
    {
        // Comentario: Se puede aplicar middleware de permisos para modulos.nosotros.
    }

    public function show(): JsonResponse
    {
        // Comentario: Se asume una sola fila para la página Nosotros.
        $pagina = PaginaNosotros::query()
            ->with([
                'imagenes' => fn ($q) => $q->orderBy('orden'),
                'progreso' => fn ($q) => $q->orderBy('orden'),
            ])
            ->first();

        if (!$pagina) {
            $pagina = PaginaNosotros::create([
                'titulo' => 'Nosotros',
                'estado' => true,
            ])->load('imagenes', 'progreso');
        }

        return response()->json($pagina, JsonResponse::HTTP_OK);
    }

    public function update(UpdatePaginaNosotrosRequest $request): JsonResponse
    {
        $pagina = PaginaNosotros::query()->first();

        if (!$pagina) {
            $pagina = PaginaNosotros::create([
                'titulo' => $request->input('titulo', 'Nosotros'),
                'estado' => $request->boolean('estado', true),
            ]);
        }

        $paginaActualizada = $this->service->updateFromRequest($pagina, $request);

        return response()->json($paginaActualizada, JsonResponse::HTTP_OK);
    }
}

