<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (! $request->user()->can('clientes.ver') && ! $request->user()->can('ventas.crear') && ! $request->user()->can('ventas.ver')) {
            abort(403, 'No tiene permiso para ver clientes.');
        }

        $query = Cliente::query();
        if ($request->has('activo') && $request->activo !== '') {
            $query->where('activo', (bool) $request->activo);
        } else {
            $query->where('activo', true);
        }
        $clientes = $query->orderBy('es_mostrador', 'desc')->orderBy('nombre')->get();

        return response()->json(['data' => $clientes], 200);
    }

    public function show(Request $request, Cliente $cliente): JsonResponse
    {
        if (! $request->user()->can('clientes.ver')) {
            abort(403, 'No tiene permiso para ver clientes.');
        }

        return response()->json(['data' => $cliente], 200);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->user()->can('clientes.administrar')) {
            abort(403, 'No tiene permiso para administrar clientes.');
        }

        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
        ], ['nombre.required' => 'El nombre del cliente es obligatorio.']);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cliente = Cliente::create([
            'nombre' => $request->nombre,
            'es_mostrador' => false,
            'activo' => true,
        ]);
        return response()->json(['data' => $cliente, 'message' => 'Cliente creado.'], 201);
    }

    public function update(Request $request, Cliente $cliente): JsonResponse
    {
        if ($cliente->es_mostrador) {
            return response()->json([
                'errors' => ['cliente' => ['No se puede editar el cliente mostrador.']],
            ], 422);
        }
        $validator = Validator::make($request->all(), [
            'nombre' => ['sometimes', 'string', 'max:255'],
            'activo' => ['sometimes', 'boolean'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $cliente->update($request->only(['nombre', 'activo']));
        return response()->json(['data' => $cliente, 'message' => 'Cliente actualizado.'], 200);
    }

    public function destroy(Request $request, Cliente $cliente): JsonResponse
    {
        if (! $request->user()->can('clientes.administrar')) {
            abort(403, 'No tiene permiso para administrar clientes.');
        }

        if ($cliente->es_mostrador) {
            return response()->json([
                'errors' => ['cliente' => ['No se puede eliminar el cliente mostrador.']],
            ], 422);
        }
        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado.'], 200);
    }
}
