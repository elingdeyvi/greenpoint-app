<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use Illuminate\Http\JsonResponse;

class UnidadMedidaController extends Controller
{
    public function index(): JsonResponse
    {
        $unidades = UnidadMedida::where('activo', true)->orderBy('codigo')->get();
        return response()->json(['data' => $unidades], 200);
    }
}
