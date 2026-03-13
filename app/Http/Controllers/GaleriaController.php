<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGaleriaRequest;
use App\Http\Requests\UpdateGaleriaRequest;
use App\Models\Galeria;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GaleriaController extends Controller
{
    public function __construct(private readonly ImageService $imageService)
    {
        // Comentario: Middleware de permisos se puede aplicar aquí si es necesario.
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->get('per_page', 15));

        $query = Galeria::query()->orderBy('orden')->orderBy('id');

        return response()->json(
            $query->paginate($perPage),
            JsonResponse::HTTP_OK
        );
    }

    public function store(StoreGaleriaRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->imageService->storeImage($request->file('imagen'), 'galeria');
        }

        $galeria = Galeria::create($data);

        return response()->json($galeria, JsonResponse::HTTP_CREATED);
    }

    public function show(Galeria $galeria): JsonResponse
    {
        return response()->json($galeria, JsonResponse::HTTP_OK);
    }

    public function update(UpdateGaleriaRequest $request, Galeria $galeria): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $this->imageService->deleteImage($galeria->imagen);
            $data['imagen'] = $this->imageService->storeImage($request->file('imagen'), 'galeria');
        }

        $galeria->update($data);

        return response()->json($galeria->refresh(), JsonResponse::HTTP_OK);
    }

    public function destroy(Galeria $galeria): JsonResponse
    {
        $this->imageService->deleteImage($galeria->imagen);
        $galeria->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}

