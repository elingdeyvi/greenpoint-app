<?php

namespace App\Http\Controllers;

use App\Models\FormularioContacto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormularioContactoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->get('per_page', 15));

        $query = FormularioContacto::query()
            ->orderBy('leido')
            ->orderByDesc('created_at');

        return response()->json(
            $query->paginate($perPage),
            JsonResponse::HTTP_OK
        );
    }

    public function show(FormularioContacto $formularioContacto): JsonResponse
    {
        return response()->json($formularioContacto, JsonResponse::HTTP_OK);
    }

    public function update(Request $request, FormularioContacto $formularioContacto): JsonResponse
    {
        // Comentario: Solo se permite marcar como leído desde el panel.
        $request->validate([
            'leido' => ['required', 'boolean'],
        ]);

        $formularioContacto->update([
            'leido' => $request->boolean('leido'),
        ]);

        return response()->json($formularioContacto->refresh(), JsonResponse::HTTP_OK);
    }
}

