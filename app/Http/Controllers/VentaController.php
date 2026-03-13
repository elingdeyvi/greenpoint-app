<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\ConfiguracionEmpresa;
use App\Models\InventarioMovimiento;
use App\Models\Producto;
use App\Services\CasetaService;
use App\Models\Sucursal;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\VigilanteQr;
use App\Http\Requests\StoreVentaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Controlador principal del Sistema de Ventas e Inventario (2 sucursales).
 *
 * Villahermosa: genera pedidos con ticket y QR (formato uuid|id|cant|unid). No se toma foto al vender.
 * Macuspana: escanea el QR de Villahermosa para importar pedido (importarPedido) y genera nuevo ticket de salida.
 *
 * El controlador BoletoController se mantiene para el flujo legacy de Control de Salidas (boletos de camión/volteo).
 */
class VentaController extends Controller
{
    /**
     * Listado de ventas con filtros.
     * Seguridad: solo se devuelven ventas de la sucursal activa (ConfiguracionEmpresa).
     */
    public function index(Request $request): JsonResponse
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursalActivaId = $config?->sucursal_id;

        $query = Venta::with(['sucursal', 'usuario', 'cliente', 'detalles.producto', 'pagos']);

        if ($sucursalActivaId !== null) {
            $query->where('sucursal_id', $sucursalActivaId);
        } elseif ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }
        if ($request->filled('estatus')) {
            $estatus = $request->estatus;
            $query->whereIn('estatus', is_array($estatus) ? $estatus : array_map('trim', explode(',', $estatus)));
        }
        if ($request->filled('folio')) {
            $query->where('folio', 'like', '%' . $request->folio . '%');
        }

        $ventas = $query->orderByDesc('created_at')->paginate(20);

        return response()->json(['data' => $ventas], 200);
    }

    /**
     * Ver una venta. Solo se permite consultar ventas de la sucursal activa.
     */
    public function show(Venta $venta): JsonResponse
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursalActivaId = $config?->sucursal_id;
        if ($sucursalActivaId !== null && (int) $venta->sucursal_id !== (int) $sucursalActivaId) {
            return response()->json(['errors' => ['venta' => ['No tiene permiso para consultar esta venta.']]], 403);
        }
        $venta->load(['sucursal', 'usuario', 'cliente', 'detalles.producto', 'pagos']);
        return response()->json(['data' => $venta], 200);
    }

    /**
     * (Oficina central - Macuspana) Listar pedidos generados en caseta pendientes de pago.
     */
    public function pedidosPendientesPago(Request $request): JsonResponse
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursal = $config?->sucursal;

        if (! $sucursal || ! $sucursal->isMacuspana()) {
            return response()->json([
                'errors' => [
                    'sucursal' => ['La validación de pedidos solo aplica para sucursales tipo venta_almacen (ej. Macuspana).'],
                ],
            ], 422);
        }

        $query = Venta::with(['sucursal', 'usuario', 'detalles.producto', 'pagos'])
            ->where('sucursal_id', $sucursal->id)
            ->where('estatus', 'pendiente_pago');

        if ($request->filled('folio')) {
            $query->where('folio', 'like', '%' . $request->folio . '%');
        }

        $ventas = $query->orderByDesc('created_at')->paginate(20);

        return response()->json(['data' => $ventas], 200);
    }

    /**
     * Crear venta. Ambas sucursales pueden vender.
     * Reglas §2.2: Sucursal venta (Villahermosa) no descuenta stock; venta_almacen (Macuspana) siempre descuenta, incluso donativos.
     * Donativos (§2.3): observaciones obligatorias; pagos no exigidos. Validado en StoreVentaRequest.
     * Respuesta JSON compatible con ApiService: { data, message } en éxito; { errors } en 422.
     */
    public function store(StoreVentaRequest $request): JsonResponse
    {
        $sucursal = Sucursal::findOrFail($request->validated('sucursal_id'));
        $detallesInput = $request->validated('detalles');
        $pagosInput = is_array($request->input('pagos')) ? $request->input('pagos') : [];
        $esDonativo = $request->isDonativo();

        try {
            DB::beginTransaction();

            $folio = $this->generarFolioUnico();
            $total = 0;

            $clienteId = $request->filled('cliente_id') ? $request->cliente_id : Cliente::clienteMostrador()?->id;
            $venta = Venta::create([
                'folio' => $folio,
                'sucursal_id' => $sucursal->id,
                'usuario_id' => auth()->id(),
                'cliente_id' => $clienteId,
                'total' => 0,
                'estatus' => 'pendiente',
                'tipo' => $esDonativo ? 'donativo' : ($request->input('tipo') ?? 'venta'),
                'es_donativo' => $esDonativo,
                'observaciones' => $request->input('observaciones'),
            ]);

            // §2.2: Sucursal venta (Villahermosa) NO llama a reducirStock. venta_almacen (Macuspana) siempre reduce, incluso donativos.
            $descontarStock = $sucursal->isSucursalVentaAlmacen();

            foreach ($detallesInput as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                $cantidad = (float) $item['cantidad_pedida'];
                if ($descontarStock) {
                    $stockAnterior = (float) $producto->stock_actual;
                    $producto->reducirStock($cantidad);
                    // Registrar movimiento de inventario por venta/donativo en sucursal venta_almacen.
                    InventarioMovimiento::create([
                        'producto_id' => $producto->id,
                        'tipo' => $esDonativo ? 'donativo' : 'venta',
                        'cantidad' => $cantidad,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => (float) $producto->stock_actual,
                        'motivo' => $esDonativo ? 'Salida por donativo' : 'Salida por venta',
                        'usuario_id' => auth()->id(),
                    ]);
                }

                $subtotal = $producto->precio_unitario * $cantidad;
                $total += $subtotal;

                $venta->detalles()->create([
                    'producto_id' => $producto->id,
                    'cantidad_pedida' => $cantidad,
                    'cantidad_entregada' => 0,
                ]);
            }

            $venta->update(['total' => $total]);

            // §2.3 + pagos con cambio: si tipo !== donativo, suma de pagos >= total; monto neto registrado = exactamente total.
            if (! empty($pagosInput) && ! $esDonativo) {
                $sumaPagos = 0.0;
                foreach ($pagosInput as $idx => $pago) {
                    $metodo = $pago['metodo_pago'] ?? null;
                    $monto = isset($pago['monto']) ? (float) $pago['monto'] : 0.0;
                    if (! $metodo || $monto <= 0) {
                        DB::rollBack();
                        return response()->json([
                            'errors' => [
                                "pagos.$idx" => ['Cada pago debe tener un método y un monto mayor a cero.'],
                            ],
                        ], 422);
                    }
                    $sumaPagos += $monto;
                }

                if (round($sumaPagos, 2) < round($total, 2)) {
                    DB::rollBack();
                    return response()->json([
                        'errors' => [
                            'pagos' => ['La suma de los pagos (' . number_format($sumaPagos, 2) . ') debe ser mayor o igual al total de la venta (' . number_format($total, 2) . ').'],
                        ],
                    ], 422);
                }
            }

            $caja_abierta = Caja::where('sucursal_id', $sucursalId)->where('estatus', 'abierta')->first();
            $pagosParaGuardar = $this->normalizarPagosParaTotal($pagosInput, $total);
            foreach ($pagosParaGuardar as $pago) {
                $venta->pagos()->create([
                    'caja_id' => $caja_abierta?->id,
                    'metodo_pago' => $pago['metodo_pago'],
                    'monto' => (float) $pago['monto'],
                    'monto_recibido' => isset($pago['monto_recibido']) ? (float) $pago['monto_recibido'] : null,
                    'referencia_pago' => $pago['referencia_pago'] ?? null,
                ]);
            }

            $venta->load(['detalles.producto', 'pagos']);
            $venta->generarQrPayload();

            DB::commit();
        } catch (\RuntimeException $e) {
            DB::rollBack();
            return response()->json(['errors' => ['stock' => [$e->getMessage()]]], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $venta->load(['sucursal', 'usuario', 'detalles.producto', 'pagos']);

        return response()->json([
            'data' => $venta,
            'message' => 'Venta creada con ticket y QR generado.',
        ], 201);
    }

    /**
     * (Para Macuspana) Recibir escaneo del QR de Villahermosa, parsear payload, crear venta local vinculada y generar QR de salida Macuspana.
     */
    public function importarPedido(Request $request): JsonResponse
    {
        $sucursalId = $request->sucursal_id;
        if ($sucursalId === null || $sucursalId === '') {
            $config = ConfiguracionEmpresa::obtenerConfiguracion();
            $sucursalId = $config?->sucursal_id;
        }

        $validator = Validator::make(array_merge($request->all(), ['sucursal_id' => $sucursalId]), [
            'qr_payload' => ['required', 'string'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
        ], [
            'qr_payload.required' => 'El payload del QR escaneado es obligatorio.',
            'sucursal_id.required' => 'La sucursal de destino es obligatoria. Configure la sucursal de almacén en Configuración de la empresa.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sucursal = Sucursal::findOrFail($sucursalId);
        if (! $sucursal->isMacuspana()) {
            return response()->json([
                'errors' => ['sucursal' => ['La importación de pedido solo aplica para la sucursal Macuspana.']],
            ], 422);
        }

        $partes = array_filter(explode('|', trim($request->qr_payload)));
        if (count($partes) < 2) {
            return response()->json([
                'errors' => ['qr_payload' => ['Payload del QR inválido. Formato: uuid:idSucursal|idProd|cant|idUnidad|...']],
            ], 422);
        }

        $cabecera = $partes[0];
        $uuidOrigen = $cabecera;
        if (str_contains($cabecera, ':')) {
            [$uuidOrigen] = explode(':', $cabecera, 2);
        }
        $lineas = [];
        $desde = 1;
        for ($i = $desde; $i + 2 < count($partes); $i += 3) {
            $lineas[] = [
                'producto_id_villa' => (int) $partes[$i],
                'cantidad' => (float) $partes[$i + 1],
                'id_unidad' => (int) $partes[$i + 2],
            ];
        }

        if (empty($lineas)) {
            return response()->json(['errors' => ['qr_payload' => ['No se encontraron líneas válidas en el payload.']]], 422);
        }

        $idsProducto = array_unique(array_column($lineas, 'producto_id_villa'));
        $productos = Producto::whereIn('id', $idsProducto)->where('activo', true)->get()->keyBy('id');

        try {
            DB::beginTransaction();

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
                $producto = $productos->get($linea['producto_id_villa']);
                if (! $producto) {
                    DB::rollBack();
                    return response()->json([
                        'errors' => ['qr_payload' => ['Producto id '.$linea['producto_id_villa'].' no existe o no está activo.']],
                    ], 422);
                }
                $cantidad = $linea['cantidad'];
                $stockAnterior = (float) $producto->stock_actual;
                $producto->reducirStock($cantidad);
                // Registrar movimiento de inventario por importación de pedido desde QR externo.
                InventarioMovimiento::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'venta',
                    'cantidad' => $cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => (float) $producto->stock_actual,
                    'motivo' => 'Salida por importar pedido (QR externo)',
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
        } catch (\RuntimeException $e) {
            DB::rollBack();
            return response()->json(['errors' => ['stock' => [$e->getMessage()]]], 422);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $venta->load(['sucursal', 'usuario', 'detalles.producto']);

        return response()->json([
            'data' => $venta,
            'message' => 'Pedido importado y QR de salida Macuspana generado.',
        ], 201);
    }

    /**
     * (Oficina central - Macuspana) Registrar pagos de una venta pendiente_pago y, si se cubre el total, marcar como pagado
     * y generar el ticket definitivo con QR activo.
     */
    public function registrarPagos(Request $request, Venta $venta): JsonResponse
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursal = $config?->sucursal;

        if (! $sucursal || ! $sucursal->isMacuspana()) {
            return response()->json([
                'errors' => [
                    'sucursal' => ['El registro de pagos en este flujo solo aplica para sucursales tipo venta_almacen (ej. Macuspana).'],
                ],
            ], 422);
        }

        if ($venta->sucursal_id !== $sucursal->id) {
            return response()->json([
                'errors' => [
                    'venta' => ['La venta no pertenece a la sucursal actual.'],
                ],
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'es_donativo' => ['nullable', 'boolean'],
            'observaciones' => ['nullable', 'string'],
            'pagos' => ['nullable', 'array'],
            'pagos.*.metodo_pago' => ['required_with:pagos', 'string', 'max:30'],
            'pagos.*.monto' => ['required_with:pagos', 'numeric', 'min:0.01'],
            'pagos.*.monto_recibido' => ['nullable', 'numeric', 'min:0'],
            'pagos.*.referencia_pago' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $pagosInput = $request->input('pagos', []);
        $esDonativo = $request->boolean('es_donativo') || $venta->tipo === 'donativo' || $venta->es_donativo;

        if ($esDonativo && ! $request->filled('observaciones') && ! $venta->observaciones) {
            return response()->json([
                'errors' => [
                    'observaciones' => ['Las observaciones son obligatorias para donativos.'],
                ],
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Asegurar que el total esté calculado (por si el pedido viene de caseta sin total explícito)
            $venta->loadMissing('detalles.producto');
            if ((float) $venta->total <= 0 && $venta->detalles->isNotEmpty()) {
                $total = 0.0;
                foreach ($venta->detalles as $detalle) {
                    $precio = (float) ($detalle->producto->precio_unitario ?? 0);
                    $total += $precio * (float) $detalle->cantidad_pedida;
                }
                $venta->total = $total;
                $venta->saveQuietly();
            }

            $totalVenta = (float) $venta->total;

            if (! $esDonativo) {
                $pagosInput = is_array($pagosInput) ? $pagosInput : [];
                if (empty($pagosInput)) {
                    DB::rollBack();
                    return response()->json([
                        'errors' => [
                            'pagos' => ['Debe registrar al menos un pago para una venta no donativo.'],
                        ],
                    ], 422);
                }

                $sumaPagos = 0.0;
                foreach ($pagosInput as $pago) {
                    $sumaPagos += (float) $pago['monto'];
                }

                if (round($sumaPagos, 2) < round($totalVenta, 2)) {
                    DB::rollBack();
                    return response()->json([
                        'errors' => [
                            'pagos' => ['La suma de los pagos (' . number_format($sumaPagos, 2) . ') debe ser mayor o igual al total de la venta (' . number_format($totalVenta, 2) . ').'],
                        ],
                    ], 422);
                }
            }

            $caja_abierta = Caja::where('sucursal_id', $sucursal->id)->where('estatus', 'abierta')->first();
            // Reemplazar pagos existentes; monto neto registrado = exactamente total (pagos con cambio).
            $venta->pagos()->delete();
            if (! $esDonativo) {
                $pagosParaGuardar = $this->normalizarPagosParaTotal($pagosInput, $totalVenta);
                foreach ($pagosParaGuardar as $pago) {
                    $venta->pagos()->create([
                        'caja_id' => $caja_abierta?->id,
                        'metodo_pago' => $pago['metodo_pago'],
                        'monto' => (float) $pago['monto'],
                        'monto_recibido' => isset($pago['monto_recibido']) ? (float) $pago['monto_recibido'] : null,
                        'referencia_pago' => $pago['referencia_pago'] ?? null,
                    ]);
                }
            }

            // Marcar tipo y observaciones si es donativo
            if ($esDonativo) {
                $venta->tipo = 'donativo';
                $venta->es_donativo = true;
                if ($request->filled('observaciones')) {
                    $venta->observaciones = $request->observaciones;
                }
            }

            // Marcar como pagado y generar QR si aún no existe
            $venta->estatus = 'pagado';
            if (! $venta->qr_payload) {
                $venta->generarQrPayload();
            } else {
                $venta->saveQuietly();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $venta->load(['sucursal', 'usuario', 'detalles.producto', 'pagos']);

        return response()->json([
            'data' => $venta,
            'message' => 'Pagos registrados y ticket de salida generado.',
        ], 200);
    }

    /**
     * Cancelar venta (todas las sucursales). Requiere permiso ventas.cancelar.
     * Solo se puede cancelar si estatus es pendiente o parcial.
     * Solo en Macuspana (venta_almacen) se repone stock; en Villahermosa (venta) solo se marca cancelada.
     */
    public function cancelar(Request $request, Venta $venta): JsonResponse
    {
        if (! auth()->user()->can('ventas.cancelar')) {
            return response()->json([
                'errors' => ['permiso' => ['No tiene permiso para cancelar ventas.']],
            ], 403);
        }

        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursalActivaId = $config?->sucursal_id;
        if ($sucursalActivaId !== null && (int) $venta->sucursal_id !== (int) $sucursalActivaId) {
            return response()->json([
                'errors' => ['venta' => ['No puede cancelar ventas de otra sucursal.']],
            ], 403);
        }

        if (! in_array($venta->estatus, ['pendiente', 'parcial'], true)) {
            return response()->json([
                'errors' => ['estatus' => ['Solo se pueden cancelar ventas en estatus pendiente o parcial.']],
            ], 422);
        }

        try {
            DB::beginTransaction();

            $venta->load('detalles.producto');
            $sucursal = $venta->sucursal;
            // Solo en venta_almacen (Macuspana) se repone stock; en venta (Villahermosa) no hay inventario local.
            if ($sucursal && $sucursal->isSucursalVentaAlmacen()) {
                foreach ($venta->detalles as $detalle) {
                    $noEntregado = (float) $detalle->cantidad_pedida - (float) $detalle->cantidad_entregada;
                    if ($noEntregado > 0) {
                        $producto = $detalle->producto;
                        if ($producto) {
                            $stockAnterior = (float) $producto->stock_actual;
                            $producto->increment('stock_actual', $noEntregado);
                            $producto->refresh();
                            // Registrar movimiento de inventario por cancelación de venta en Macuspana.
                            InventarioMovimiento::create([
                                'producto_id' => $producto->id,
                                'tipo' => 'cancelacion',
                                'cantidad' => $noEntregado,
                                'stock_anterior' => $stockAnterior,
                                'stock_nuevo' => (float) $producto->stock_actual,
                                'motivo' => 'Devolución de material por cancelación de venta',
                                'usuario_id' => auth()->id(),
                            ]);
                        }
                    }
                }
            }

            $venta->estatus = 'cancelado';
            $venta->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $venta->load(['sucursal', 'usuario', 'detalles.producto']);

        return response()->json([
            'data' => $venta,
            'message' => 'Venta cancelada. Se devolvió al inventario el material no entregado.',
        ], 200);
    }

    /**
     * Devuelve el contenido del ticket para impresión (texto plano, impresora térmica).
     * Incluye: nombre empresa, folio, fecha/hora, sucursal (unidad), líneas (producto, cantidad, unidad) y total.
     * El front puede enviar este contenido a POST /api/impresion/imprimir-raw con impresora_id.
     */
    public function ticketContenido(Venta $venta): JsonResponse
    {
        $venta->load(['sucursal', 'usuario', 'detalles.producto.unidadMedida']);
        $empresa = ConfiguracionEmpresa::obtenerConfiguracion();
        $nombreEmpresa = $empresa?->nombre_empresa ?? config('app.name');
        $ancho = 32;
        $linea = str_repeat('-', $ancho);
        $centrar = function (string $s) use ($ancho) {
            return str_pad($s, $ancho, ' ', STR_PAD_BOTH);
        };

        $lineas = [];
        $lineas[] = $centrar($nombreEmpresa);
        $lineas[] = $linea;
        $lineas[] = 'Folio: ' . $venta->folio;
        $lineas[] = 'Fecha: ' . $venta->created_at?->format('d/m/Y H:i') ?? '';
        $lineas[] = 'Sucursal: ' . ($venta->sucursal->nombre ?? '');
        $lineas[] = $linea;
        foreach ($venta->detalles as $d) {
            $prod = $d->producto;
            $nombre = $prod ? mb_substr($prod->nombre, 0, 18) : 'Producto';
            $cant = number_format((float) $d->cantidad_pedida, 2);
            $unid = $prod->unidad_nombre ?? 'm3';
            $lineas[] = sprintf('%s %s %s', $nombre, $cant, $unid);
        }
        $lineas[] = $linea;
        $lineas[] = 'TOTAL: $' . number_format((float) $venta->total, 2);
        $lineas[] = $linea;
        $lineas[] = '';

        $contenido = implode("\n", $lineas);

        return response()->json(['data' => ['contenido' => $contenido]], 200);
    }

    /**
     * (Vigilante - Macuspana) Generar un QR local con número de viajes definidos.
     * El payload mantiene el formato plano con pipes: uuid|idSucursal|idProd|cant|idUnidad|...
     */
    public function generarQrVigilanteLocal(Request $request): JsonResponse
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursalId = $config?->sucursal_id;

        if (! $sucursalId) {
            return response()->json([
                'errors' => [
                    'sucursal' => ['No hay sucursal configurada en Configuración de la empresa.'],
                ],
            ], 422);
        }

        $sucursal = Sucursal::findOrFail($sucursalId);
        if (! $sucursal->isMacuspana()) {
            return response()->json([
                'errors' => [
                    'sucursal' => ['La generación de QR para vigilante solo aplica en sucursales tipo almacén (ej. Macuspana).'],
                ],
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'producto_id' => ['required', 'exists:productos,id'],
            'venta_id' => ['nullable', 'exists:ventas,id'],
            'numero_viajes' => ['nullable', 'integer', 'min:1'],
        ], [
            'producto_id.required' => 'Debe seleccionar un producto.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $producto = Producto::findOrFail($request->producto_id);

        // Restricción venta_almacen: solo Polvo, Rezaga y Balastre para nuevos tickets/pedidos locales.
        $casetaService = app(CasetaService::class);
        if (! $casetaService->isProductAllowedForLocalQr($producto)) {
            $nombresPermitidos = implode(', ', $casetaService->getAllowedProductNamesForLocalQr());
            return response()->json([
                'errors' => [
                    'producto_id' => [
                        "En esta sucursal (venta y almacén) solo se pueden generar tickets para: {$nombresPermitidos}. El producto \"{$producto->nombre}\" no está permitido para nuevos pedidos en caseta.",
                    ],
                ],
            ], 422);
        }

        $ventaId = $request->filled('venta_id') ? (int) $request->venta_id : null;
        $numeroViajesInput = $request->filled('numero_viajes') ? (int) $request->numero_viajes : null;

        $venta = null;
        if ($ventaId) {
            $venta = Venta::where('id', $ventaId)->where('sucursal_id', $sucursal->id)->firstOrFail();
            if ($venta->viajes_permitidos === null && $numeroViajesInput === null) {
                return response()->json([
                    'errors' => ['numero_viajes' => ['Este pedido aún no tiene número de viajes. Indique la cantidad de viajes del pedido.']],
                ], 422);
            }
            if ($venta->viajes_permitidos === null) {
                $venta->viajes_permitidos = $numeroViajesInput;
                $venta->viajes_usados = 0;
                $venta->saveQuietly();
            }
            $numeroViajes = (int) $venta->viajes_permitidos;
        } else {
            if ($numeroViajesInput === null || $numeroViajesInput < 1) {
                return response()->json([
                    'errors' => ['numero_viajes' => ['Para un pedido nuevo debe indicar el número de viajes del pedido.']],
                ], 422);
            }
            $venta = Venta::create([
                'folio' => $this->generarFolioUnico(),
                'sucursal_id' => $sucursal->id,
                'usuario_id' => auth()->id(),
                'total' => 0,
                // Pedido generado en caseta: pendiente de cobro en oficina
                'estatus' => 'pendiente_pago',
                'tipo' => 'venta',
                'viajes_permitidos' => $numeroViajesInput,
                'viajes_usados' => 0,
            ]);
            $numeroViajes = (int) $venta->viajes_permitidos;
        }

        $uuid = (string) Str::uuid();
        $idUnidad = $producto->unidad_medida_id ?? 1;

        // Formato plano: uuid|idSucursal|idProd|cant|idUnidad. "cant" = viajes del pedido (solo informativo en payload).
        $payload = implode('|', [
            $uuid,
            $sucursal->id,
            $producto->id,
            $numeroViajes,
            $idUnidad,
        ]);

        $registro = VigilanteQr::create([
            'venta_id' => $venta->id,
            'uuid' => $uuid,
            'sucursal_origen_id' => $sucursal->id,
            'sucursal_local_id' => $sucursal->id,
            'origen' => 'local',
            'payload_original' => $payload,
            'viajes_permitidos' => $numeroViajes,
            'viajes_usados' => 0,
            'estatus' => 'activo',
        ]);

        return response()->json([
            'data' => [
                'qr_payload' => $payload,
                'registro' => $registro,
                'venta_id' => $venta->id,
                'folio' => $venta->folio,
                'viajes_permitidos' => $venta->viajes_permitidos,
                'viajes_usados' => $venta->viajes_usados,
            ],
            'message' => 'QR local generado correctamente.',
        ], 201);
    }

    /**
     * (Vigilante - Macuspana) Validar un QR escaneado y registrar el viaje.
     *
     * - Si el QR es externo (Villahermosa u otra sucursal), se registra como "importado"
     *   la primera vez que se escanea.
     * - Si es local (Macuspana), se verifica contra los viajes permitidos.
     */
    public function validarQrVigilante(Request $request): JsonResponse
    {
        $config = ConfiguracionEmpresa::obtenerConfiguracion();
        $sucursalLocalId = $config?->sucursal_id;

        if (! $sucursalLocalId) {
            return response()->json([
                'errors' => [
                    'sucursal' => ['No hay sucursal configurada en Configuración de la empresa.'],
                ],
            ], 422);
        }

        $sucursalLocal = Sucursal::findOrFail($sucursalLocalId);
        if (! $sucursalLocal->isMacuspana()) {
            return response()->json([
                'errors' => [
                    'sucursal' => ['La validación de QR para vigilante solo aplica en sucursales tipo almacén (ej. Macuspana).'],
                ],
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'qr_payload' => ['required', 'string'],
        ], [
            'qr_payload.required' => 'El payload del QR es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rawPayload = trim($request->qr_payload);
        $partes = array_filter(explode('|', $rawPayload), function ($v) {
            return $v !== '';
        });

        if (count($partes) < 2) {
            return response()->json([
                'errors' => [
                    'qr_payload' => ['Payload del QR inválido. Formato esperado: uuid|idSucursal|idProd|cant|idUnidad|...'],
                ],
            ], 422);
        }

        // Soportar tanto uuid:sucursal|... como uuid|sucursal|...
        $uuid = null;
        $sucursalOrigenId = null;
        $detalleOffset = 0;

        $cabecera = $partes[0];
        if (str_contains($cabecera, ':')) {
            [$uuid, $sucursalIdStr] = explode(':', $cabecera, 2);
            $sucursalOrigenId = is_numeric($sucursalIdStr) ? (int) $sucursalIdStr : null;
            $detalleOffset = 1; // Detalles empiezan en $partes[1]
        } else {
            $uuid = $cabecera;
            $sucursalOrigenId = isset($partes[1]) && is_numeric($partes[1]) ? (int) $partes[1] : null;
            $detalleOffset = 2; // uuid|idSucursal|...
        }

        if (! $uuid) {
            return response()->json([
                'errors' => [
                    'qr_payload' => ['No se pudo obtener el UUID del QR.'],
                ],
            ], 422);
        }

        $esExterno = $sucursalOrigenId !== null && $sucursalOrigenId !== (int) $sucursalLocalId;

        // Buscar o crear registro en tabla de Vigilante
        /** @var VigilanteQr|null $registro */
        $registro = VigilanteQr::where('uuid', $uuid)->first();

        if (! $registro) {
            if ($esExterno) {
                // Registrar primera vez como importado, con un solo viaje permitido.
                $registro = VigilanteQr::create([
                    'uuid' => $uuid,
                    'sucursal_origen_id' => $sucursalOrigenId,
                    'sucursal_local_id' => $sucursalLocalId,
                    'origen' => 'importado',
                    'payload_original' => $rawPayload,
                    'viajes_permitidos' => 1,
                    'viajes_usados' => 0,
                    'estatus' => 'activo',
                ]);
            } else {
                return response()->json([
                    'errors' => [
                        'qr_payload' => ['QR local no registrado. Genere primero el QR en este sistema.'],
                    ],
                ], 422);
            }
        }

        // Viajes a nivel pedido (venta) cuando el QR está asociado a una venta
        $venta = $registro->venta_id ? $registro->venta()->lockForUpdate()->first() : null;
        $viajesPermitidos = $venta ? (int) $venta->viajes_permitidos : $registro->viajes_permitidos;
        $viajesUsados = $venta ? (int) $venta->viajes_usados : $registro->viajes_usados;

        if ($viajesUsados >= $viajesPermitidos) {
            if ($registro->estatus !== 'agotado') {
                $registro->estatus = 'agotado';
                $registro->saveQuietly();
            }
            return response()->json([
                'data' => [
                    'uuid' => $registro->uuid,
                    'origen' => $registro->origen,
                    'estatus' => 'agotado',
                    'viajes_permitidos' => $viajesPermitidos,
                    'viajes_usados' => $viajesUsados,
                    'viajes_restantes' => 0,
                    'sucursal_origen_id' => $registro->sucursal_origen_id,
                    'sucursal_local_id' => $registro->sucursal_local_id,
                ],
                'message' => 'El QR ya no tiene viajes disponibles.',
            ], 200);
        }

        // Registrar viaje (a nivel pedido si hay venta_id)
        if ($venta) {
            $venta->viajes_usados += 1;
            $venta->saveQuietly();
            $viajesUsados = (int) $venta->viajes_usados;
            if ($viajesUsados >= $viajesPermitidos) {
                $registro->estatus = 'agotado';
                $registro->saveQuietly();
            }
        } else {
            $registro->viajes_usados += 1;
            if ($registro->viajes_usados >= $registro->viajes_permitidos) {
                $registro->estatus = 'agotado';
            }
            $registro->saveQuietly();
            $viajesUsados = $registro->viajes_usados;
        }

        $viajesRestantes = max(0, $viajesPermitidos - $viajesUsados);

        // Extraer primera línea de detalle (si existe) para mostrar al vigilante
        $detalle = null;
        if ($detalleOffset < count($partes) - 2) {
            $productoId = (int) $partes[$detalleOffset];
            $cantidad = (float) $partes[$detalleOffset + 1];
            $idUnidad = (int) $partes[$detalleOffset + 2];

            $producto = Producto::find($productoId);
            $detalle = [
                'producto_id' => $productoId,
                'producto_nombre' => $producto?->nombre,
                'cantidad' => $cantidad,
                'unidad_id' => $idUnidad,
            ];
        }

        return response()->json([
            'data' => [
                'uuid' => $registro->uuid,
                'origen' => $registro->origen,
                'estatus' => $registro->estatus,
                'viajes_permitidos' => $viajesPermitidos,
                'viajes_usados' => $viajesUsados,
                'viajes_restantes' => $viajesRestantes,
                'sucursal_origen_id' => $registro->sucursal_origen_id,
                'sucursal_local_id' => $registro->sucursal_local_id,
                'detalle_principal' => $detalle,
            ],
            'message' => $esExterno
                ? 'QR importado validado correctamente.'
                : 'QR local validado correctamente.',
        ], 200);
    }

    /**
     * Normaliza pagos para que la suma de montos registrados sea exactamente el total de la venta.
     * Si el cliente envía suma > total (p. ej. efectivo con cambio), se ajusta el monto aplicado por pago.
     * monto_recibido se conserva para auditoría (lo que el cliente entregó en efectivo).
     *
     * @param  array<int, array{metodo_pago: string, monto: float, referencia_pago?: string, monto_recibido?: float}>  $pagosInput
     * @return array<int, array{metodo_pago: string, monto: float, referencia_pago?: string, monto_recibido?: float}>
     */
    private function normalizarPagosParaTotal(array $pagosInput, float $total): array
    {
        $total = round($total, 2);
        $restante = $total;
        $result = [];
        $last = count($pagosInput) - 1;

        foreach ($pagosInput as $idx => $pago) {
            $montoBruto = (float) ($pago['monto'] ?? 0);
            $montoRegistrado = $idx === $last
                ? round($restante, 2)
                : round(min($montoBruto, $restante), 2);
            $restante -= $montoRegistrado;
            $result[] = [
                'metodo_pago' => $pago['metodo_pago'],
                'monto' => $montoRegistrado,
                'referencia_pago' => $pago['referencia_pago'] ?? null,
                'monto_recibido' => isset($pago['monto_recibido']) ? (float) $pago['monto_recibido'] : null,
            ];
        }

        return $result;
    }

    private function generarFolioUnico(): string
    {
        do {
            $folio = 'VTA-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Venta::where('folio', $folio)->exists());

        return $folio;
    }
}
