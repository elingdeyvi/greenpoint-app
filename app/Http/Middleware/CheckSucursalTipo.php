<?php

namespace App\Http\Middleware;

use App\Models\ConfiguracionEmpresa;
use App\Models\Sucursal;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSucursalTipo
{
    /**
     * Maneja una petición entrante verificando el tipo de sucursal activa.
     *
     * Uso en rutas:
     *  ->middleware('sucursal.tipo:venta_almacen')
     *  ->middleware('sucursal.tipo:venta')
     */
    public function handle(Request $request, Closure $next, string $tipoRequerido): Response
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursal = $config?->sucursal;

        if (! $sucursal || ! $sucursal->activo) {
            return response()->json([
                'message' => 'No hay una sucursal activa configurada para este equipo.',
            ], Response::HTTP_FORBIDDEN);
        }

        $tipoActual = $sucursal->tipo_sucursal;

        // Normalizar constantes del modelo Sucursal si se usan
        if ($tipoRequerido === 'venta' && $tipoActual !== Sucursal::TIPO_VENTA) {
            return response()->json([
                'message' => 'La sucursal actual no tiene permitido acceder a este recurso.',
            ], Response::HTTP_FORBIDDEN);
        }

        if ($tipoRequerido === 'venta_almacen' && $tipoActual !== Sucursal::TIPO_VENTA_ALMACEN) {
            return response()->json([
                'message' => 'La sucursal actual no tiene permitido acceder a este recurso.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}

