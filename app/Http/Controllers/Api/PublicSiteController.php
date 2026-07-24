<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use App\Services\PublicSiteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicSiteController extends Controller
{
    public function __construct(private readonly PublicSiteService $publicSiteService)
    {
    }

    public function home(): JsonResponse
    {
        return response()->json($this->publicSiteService->home(), JsonResponse::HTTP_OK);
    }

    public function serviciosIndex(): JsonResponse
    {
        return response()->json($this->publicSiteService->servicios(), JsonResponse::HTTP_OK);
    }

    public function serviciosShow(Servicio $servicio): JsonResponse
    {
        $servicio = $this->publicSiteService->servicio($servicio);

        if (!$servicio) {
            return response()->json(['message' => 'Not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json($servicio, JsonResponse::HTTP_OK);
    }

    public function clientesIndex(): JsonResponse
    {
        return response()->json($this->publicSiteService->clientes(), JsonResponse::HTTP_OK);
    }

    public function galeriaIndex(): JsonResponse
    {
        return response()->json($this->publicSiteService->galeria(), JsonResponse::HTTP_OK);
    }

    public function contactosIndex(): JsonResponse
    {
        return response()->json($this->publicSiteService->contactos(), JsonResponse::HTTP_OK);
    }

    public function redesSociales(): JsonResponse
    {
        return response()->json($this->publicSiteService->redesSociales(), JsonResponse::HTTP_OK);
    }

    public function paginaNosotros(): JsonResponse
    {
        return response()->json($this->publicSiteService->paginaNosotros(), JsonResponse::HTTP_OK);
    }

    public function paginaHistoria(): JsonResponse
    {
        return response()->json($this->publicSiteService->paginaHistoria(), JsonResponse::HTTP_OK);
    }

    public function paginaTecnologia(): JsonResponse
    {
        return response()->json($this->publicSiteService->paginaTecnologia(), JsonResponse::HTTP_OK);
    }

    public function paginaAviso(): JsonResponse
    {
        return response()->json($this->publicSiteService->paginaAviso(), JsonResponse::HTTP_OK);
    }

    public function enviarFormularioContacto(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'mensaje' => ['required', 'string'],
        ]);

        $formulario = $this->publicSiteService->enviarContacto($validated);

        return response()->json([
            'success' => true,
            'data' => $formulario,
        ], JsonResponse::HTTP_CREATED);
    }
}
