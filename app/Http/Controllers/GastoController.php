<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Gasto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GastoController extends Controller
{
    /**
     * Listar gastos de una caja.
     */
    public function index(Request $request, Caja $caja): JsonResponse
    {
        $gastos = $caja->gastos()->orderByDesc('created_at')->get();
        return response()->json(['data' => $gastos], 200);
    }

    /**
     * Registrar un gasto en la caja. La caja debe estar abierta.
     */
    public function store(Request $request, Caja $caja): JsonResponse
    {
        if ($caja->estatus !== 'abierta') {
            return response()->json([
                'errors' => ['caja' => ['Solo se pueden registrar gastos en caja abierta.']],
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => ['required', 'string', 'max:500'],
            'monto' => ['required', 'numeric', 'min:0.01'],
        ], [
            'descripcion.required' => 'La descripción es obligatoria.',
            'monto.required' => 'El monto es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $gasto = $caja->gastos()->create([
            'descripcion' => $request->descripcion,
            'monto' => (float) $request->monto,
        ]);

        return response()->json([
            'data' => $gasto,
            'message' => 'Gasto registrado.',
        ], 201);
    }
}
