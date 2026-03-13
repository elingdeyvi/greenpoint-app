<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateConfiguracionRequest;
use App\Models\Configuracion;
use Illuminate\Http\JsonResponse;

class ConfiguracionController extends Controller
{
    public function index(): JsonResponse
    {
        // Comentario: Devuelve todas las claves de configuración como arreglo.
        $items = Configuracion::query()
            ->orderBy('clave')
            ->get(['id', 'clave', 'valor']);

        return response()->json($items, JsonResponse::HTTP_OK);
    }

    public function update(UpdateConfiguracionRequest $request): JsonResponse
    {
        $items = $request->validated('items');

        foreach ($items as $item) {
            Configuracion::updateOrCreate(
                ['clave' => $item['clave']],
                ['valor' => $item['valor'] ?? null]
            );
        }

        $updated = Configuracion::whereIn('clave', collect($items)->pluck('clave'))
            ->orderBy('clave')
            ->get(['id', 'clave', 'valor']);

        return response()->json($updated, JsonResponse::HTTP_OK);
    }
}

