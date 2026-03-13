<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventarioAjusteRequest;
use App\Models\InventarioMovimiento;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Ajuste de inventario (stock en M3). Solo el Gerente de Producción (permiso inventario.ajustar).
 */
class InventarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * List current inventory for the active warehouse branch (venta_almacen).
     * Only available for users with inventario.consultar or inventario.ajustar.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user || (! $user->can('inventario.consultar') && ! $user->can('inventario.ajustar'))) {
            return response()->json([
                'errors' => ['permiso' => ['No tiene permiso para consultar inventario.']],
            ], 403);
        }

        $config = \App\Models\ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursal = $config?->sucursal;
        if (! $sucursal || ! $sucursal->isSucursalVentaAlmacen()) {
            return response()->json([
                'errors' => ['sucursal' => ['El módulo de inventario solo está disponible en sucursales tipo almacén (ej. Macuspana).']],
            ], 422);
        }

        $query = Producto::query()->where('activo', true);
        if ($search = trim((string) $request->query('q', ''))) {
            $query->where('nombre', 'like', '%' . $search . '%');
        }

        $productos = $query
            ->orderBy('nombre')
            ->get()
            ->map(function (Producto $p) {
                $valor = (float) $p->precio_unitario * (float) $p->stock_actual;
                return [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                    'stock_actual' => (float) $p->stock_actual,
                    'stock_minimo' => (float) ($p->stock_minimo ?? 0),
                    'unidad_medida' => $p->unidad_medida,
                    'unidad_medida_id' => $p->unidad_medida_id,
                    'precio_unitario' => (float) $p->precio_unitario,
                    'valor_inventario' => $valor,
                ];
            });

        return response()->json([
            'data' => $productos,
        ], 200);
    }

    /**
     * Ajustar stock de un producto. Requiere permiso inventario.ajustar (Gerente de Producción).
     */
    public function ajustar(InventarioAjusteRequest $request): JsonResponse
    {
        if (! auth()->user()->can('inventario.ajustar')) {
            return response()->json([
                'errors' => ['permiso' => ['No tiene permiso para ajustar inventario. Solo el Gerente de Producción puede realizar ajustes.']],
            ], 403);
        }

        $producto = Producto::findOrFail($request->validated('producto_id'));
        $valor = (float) $request->valor;
        $stockAnterior = (float) $producto->stock_actual;
        $tipoAjuste = $request->tipo;

        switch ($tipoAjuste) {
            case 'incremento':
                $producto->increment('stock_actual', $valor);
                break;
            case 'decremento':
                if ($valor > $stockAnterior) {
                    return response()->json([
                        'errors' => ['valor' => ['No puede decrementar más del stock actual (' . $stockAnterior . ' ' . $producto->unidad_medida . ').']],
                    ], 422);
                }
                $producto->decrement('stock_actual', $valor);
                break;
            case 'establecer':
                $producto->stock_actual = $valor;
                $producto->save();
                break;
        }

        $producto->refresh();

        // Registrar movimiento de inventario para tener historial de ajustes manuales.
        InventarioMovimiento::create([
            'producto_id' => $producto->id,
            'tipo' => 'ajuste',
            'cantidad' => $valor,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => (float) $producto->stock_actual,
            'motivo' => (string) $request->input('reason', ''),
            'usuario_id' => auth()->id(),
        ]);

        return response()->json([
            'data' => [
                'producto_id' => $producto->id,
                'nombre' => $producto->nombre,
                'stock_anterior' => $stockAnterior,
                'stock_actual' => (float) $producto->stock_actual,
                'unidad_medida' => $producto->unidad_medida,
            ],
            'message' => 'Inventario ajustado correctamente.',
        ], 200);
    }

    /**
     * Alertas de material agotado o stock bajo. Query: sucursal_id (opcional), umbral (default 0 = agotado).
     */
    public function alertas(Request $request): JsonResponse
    {
        $umbral = (float) ($request->query('umbral', 0));
        $productos = Producto::where('activo', true)
            ->whereRaw('stock_actual <= ?', [$umbral])
            ->orderBy('stock_actual')
            ->orderBy('nombre')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                    'stock_actual' => (float) $p->stock_actual,
                    'unidad_medida' => $p->unidad_medida,
                ];
            });

        return response()->json([
            'data' => $productos,
            'meta' => ['umbral' => $umbral, 'total' => $productos->count()],
        ], 200);
    }

    /**
     * List inventory movements. Optional filter by product.
     */
    public function movimientos(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user || (! $user->can('inventario.consultar') && ! $user->can('inventario.ajustar'))) {
            return response()->json([
                'errors' => ['permiso' => ['No tiene permiso para consultar movimientos de inventario.']],
            ], 403);
        }

        $query = InventarioMovimiento::with('producto')
            ->orderByDesc('created_at');

        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->query('producto_id'));
        }

        $movimientos = $query->limit(50)->get()->map(function (InventarioMovimiento $m) {
            return [
                'id' => $m->id,
                'producto_id' => $m->producto_id,
                'producto_nombre' => $m->producto?->nombre,
                'tipo' => $m->tipo,
                'cantidad' => (float) $m->cantidad,
                'stock_anterior' => (float) ($m->stock_anterior ?? 0),
                'stock_nuevo' => (float) ($m->stock_nuevo ?? 0),
                'motivo' => $m->motivo,
                'usuario_id' => $m->usuario_id,
                'created_at' => $m->created_at,
            ];
        });

        return response()->json([
            'data' => $movimientos,
        ], 200);
    }
}
