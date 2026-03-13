<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (! $request->user()->can('productos.ver') && ! $request->user()->can('ventas.crear') && ! $request->user()->can('ventas.ver')) {
            abort(403, 'No tiene permiso para ver productos.');
        }

        $query = Producto::with('unidadMedida');

        if ($request->boolean('todos')) {
            // Sin filtrar por activo
        } elseif ($request->has('activo') && $request->activo !== '' && $request->activo !== null) {
            $query->where('activo', (bool) $request->activo);
        } else {
            $query->where('activo', true);
        }

        $productos = $query->orderBy('nombre')->get();

        return response()->json(['data' => $productos], 200);
    }

    public function show(Request $request, Producto $producto): JsonResponse
    {
        if (! $request->user()->can('productos.ver')) {
            abort(403, 'No tiene permiso para ver productos.');
        }

        $producto->load('unidadMedida');

        return response()->json(['data' => $producto], 200);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->user()->can('productos.administrar')) {
            abort(403, 'No tiene permiso para administrar productos.');
        }

        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'precio_unitario' => ['required', 'numeric', 'min:0'],
            'stock_actual' => ['required', 'numeric', 'min:0'],
            'unidad_medida_id' => ['nullable', 'exists:unidades_medida,id'],
            'activo' => ['sometimes', 'boolean'],
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $producto = Producto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio_unitario' => $request->precio_unitario,
            'stock_actual' => $request->stock_actual,
            'unidad_medida_id' => $request->unidad_medida_id,
            'unidad_medida' => $request->unidad_medida ?? 'm3',
            'activo' => $request->boolean('activo', true),
        ]);

        $producto->load('unidadMedida');

        return response()->json(['data' => $producto, 'message' => 'Producto creado.'], 201);
    }

    public function update(Request $request, Producto $producto): JsonResponse
    {
        if (! $request->user()->can('productos.administrar')) {
            abort(403, 'No tiene permiso para administrar productos.');
        }

        // Solo quien tiene precios.modificar puede cambiar el precio (ej. Gerente de Producción no puede)
        if ($request->has('precio_unitario') && (float) $request->precio_unitario !== (float) $producto->precio_unitario) {
            if (! $request->user()->can('precios.modificar')) {
                return response()->json([
                    'errors' => ['precio_unitario' => ['No tiene permiso para modificar precios. Solo un administrador puede cambiar el precio.']],
                ], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'nombre' => ['sometimes', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'precio_unitario' => ['sometimes', 'numeric', 'min:0'],
            'stock_actual' => ['sometimes', 'numeric', 'min:0'],
            'unidad_medida_id' => ['nullable', 'exists:unidades_medida,id'],
            'activo' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $producto->update($request->only([
            'nombre', 'descripcion', 'precio_unitario', 'stock_actual',
            'unidad_medida_id', 'activo',
        ]));

        if ($request->has('unidad_medida')) {
            $producto->unidad_medida = $request->unidad_medida;
            $producto->save();
        }

        $producto->load('unidadMedida');

        return response()->json(['data' => $producto, 'message' => 'Producto actualizado.'], 200);
    }

    public function destroy(Request $request, Producto $producto): JsonResponse
    {
        if (! $request->user()->can('productos.administrar')) {
            abort(403, 'No tiene permiso para administrar productos.');
        }

        $producto->update(['activo' => false]);

        return response()->json(['message' => 'Producto desactivado.'], 200);
    }
}
