<?php

namespace App\Http\Controllers;

use App\Http\Requests\CierreCajaRequest;
use App\Models\Caja;
use App\Models\ConfiguracionEmpresa;
use App\Services\CajaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CajaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Caja::with(['sucursal', 'usuario', 'gastos']);
        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }
        $cajas = $query->orderByDesc('fecha_apertura')->paginate(20);
        return response()->json(['data' => $cajas], 200);
    }

    public function apertura(Request $request): JsonResponse
    {
        $sucursalId = $request->sucursal_id;
        if ($sucursalId === null || $sucursalId === '') {
            $config = ConfiguracionEmpresa::obtenerConfiguracion();
            $sucursalId = $config?->sucursal_id;
        }

        $validator = Validator::make(array_merge($request->all(), ['sucursal_id' => $sucursalId]), [
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'monto_inicial' => ['required', 'numeric', 'min:0'],
        ], [
            'sucursal_id.required' => 'La sucursal es obligatoria. Configure la sucursal en Configuración de la empresa.',
            'monto_inicial.required' => 'El monto inicial es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Saneamiento defensivo: si hubiera cajas "abiertas" sin fecha_apertura (inconsistentes),
        // marcarlas como cerradas para permitir una nueva apertura limpia.
        Caja::where('sucursal_id', $sucursalId)
            ->where('estatus', 'abierta')
            ->whereNull('fecha_apertura')
            ->update(['estatus' => 'cerrada']);

        $abierta = Caja::where('sucursal_id', $sucursalId)->where('estatus', 'abierta')->first();
        if ($abierta) {
            return response()->json([
                'errors' => ['caja' => ['Ya existe una caja abierta en esta sucursal. Cierre la caja actual antes de abrir otra.']],
            ], 422);
        }

        $caja = Caja::create([
            'sucursal_id' => $sucursalId,
            'usuario_id' => auth()->id(),
            'monto_inicial' => (float) $request->monto_inicial,
            'estatus' => 'abierta',
            'fecha_apertura' => now(),
        ]);
        $caja->load(['sucursal', 'usuario']);

        return response()->json(['data' => $caja, 'message' => 'Caja abierta correctamente.'], 201);
    }

    public function corte(CierreCajaRequest $request, CajaService $cajaService): JsonResponse
    {
        $caja = Caja::with(['sucursal', 'usuario', 'gastos'])->findOrFail($request->validated('caja_id'));
        if ($caja->estatus === 'cerrada') {
            return response()->json(['errors' => ['caja' => ['Esta caja ya está cerrada.']]], 422);
        }

        $monto_final = (float) $request->validated('monto_final');
        $resultado = $cajaService->procesarCierre($caja, $monto_final);

        return response()->json([
            'data' => [
                'caja' => $resultado['caja'],
                'reporte_x' => $resultado['reporte_x'],
                'reporte_z' => $resultado['reporte_z'],
            ],
            'message' => 'Caja cerrada. Reportes X y Z generados.',
        ], 200);
    }

    public function cajaAbierta(Request $request): JsonResponse
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursal = $config?->sucursal;

        // Si no hay sucursal configurada o está inactiva, no se considera caja abierta
        if (! $sucursal || ! $sucursal->activo) {
            return response()->json(['data' => null], 200);
        }

        $caja = Caja::with(['sucursal', 'usuario', 'gastos'])
            ->where('sucursal_id', $sucursal->id)
            ->where('estatus', 'abierta')
            ->whereNotNull('fecha_apertura')
            ->first();

        return response()->json(['data' => $caja], 200);
    }
}
