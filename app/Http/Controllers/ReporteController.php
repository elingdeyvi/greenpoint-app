<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\ConfiguracionEmpresa;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteController extends Controller
{
    /**
     * Reporte de salidas con filtros
     */
    public function salidas(Request $request): JsonResponse
    {
        $query = Boleto::with(['usuarioGenerador', 'usuarioValidador']);

        // Filtros
        if ($request->has('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        if ($request->has('usuario_generador_id')) {
            $query->where('usuario_generador_id', $request->usuario_generador_id);
        }

        if ($request->has('folio')) {
            $query->where('folio', 'like', '%' . $request->folio . '%');
        }

        if ($request->has('fecha_desde')) {
            $query->whereDate('fecha_generacion', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('fecha_generacion', '<=', $request->fecha_hasta);
        }

        if ($request->has('placa')) {
            $query->where('placa', 'like', '%' . $request->placa . '%');
        }

        $boletos = $query->orderBy('fecha_generacion', 'desc')->paginate($request->per_page ?? 50);

        return response()->json(['data' => $boletos], JsonResponse::HTTP_OK);
    }

    /**
     * Estadísticas generales para dashboard y reportes
     */
    public function estadisticas(Request $request): JsonResponse
    {
        $query = Boleto::query();

        if ($request->has('fecha_desde')) {
            $query->whereDate('fecha_generacion', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('fecha_generacion', '<=', $request->fecha_hasta);
        }

        $hoy = Carbon::today()->toDateString();
        $hasta = $request->get('fecha_hasta', $hoy);
        $desde = $request->get('fecha_desde', Carbon::parse($hasta)->subDays(30)->toDateString());

        // Boletos generados hoy (siempre del día actual, sin filtrar por rango)
        $hoyCount = Boleto::whereDate('fecha_generacion', $hoy)->count();
        // Salidas (validados) hoy
        $salidasHoyCount = Boleto::where('estatus', 'utilizado')->whereDate('fecha_validacion', $hoy)->count();

        // Por día: total de boletos generados por fecha (orden ascendente para la gráfica)
        $porDia = (clone $query)
            ->select(DB::raw('DATE(fecha_generacion) as fecha'), DB::raw('count(*) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->limit(90)
            ->get();

        // Por día de la semana (últimos 7 días): Dom, Lun, Mar, Mié, Jue, Vie, Sáb
        $hace7Dias = Carbon::today()->subDays(7)->toDateString();
        $porDiaSemanaRaw = Boleto::whereDate('fecha_generacion', '>=', $hace7Dias)
            ->whereDate('fecha_generacion', '<=', $hoy)
            ->selectRaw('DAYOFWEEK(fecha_generacion) as dia, count(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia')
            ->toArray();
        $porDiaSemana = [];
        for ($i = 1; $i <= 7; $i++) {
            $porDiaSemana[] = (int) ($porDiaSemanaRaw[$i] ?? 0);
        }

        // Salidas (utilizados) por día - últimos 10 días para sparkline
        $hace9Dias = Carbon::today()->subDays(9)->toDateString();
        $salidasPorDia = Boleto::where('estatus', 'utilizado')
            ->whereDate('fecha_validacion', '>=', $hace9Dias)
            ->whereDate('fecha_validacion', '<=', $hoy)
            ->select(DB::raw('DATE(fecha_validacion) as fecha'), DB::raw('count(*) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get()
            ->keyBy('fecha');

        // Rellenar los 10 días (hoy + 9 anteriores) para que el frontend tenga siempre 10 puntos
        $salidasPorDiaArray = [];
        for ($i = 9; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i)->toDateString();
            $row = $salidasPorDia->get($fecha);
            $salidasPorDiaArray[] = [
                'fecha' => $fecha,
                'total' => $row ? (is_array($row) ? ($row['total'] ?? 0) : $row->total) : 0,
            ];
        }

        $estadisticas = [
            'total' => $query->count(),
            'pendientes' => (clone $query)->where('estatus', 'pendiente')->count(),
            'utilizados' => (clone $query)->where('estatus', 'utilizado')->count(),
            'cancelados' => (clone $query)->where('estatus', 'cancelado')->count(),
            'hoy' => $hoyCount,
            'salidas_hoy' => $salidasHoyCount,
            'por_dia' => $porDia,
            'por_dia_semana' => $porDiaSemana,
            'salidas_por_dia' => array_column($salidasPorDiaArray, 'total'),
        ];

        return response()->json(['data' => $estadisticas], JsonResponse::HTTP_OK);
    }

    /**
     * Estadísticas unificadas para el dashboard. Filtradas por sucursal activa donde aplique.
     * Devuelve perfil (VENTA | VENTA_ALMACEN), boletos, ventas y opcionalmente donativos/alertas.
     */
    public function dashboardEstadisticas(Request $request): JsonResponse
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursalActivaId = $config?->sucursal_id;
        $sucursal = $sucursalActivaId ? Sucursal::find($sucursalActivaId) : null;
        $perfil = $sucursal ? ($sucursal->isSucursalVentaAlmacen() ? 'VENTA_ALMACEN' : 'VENTA') : null;

        $fechaHasta = $request->get('fecha_hasta', Carbon::today()->toDateString());
        $fechaDesde = $request->get('fecha_desde', Carbon::parse($fechaHasta)->subDays(30)->toDateString());
        $hoy = Carbon::today()->toDateString();

        // Boletos (misma lógica que estadisticas())
        $queryBoletos = Boleto::query()
            ->whereDate('fecha_generacion', '>=', $fechaDesde)
            ->whereDate('fecha_generacion', '<=', $fechaHasta);
        $hoyCount = Boleto::whereDate('fecha_generacion', $hoy)->count();
        $salidasHoyCount = Boleto::where('estatus', 'utilizado')->whereDate('fecha_validacion', $hoy)->count();
        $porDia = (clone $queryBoletos)
            ->select(DB::raw('DATE(fecha_generacion) as fecha'), DB::raw('count(*) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->limit(90)
            ->get();
        $hace7Dias = Carbon::today()->subDays(7)->toDateString();
        $porDiaSemanaRaw = Boleto::whereDate('fecha_generacion', '>=', $hace7Dias)
            ->whereDate('fecha_generacion', '<=', $hoy)
            ->selectRaw('DAYOFWEEK(fecha_generacion) as dia, count(*) as total')
            ->groupBy('dia')
            ->pluck('total', 'dia')
            ->toArray();
        $porDiaSemana = [];
        for ($i = 1; $i <= 7; $i++) {
            $porDiaSemana[] = (int) ($porDiaSemanaRaw[$i] ?? 0);
        }
        $hace9Dias = Carbon::today()->subDays(9)->toDateString();
        $salidasPorDia = Boleto::where('estatus', 'utilizado')
            ->whereDate('fecha_validacion', '>=', $hace9Dias)
            ->whereDate('fecha_validacion', '<=', $hoy)
            ->select(DB::raw('DATE(fecha_validacion) as fecha'), DB::raw('count(*) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get()
            ->keyBy('fecha');
        $salidasPorDiaArray = [];
        for ($i = 9; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i)->toDateString();
            $row = $salidasPorDia->get($fecha);
            $salidasPorDiaArray[] = [
                'fecha' => $fecha,
                'total' => $row ? (is_array($row) ? ($row['total'] ?? 0) : $row->total) : 0,
            ];
        }
        $boletos = [
            'total' => $queryBoletos->count(),
            'pendientes' => (clone $queryBoletos)->where('estatus', 'pendiente')->count(),
            'utilizados' => (clone $queryBoletos)->where('estatus', 'utilizado')->count(),
            'cancelados' => (clone $queryBoletos)->where('estatus', 'cancelado')->count(),
            'hoy' => $hoyCount,
            'salidas_hoy' => $salidasHoyCount,
            'por_dia' => $porDia,
            'por_dia_semana' => $porDiaSemana,
            'salidas_por_dia' => array_column($salidasPorDiaArray, 'total'),
        ];

        $ventas = null;
        $donativos_total = null;
        $alertas_inventario = null;
        $pendientes_cobro = null;
        $entregas_hoy = null;
        $volumen_entregado_hoy = null;
        $productos_stock_bajo = null;
        $valor_inventario = null;

        if ($sucursalActivaId) {
            $baseVentas = Venta::query()
                ->where('sucursal_id', $sucursalActivaId)
                ->where('estatus', '!=', 'cancelado')
                ->whereBetween(DB::raw('DATE(created_at)'), [$fechaDesde, $fechaHasta]);
            $queryVentas = (clone $baseVentas)
                ->where(function ($q) {
                    $q->where('tipo', '!=', 'donativo')->where('es_donativo', false);
                });
            $porDiaVentas = (clone $queryVentas)
                ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(total) as total'), 'sucursal_id')
                ->groupBy('fecha', 'sucursal_id')
                ->orderBy('fecha', 'asc')
                ->get();
            $fechasUnicas = $porDiaVentas->pluck('fecha')->unique()->sort()->values();
            $seriesVentas = [[
                'name' => $sucursal?->nombre ?? 'Sucursal',
                'data' => $fechasUnicas->map(function ($f) use ($porDiaVentas, $sucursalActivaId) {
                    $r = $porDiaVentas->where('fecha', $f)->where('sucursal_id', $sucursalActivaId)->first();
                    return $r ? (float) $r->total : 0;
                })->values()->toArray(),
            ]];
            $ventas = [
                'total_ventas' => (float) (clone $queryVentas)->sum('total'),
                'cantidad_ventas' => (int) (clone $queryVentas)->count(),
                'por_dia' => $porDiaVentas->toArray(),
                'fechas' => $fechasUnicas->values()->toArray(),
                'series_por_sucursal' => $seriesVentas,
            ];
            // Donativos (solo sucursal activa): total monetario o cantidad
            $donativos_total = (float) (clone $baseVentas)
                ->where(function ($q) {
                    $q->where('tipo', 'donativo')->orWhere('es_donativo', true);
                })
                ->sum('total');
            // Métricas adicionales solo para sucursal tipo venta_almacen (Macuspana)
            if ($perfil === 'VENTA_ALMACEN') {
                // Alertas inventario (productos con stock <= 0)
                $alertas_inventario = Producto::where('activo', true)
                    ->whereRaw('stock_actual <= 0')
                    ->count();
                // Productos con stock por debajo del mínimo configurado (>0)
                $productos_stock_bajo = Producto::where('activo', true)
                    ->where('stock_minimo', '>', 0)
                    ->whereColumn('stock_actual', '<', 'stock_minimo')
                    ->count();
                // Valor total de inventario (precio_unitario * stock_actual)
                $valor_inventario = (float) Producto::where('activo', true)
                    ->sum(DB::raw('precio_unitario * stock_actual'));
                // Pedidos pendientes de cobro en oficina (estatus pendiente_pago)
                $pendientes_cobro = Venta::where('sucursal_id', $sucursalActivaId)
                    ->where('estatus', 'pendiente_pago')
                    ->count();
                // Resumen de entregas del día (viajes y volumen)
                $entregas_hoy = \App\Models\Entrega::whereDate('created_at', $hoy)->count();
                $volumen_entregado_hoy = (float) \App\Models\Entrega::whereDate('created_at', $hoy)
                    ->sum('cantidad_despachada');
            }
        }

        $payload = [
            'perfil' => $perfil,
            'boletos' => $boletos,
            'ventas' => $ventas,
            'donativos_total' => $donativos_total,
            'alertas_inventario' => $alertas_inventario,
            'pendientes_cobro' => $pendientes_cobro,
            'entregas_hoy' => $entregas_hoy,
            'volumen_entregado_hoy' => $volumen_entregado_hoy,
            'productos_stock_bajo' => $productos_stock_bajo,
            'valor_inventario' => $valor_inventario,
        ];

        return response()->json(['data' => $payload], JsonResponse::HTTP_OK);
    }

    /**
     * Exportar a CSV
     */
    public function exportarCsv(Request $request): JsonResponse
    {
        $query = Boleto::with(['usuarioGenerador', 'usuarioValidador']);

        // Aplicar mismos filtros que salidas
        if ($request->has('estatus')) {
            $query->where('estatus', $request->estatus);
        }
        if ($request->has('fecha_desde')) {
            $query->whereDate('fecha_generacion', '>=', $request->fecha_desde);
        }
        if ($request->has('fecha_hasta')) {
            $query->whereDate('fecha_generacion', '<=', $request->fecha_hasta);
        }

        $boletos = $query->orderBy('fecha_generacion', 'desc')->get();

        // Generar CSV
        $csv = "Folio,Placa,Conductor,Estatus,Generado Por,Validado Por,Fecha Generación,Fecha Validación\n";

        foreach ($boletos as $boleto) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s\n",
                $boleto->folio,
                $boleto->placa,
                $boleto->conductor ?? '',
                $boleto->estatus,
                $boleto->usuarioGenerador->name ?? '',
                $boleto->usuarioValidador->name ?? '',
                $boleto->fecha_generacion,
                $boleto->fecha_validacion ?? ''
            );
        }

        return response()->json([
            'data' => base64_encode($csv),
            'filename' => 'reporte_salidas_' . date('Y-m-d') . '.csv'
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Estadísticas de ventas para gráficos: por día y por sucursal.
     * Seguridad: solo datos de la sucursal activa (ConfiguracionEmpresa).
     */
    public function estadisticasVentas(Request $request): JsonResponse
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursalActivaId = $config?->sucursal_id;
        if ($sucursalActivaId === null) {
            return response()->json(['errors' => ['sucursal' => ['No hay sucursal configurada.']]], 422);
        }
        if ($request->filled('sucursal_id') && (int) $request->sucursal_id !== (int) $sucursalActivaId) {
            return response()->json(['errors' => ['sucursal' => ['Solo puede consultar datos de su sucursal activa.']]], 403);
        }

        $fechaHasta = $request->get('fecha_hasta', Carbon::today()->toDateString());
        $fechaDesde = $request->get('fecha_desde', Carbon::parse($fechaHasta)->subDays(30)->toDateString());

        $base = Venta::query()
            ->where('sucursal_id', $sucursalActivaId)
            ->where('estatus', '!=', 'cancelado')
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaDesde, $fechaHasta]);

        // Para ingresos solo consideramos ventas no donativo
        $queryVentas = (clone $base)
            ->where(function ($q) {
                $q->where('tipo', '!=', 'donativo')
                    ->where('es_donativo', false);
            });

        $porDia = (clone $queryVentas)
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(total) as total'), 'sucursal_id')
            ->groupBy('fecha', 'sucursal_id')
            ->orderBy('fecha', 'asc')
            ->get();

        $porSucursal = (clone $queryVentas)
            ->select('sucursal_id', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(total) as total_ventas'))
            ->groupBy('sucursal_id')
            ->with('sucursal:id,nombre')
            ->get()
            ->map(function ($row) {
                return [
                    'sucursal_id' => $row->sucursal_id,
                    'sucursal_nombre' => $row->sucursal->nombre ?? 'N/A',
                    'cantidad' => (int) $row->cantidad,
                    'total_ventas' => (float) $row->total_ventas,
                ];
            });

        $fechasUnicas = $porDia->pluck('fecha')->unique()->sort()->values();
        $sucursalesIds = $porDia->pluck('sucursal_id')->unique()->filter();
        $sucursales = Sucursal::whereIn('id', $sucursalesIds)->get()->keyBy('id');
        $seriesPorSucursal = [];
        foreach ($sucursales as $id => $suc) {
            $seriesPorSucursal[] = [
                'name' => $suc->nombre ?? 'Sucursal '.$id,
                'data' => $fechasUnicas->map(function ($f) use ($porDia, $id) {
                    $r = $porDia->where('fecha', $f)->where('sucursal_id', $id)->first();
                    return $r ? (float) $r->total : 0;
                })->values()->toArray(),
            ];
        }

        return response()->json([
            'data' => [
                'por_dia' => $porDia->toArray(),
                'por_sucursal' => $porSucursal->toArray(),
                'fechas' => $fechasUnicas->values()->toArray(),
                'series_por_sucursal' => $seriesPorSucursal,
                'total_ventas' => (float) (clone $queryVentas)->sum('total'),
                'cantidad_ventas' => (int) (clone $queryVentas)->count(),
            ],
        ], 200);
    }

    /**
     * Exportar ventas a CSV (Excel-compatible). Solo sucursal activa.
     */
    public function exportarVentasExcel(Request $request): JsonResponse
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursalActivaId = $config?->sucursal_id;
        if ($sucursalActivaId === null) {
            return response()->json(['errors' => ['sucursal' => ['No hay sucursal configurada.']]], 422);
        }
        if ($request->filled('sucursal_id') && (int) $request->sucursal_id !== (int) $sucursalActivaId) {
            return response()->json(['errors' => ['sucursal' => ['Solo puede exportar datos de su sucursal activa.']]], 403);
        }

        $query = Venta::with(['sucursal', 'usuario', 'detalles.producto'])
            ->where('sucursal_id', $sucursalActivaId)
            ->where('estatus', '!=', 'cancelado');
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $ventas = $query->orderByDesc('created_at')->get();

        $csv = "Folio,Sucursal,Usuario,Total,Estatus,Tipo,Observaciones,Fecha,Creado\n";
        foreach ($ventas as $v) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $v->folio,
                $v->sucursal->nombre ?? '',
                $v->usuario->name ?? '',
                $v->total,
                $v->estatus,
                $v->tipo === 'donativo' || $v->es_donativo ? 'Donativo' : 'Venta',
                str_replace(["\r", "\n"], ' ', (string) $v->observaciones),
                $v->created_at?->format('Y-m-d H:i'),
                $v->created_at?->toIso8601String()
            );
        }

        return response()->json([
            'data' => base64_encode($csv),
            'filename' => 'reporte_ventas_' . date('Y-m-d') . '.csv',
        ], 200);
    }

    /**
     * Exportar reporte de ventas a PDF. Solo sucursal activa.
     */
    public function exportarVentasPdf(Request $request)
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursalActivaId = $config?->sucursal_id;
        if ($sucursalActivaId === null) {
            return response()->json(['errors' => ['sucursal' => ['No hay sucursal configurada.']]], 422);
        }
        if ($request->filled('sucursal_id') && (int) $request->sucursal_id !== (int) $sucursalActivaId) {
            return response()->json(['errors' => ['sucursal' => ['Solo puede exportar datos de su sucursal activa.']]], 403);
        }

        $query = Venta::with(['sucursal', 'usuario', 'detalles.producto'])
            ->where('sucursal_id', $sucursalActivaId)
            ->where('estatus', '!=', 'cancelado');
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $ventas = $query->orderByDesc('created_at')->get();
        $empresa = ConfiguracionEmpresa::obtenerConfiguracion();

        $pdf = Pdf::loadView('reportes.ventas-pdf', [
            'ventas' => $ventas,
            'nombreEmpresa' => $empresa?->nombre_empresa ?? config('app.name'),
            'fechaDesde' => $request->get('fecha_desde'),
            'fechaHasta' => $request->get('fecha_hasta'),
        ]);

        $filename = 'reporte_ventas_' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Reporte Excel/CSV: tickets entregados de Villahermosa (ventas con ticket_origen_uuid y estatus entregado),
     * con columnas de datos y URLs de fotos de la unidad en las entregas.
     */
    public function exportarTicketsVillahermosaExcel(Request $request): JsonResponse
    {
        $query = Venta::with(['sucursal', 'usuario', 'entregas'])
            ->whereNotNull('ticket_origen_uuid')
            ->where('estatus', 'entregado');

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $ventas = $query->orderByDesc('created_at')->get();

        $csv = "Folio,Ticket Origen UUID,Sucursal,Usuario,Total,Fecha Venta,Fotos (URLs)\n";
        foreach ($ventas as $v) {
            $fotosUrls = $v->entregas
                ->filter(fn ($e) => ! empty($e->foto_ruta))
                ->map(fn ($e) => asset('storage/' . $e->foto_ruta))
                ->implode('; ');
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,\"%s\"\n",
                $v->folio,
                $v->ticket_origen_uuid,
                $v->sucursal->nombre ?? '',
                $v->usuario->name ?? '',
                $v->total,
                $v->created_at?->format('Y-m-d H:i'),
                str_replace('"', '""', $fotosUrls)
            );
        }

        return response()->json([
            'data' => base64_encode($csv),
            'filename' => 'tickets_entregados_villahermosa_' . date('Y-m-d') . '.csv',
        ], 200);
    }
}

