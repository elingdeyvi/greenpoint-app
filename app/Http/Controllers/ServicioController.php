<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServicioRequest;
use App\Http\Requests\UpdateServicioRequest;
use App\Models\Servicio;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function __construct(private readonly ImageService $imageService)
    {
        // Comentario: Se puede aplicar middleware de permisos a nivel controlador si se desea.
    }

    public function index(Request $request): JsonResponse
    {
        // Comentario: Listado paginado y ordenado de servicios.
        $perPage = (int) ($request->get('per_page', 15));

        $query = Servicio::query()->orderBy('orden')->orderBy('id');

        return response()->json(
            $query->paginate($perPage),
            JsonResponse::HTTP_OK
        );
    }

    public function store(StoreServicioRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->imageService->storeImage($request->file('imagen'), 'servicios');
        }

        $servicio = Servicio::create($data);

        return response()->json($servicio, JsonResponse::HTTP_CREATED);
    }

    public function show(Servicio $servicio): JsonResponse
    {
        return response()->json($servicio, JsonResponse::HTTP_OK);
    }

    public function update(UpdateServicioRequest $request, Servicio $servicio): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $this->imageService->deleteImage($servicio->imagen);
            $data['imagen'] = $this->imageService->storeImage($request->file('imagen'), 'servicios');
        }

        $servicio->update($data);

        return response()->json($servicio->refresh(), JsonResponse::HTTP_OK);
    }

    public function destroy(Servicio $servicio): JsonResponse
    {
        $this->imageService->deleteImage($servicio->imagen);
        $servicio->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}

