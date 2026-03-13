<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Entrega;
use App\Models\ConfiguracionEmpresa;
use App\Models\Producto;
use App\Models\Sucursal;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\VigilanteQr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EntregaController extends Controller
{
    public function registrar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'venta_id' => ['required', 'exists:ventas,id'],
            'venta_detalle_id' => ['required', 'exists:venta_detalles,id'],
            'cantidad_despachada' => ['required', 'numeric', 'min:0.01'],
            'foto' => ['nullable', 'file', 'image', 'max:10240'],
            'foto_ruta' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $venta = Venta::with(['sucursal', 'detalles'])->findOrFail($request->venta_id);
        $detalle = VentaDetalle::where('id', $request->venta_detalle_id)->where('venta_id', $venta->id)->firstOrFail();

        $restante = (float) $detalle->cantidad_pedida - (float) $detalle->cantidad_entregada;
        $cantidad = (float) $request->cantidad_despachada;

        if ($cantidad > $restante) {
            return response()->json([
                'errors' => ['cantidad_despachada' => ['La cantidad no puede exceder el restante (' . $restante . ').']],
            ], 422);
        }

        // Foto opcional en Macuspana/Entregas pero se registra por cada entrega parcial si se envía
        $fotoRuta = $request->foto_ruta;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fotoRuta = 'entregas/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('', $fotoRuta, 'public');
        }

        $entrega = Entrega::create([
            'venta_id' => $venta->id,
            'venta_detalle_id' => $detalle->id,
            'usuario_id' => auth()->id(),
            'cantidad_despachada' => $cantidad,
            'foto_ruta' => $fotoRuta,
        ]);

        $entrega->load(['venta', 'ventaDetalle', 'usuario']);

        return response()->json(['data' => $entrega, 'message' => 'Entrega registrada correctamente.'], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Entrega::with(['venta', 'ventaDetalle.producto', 'usuario']);
        if ($request->filled('venta_id')) {
            $query->where('venta_id', $request->venta_id);
        }
        $entregas = $query->orderByDesc('created_at')->paginate(20);
        return response()->json(['data' => $entregas], 200);
    }

    /**
     * Validación e importación automática de acceso en caseta (Vigilante - Macuspana).
     *
     * - Recibe la cadena plana del QR (uuid|idSucursal|idProd|cant|idUnidad|...) y una imagen.
     * - Si el QR es de otra sucursal (Villahermosa), marca el acceso como importado (en base al idSucursal del QR).
     * - Usa la tabla de entregas local para contar los viajes realizados por UUID.
     * - Controla que la cantidad de este viaje no exceda el remanente del pedido original (cant del QR).
     * - Bloquea si existe una venta asociada al UUID en estatus 'entregado' o 'cancelado'.
     */
    public function validarYRegistrarAcceso(Request $request): JsonResponse
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursalLocal = $config?->sucursal;
        if (! $sucursalLocal || ! $sucursalLocal->isMacuspana()) {
            return response()->json([
                'errors' => [
                    'sucursal' => ['El control de acceso de Vigilante solo está disponible en la sucursal Macuspana.'],
                ],
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'qr_payload' => ['required', 'string'],
            'cantidad_viaje' => ['nullable', 'numeric', 'min:0.01'],
            'foto' => ['required', 'file', 'image', 'max:10240'],
        ], [
            'qr_payload.required' => 'La cadena del QR es obligatoria.',
            'foto.required' => 'La foto del vehículo/material es obligatoria.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rawPayload = trim($request->input('qr_payload'));
        $partes = array_values(array_filter(explode('|', $rawPayload), function ($v) {
            return $v !== '';
        }));

        if (count($partes) < 3) {
            return response()->json([
                'errors' => [
                    'qr_payload' => ['Payload del QR inválido. Formato esperado: uuid|idSucursal|idProd|cant|idUnidad|...'],
                ],
            ], 422);
        }

        // Soportar uuid:sucursal|... o uuid|sucursal|...
        $uuid = null;
        $sucursalOrigenId = null;
        $offset = 0;
        $cabecera = $partes[0];
        if (str_contains($cabecera, ':')) {
            [$uuid, $sucursalIdStr] = explode(':', $cabecera, 2);
            $sucursalOrigenId = is_numeric($sucursalIdStr) ? (int) $sucursalIdStr : null;
            $offset = 1;
        } else {
            $uuid = $cabecera;
            $sucursalOrigenId = isset($partes[1]) && is_numeric($partes[1]) ? (int) $partes[1] : null;
            $offset = 2;
        }

        if (! $uuid) {
            return response()->json([
                'errors' => [
                    'qr_payload' => ['No se pudo obtener el UUID del QR.'],
                ],
            ], 422);
        }

        if ($offset + 1 >= count($partes)) {
            return response()->json([
                'errors' => [
                    'qr_payload' => ['Payload del QR incompleto. Debe incluir idProd y cant.'],
                ],
            ], 422);
        }

        $productoId = (int) $partes[$offset];
        $cantidadTotal = (float) $partes[$offset + 1];
        $unidadId = $offset + 2 < count($partes) ? (int) $partes[$offset + 2] : null;

        if ($cantidadTotal <= 0) {
            return response()->json([
                'errors' => [
                    'qr_payload' => ['La cantidad total del QR debe ser mayor a cero.'],
                ],
            ], 422);
        }

        $producto = Producto::findOrFail($productoId);
        if (! $producto->activo) {
            return response()->json([
                'errors' => [
                    'producto_id' => ['El producto del QR no está activo en el catálogo.'],
                ],
            ], 422);
        }

        $esImportado = $sucursalOrigenId !== null && $sucursalOrigenId !== (int) $sucursalLocal->id;

        // Verificar si el uuid ya existe localmente (venta importada o local)
        $ventaAsociada = Venta::where('uuid', $uuid)
            ->orWhere('ticket_origen_uuid', $uuid)
            ->first();

        // Importación silenciosa: si el QR es externo (Villahermosa) y no existe localmente, crear la venta para seguimiento en Macuspana.
        if ($esImportado && ! $ventaAsociada) {
            $lineas = [];
            for ($i = $offset; $i + 2 < count($partes); $i += 3) {
                $lineas[] = [
                    'producto_id' => (int) $partes[$i],
                    'cantidad' => (float) $partes[$i + 1],
                    'id_unidad' => (int) $partes[$i + 2],
                ];
            }
            if (! empty($lineas)) {
                try {
                    $ventaAsociada = $this->importarPedidoSilencio($uuid, $lineas, $sucursalLocal);
                } catch (\RuntimeException $e) {
                    return response()->json([
                        'errors' => ['qr_payload' => [$e->getMessage()]],
                    ], 422);
                } catch (\Throwable $e) {
                    return response()->json([
                        'errors' => ['qr_payload' => ['Error al importar el pedido: ' . $e->getMessage()]],
                    ], 422);
                }
            }
        }

        // Bloquear si hay una venta asociada al UUID en estatus entregado o cancelado
        if ($ventaAsociada && in_array($ventaAsociada->estatus, ['entregado', 'cancelado'], true)) {
            return response()->json([
                'errors' => [
                    'venta' => ['No se pueden registrar más viajes porque el pedido está en estatus ' . $ventaAsociada->estatus . '.'],
                ],
            ], 422);
        }
        // Bloquear salidas si la venta sigue pendiente de cobro en oficina (solo aplica a pedidos locales pendiente_pago).
        if ($ventaAsociada && $ventaAsociada->estatus === 'pendiente_pago') {
            return response()->json([
                'errors' => [
                    'venta' => ['No se puede registrar la salida porque el pedido sigue pendiente de cobro en oficina.'],
                ],
            ], 422);
        }

        // Viajes a nivel pedido (venta) para QRs locales con venta_id
        $vigilanteQr = VigilanteQr::where('uuid', $uuid)->first();
        $ventaPedido = $vigilanteQr?->venta_id ? $vigilanteQr->venta()->lockForUpdate()->first() : null;

        if ($ventaPedido) {
            $viajesTotales = (int) $ventaPedido->viajes_permitidos;
            $viajesPrevios = (int) $ventaPedido->viajes_usados;
            if ($viajesPrevios >= $viajesTotales) {
                return response()->json([
                    'errors' => [
                        'qr_payload' => ['El pedido ya no tiene viajes disponibles (' . $viajesPrevios . '/' . $viajesTotales . ').'],
                    ],
                ], 422);
            }
            $numeroViaje = $viajesPrevios + 1;
            $cantidadViaje = (float) ($request->input('cantidad_viaje') ?: $cantidadTotal);
            if ($cantidadViaje <= 0) {
                $cantidadViaje = 1.0;
            }
            $totalEntregado = 0;
            $remanente = $cantidadTotal;
        } else {
            // Flujo sin venta: viajes por cantidad (QR importado o legacy)
            $totalEntregado = Entrega::where('uuid_qr', $uuid)->sum('cantidad_despachada');
            $remanente = max(0.0, $cantidadTotal - $totalEntregado);

            $cantidadViaje = $request->input('cantidad_viaje');
            if ($cantidadViaje === null) {
                $cantidadViaje = $cantidadTotal;
            }
            $cantidadViaje = (float) $cantidadViaje;

            if ($cantidadViaje <= 0) {
                return response()->json([
                    'errors' => [
                        'cantidad_viaje' => ['La cantidad del viaje debe ser mayor a cero.'],
                    ],
                ], 422);
            }

            if ($cantidadViaje > $remanente) {
                return response()->json([
                    'errors' => [
                        'cantidad_viaje' => ['La cantidad de este viaje excede el remanente disponible (' . $remanente . ').'],
                    ],
                ], 422);
            }

            $viajesPrevios = Entrega::where('uuid_qr', $uuid)->count();
            $numeroViaje = $viajesPrevios + 1;
            $viajesTotales = (int) ceil($cantidadTotal / $cantidadViaje);
        }

        // Guardar la foto en storage usando uuid y timestamp
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'vigilante/' . $uuid . '_' . time() . '.' . $file->getClientOriginalExtension();
            $fotoPath = $file->storeAs('public', $filename);
        }

        $ventaIdParaEntrega = $ventaPedido?->id ?? $ventaAsociada?->id;

        $entrega = Entrega::create([
            'venta_id' => $ventaIdParaEntrega,
            'venta_detalle_id' => null,
            'usuario_id' => auth()->id(),
            'cantidad_despachada' => $cantidadViaje,
            'foto_ruta' => null,
            'foto_path' => $fotoPath,
            'uuid_qr' => $uuid,
            'numero_viaje' => $numeroViaje,
        ]);

        if ($ventaPedido) {
            $ventaPedido->viajes_usados = $numeroViaje;
            $ventaPedido->saveQuietly();
        }

        return response()->json([
            'data' => [
                'entrega' => $entrega,
                'uuid' => $uuid,
                'producto_id' => $productoId,
                'cantidad_viaje' => $cantidadViaje,
                'cantidad_total' => $cantidadTotal,
                'total_entregado' => $totalEntregado + $cantidadViaje,
                'remanente' => max(0.0, $cantidadTotal - ($totalEntregado + $cantidadViaje)),
                'viaje_actual' => $numeroViaje,
                'viajes_totales' => $viajesTotales,
                'es_importado' => $esImportado,
            ],
            'message' => $esImportado
                ? 'Pedido importado exitosamente. Viaje ' . $numeroViaje . ' de ' . $viajesTotales . ' registrado.'
                : 'Viaje ' . $numeroViaje . ' de ' . $viajesTotales . ' registrado.',
        ], 201);
    }

    /**
     * Importación silenciosa: crea la venta local desde el payload del QR externo para iniciar seguimiento en Macuspana.
     * Descuenta inventario (obligatorio en sucursal venta_almacen).
     */
    private function importarPedidoSilencio(string $uuidOrigen, array $lineas, Sucursal $sucursal): Venta
    {
        $idsProducto = array_unique(array_column($lineas, 'producto_id'));
        $productos = Producto::whereIn('id', $idsProducto)->where('activo', true)->get()->keyBy('id');

        DB::beginTransaction();
        try {
            $clienteId = Cliente::clienteMostrador()?->id;
            $folio = $this->generarFolioUnico();
            $venta = Venta::create([
                'folio' => $folio,
                'sucursal_id' => $sucursal->id,
                'usuario_id' => auth()->id(),
                'cliente_id' => $clienteId,
                'total' => 0,
                'estatus' => 'pendiente',
                'tipo' => 'venta',
                'ticket_origen_uuid' => $uuidOrigen,
            ]);

            $total = 0;
            foreach ($lineas as $linea) {
                $producto = $productos->get($linea['producto_id']);
                if (! $producto) {
                    throw new \RuntimeException('Producto id ' . $linea['producto_id'] . ' no existe o no está activo.');
                }
                $cantidad = $linea['cantidad'];
                $stockAnterior = (float) $producto->stock_actual;
                $producto->reducirStock($cantidad);
                // Registrar movimiento de inventario por importación silenciosa de pedido externo.
                \App\Models\InventarioMovimiento::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'venta',
                    'cantidad' => $cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => (float) $producto->stock_actual,
                    'motivo' => 'Salida por importación silenciosa de pedido (QR externo)',
                    'usuario_id' => auth()->id(),
                ]);

                $subtotal = $producto->precio_unitario * $cantidad;
                $total += $subtotal;

                $venta->detalles()->create([
                    'producto_id' => $producto->id,
                    'cantidad_pedida' => $cantidad,
                    'cantidad_entregada' => 0,
                ]);
            }

            $venta->update(['total' => $total]);
            $venta->load('detalles.producto');
            $venta->generarQrPayload();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $venta;
    }

    private function generarFolioUnico(): string
    {
        do {
            $folio = 'IMP-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Venta::where('folio', $folio)->exists());

        return $folio;
    }
}
