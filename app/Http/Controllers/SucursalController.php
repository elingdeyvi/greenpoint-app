<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\JsonResponse;

class SucursalController extends Controller
{
    public function index(): JsonResponse
    {
        $sucursales = Sucursal::where('activo', true)->orderBy('nombre')->get();
        return response()->json(['data' => $sucursales], 200);
    }
}
