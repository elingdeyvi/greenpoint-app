<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Services\ImageService;
use App\Services\PublicSiteCacheService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct(
        private readonly ImageService $imageService,
        private readonly PublicSiteCacheService $publicCache
    )
    {
        // Comentario: Middleware de permisos se puede aplicar aquí si es necesario.
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->get('per_page', 15));

        $query = Cliente::query()->orderBy('orden')->orderBy('id');

        return response()->json(
            $query->paginate($perPage),
            JsonResponse::HTTP_OK
        );
    }

    public function store(StoreClienteRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->imageService->storeImage($request->file('logo'), 'clientes');
        }

        $cliente = Cliente::create($data);
        $this->publicCache->invalidateClientes();

        return response()->json($cliente, JsonResponse::HTTP_CREATED);
    }

    public function show(Cliente $cliente): JsonResponse
    {
        return response()->json($cliente, JsonResponse::HTTP_OK);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $this->imageService->deleteImage($cliente->logo);
            $data['logo'] = $this->imageService->storeImage($request->file('logo'), 'clientes');
        }

        $cliente->update($data);
        $this->publicCache->invalidateClientes();

        return response()->json($cliente->refresh(), JsonResponse::HTTP_OK);
    }

    public function destroy(Cliente $cliente): JsonResponse
    {
        $this->imageService->deleteImage($cliente->logo);
        $cliente->delete();
        $this->publicCache->invalidateClientes();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}

