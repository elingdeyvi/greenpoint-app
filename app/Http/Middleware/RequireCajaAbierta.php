<?php

namespace App\Http\Middleware;

use App\Models\Caja;
use App\Models\ConfiguracionEmpresa;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireCajaAbierta
{
    /**
     * Verifica que exista una caja con estatus = 'abierta' para la sucursal activa.
     * Si no hay apertura, retorna 403.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursal = $config?->sucursal;

        if (! $sucursal) {
            return response()->json([
                'message' => 'No hay sucursal activa configurada.',
            ], Response::HTTP_FORBIDDEN);
        }

        $cajaAbierta = Caja::where('sucursal_id', $sucursal->id)
            ->where('estatus', 'abierta')
            ->exists();

        if (! $cajaAbierta) {
            return response()->json([
                'message' => 'Debe realizar la apertura de caja antes de procesar ventas.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
