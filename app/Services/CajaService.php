<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\PagoDetalle;
use App\Models\Venta;
use Illuminate\Support\Collection;

class CajaService
{
    /**
     * Procesa el cierre de caja: actualiza estatus y monto_final, calcula reportes X y Z.
     * Regla crítica: total_cobrado solo suma pagos de ventas donde es_donativo = false.
     * Donativos se informan como valor referencial sin afectar la diferencia de efectivo.
     * Fórmula de arqueo: (monto_inicial + total_efectivo) - total_gastos - monto_final_reportado.
     *
     * @return array{caja: Caja, reporte_x: array, reporte_z: array}
     */
    public function procesarCierre(Caja $caja, float $monto_final): array
    {
        $caja->monto_final = $monto_final;
        $caja->fecha_cierre = now();
        $caja->estatus = 'cerrada';
        $caja->save();

        $fecha_apertura = $caja->fecha_apertura;
        $fecha_cierre = $caja->fecha_cierre;

        $ventas_periodo = Venta::where('sucursal_id', $caja->sucursal_id)
            ->where('estatus', '!=', 'cancelado')
            ->whereBetween('created_at', [$fecha_apertura, $fecha_cierre])
            ->get();

        $ventas_ingreso = $ventas_periodo->filter(fn (Venta $v) => ! $v->es_donativo && $v->tipo !== 'donativo');
        $ventas_donativo = $ventas_periodo->filter(fn (Venta $v) => $v->es_donativo || $v->tipo === 'donativo');

        $total_ventas_ingreso = (float) $ventas_ingreso->sum('total');
        $total_donativos = (float) $ventas_donativo->sum('total');

        // Ingresos iterando sobre pago_detalles: solo ventas no donativo (total_cobrado no incluye donativos)
        $pagos_periodo = PagoDetalle::with('venta')
            ->whereHas('venta', function ($q) use ($caja, $fecha_apertura, $fecha_cierre) {
                $q->where('sucursal_id', $caja->sucursal_id)
                    ->where('estatus', '!=', 'cancelado')
                    ->where('tipo', '!=', 'donativo')
                    ->where('es_donativo', false)
                    ->whereBetween('created_at', [$fecha_apertura, $fecha_cierre]);
            })
            ->get();

        $total_cobrado = (float) $pagos_periodo->sum('monto');
        $pagos_por_metodo = $pagos_periodo
            ->groupBy('metodo_pago')
            ->map(fn (Collection $grupo) => (float) $grupo->sum('monto'))
            ->toArray();

        $total_efectivo = (float) ($pagos_por_metodo['efectivo'] ?? 0.0);
        $total_gastos = (float) $caja->gastos->sum('monto');
        $monto_final_reportado = (float) $caja->monto_final;

        // Arqueo: (monto_inicial + total_efectivo) - total_gastos - monto_final_reportado
        $diferencia = ($caja->monto_inicial + $total_efectivo) - $total_gastos - $monto_final_reportado;

        $reporte_x = [
            'tipo' => 'X',
            'descripcion' => 'Reporte de ventas del período',
            'fecha_apertura' => $fecha_apertura->toIso8601String(),
            'fecha_cierre' => $fecha_cierre->toIso8601String(),
            'monto_inicial' => (float) $caja->monto_inicial,
            'total_ventas_ingreso' => $total_ventas_ingreso,
            'total_donativos' => $total_donativos,
            'total_cobrado' => $total_cobrado,
            'total_gastos' => $total_gastos,
            'pagos_por_metodo' => $pagos_por_metodo,
            'monto_final_reportado' => $caja->monto_final,
            'cantidad_ventas' => $ventas_periodo->count(),
        ];

        $reporte_z = [
            'tipo' => 'Z',
            'descripcion' => 'Cierre de jornada',
            'fecha_cierre' => $fecha_cierre->toIso8601String(),
            'monto_inicial' => (float) $caja->monto_inicial,
            'total_ventas_ingreso' => $total_ventas_ingreso,
            'total_donativos' => $total_donativos,
            'total_cobrado' => $total_cobrado,
            'total_gastos' => $total_gastos,
            'pagos_por_metodo' => $pagos_por_metodo,
            'monto_final' => $caja->monto_final,
            'diferencia' => round($diferencia, 2),
        ];

        $caja->load(['sucursal', 'usuario', 'gastos']);

        return [
            'caja' => $caja,
            'reporte_x' => $reporte_x,
            'reporte_z' => $reporte_z,
        ];
    }
}
