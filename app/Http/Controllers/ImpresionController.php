<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionHardware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImpresionController extends Controller
{
    /**
     * Listar impresoras configuradas (IP y nombre).
     */
    public function index(): JsonResponse
    {
        $impresoras = ConfiguracionHardware::impresoras()->activos()->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'nombre' => $item->nombre,
                'ip_url' => $item->ip_url,
                'puerto' => $item->puerto ?? 9100,
                'activo' => $item->activo,
            ];
        });

        return response()->json(['data' => $impresoras], 200);
    }

    /**
     * Mostrar una impresora.
     */
    public function show(ConfiguracionHardware $configuracionHardware): JsonResponse
    {
        if ($configuracionHardware->tipo !== 'impresora') {
            return response()->json(['errors' => ['impresora' => ['El registro no es una impresora.']]], 404);
        }

        return response()->json([
            'data' => [
                'id' => $configuracionHardware->id,
                'nombre' => $configuracionHardware->nombre,
                'ip_url' => $configuracionHardware->ip_url,
                'puerto' => $configuracionHardware->puerto ?? 9100,
                'activo' => $configuracionHardware->activo,
            ],
        ], 200);
    }

    /**
     * Crear o actualizar configuración de impresora (IP y nombre).
     * Usa la tabla configuracion_hardware con tipo 'impresora'.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
            'ip_url' => ['required', 'string', 'max:255'],
            'puerto' => ['nullable', 'integer', 'min:1', 'max:65535'],
        ], [
            'nombre.required' => 'El nombre de la impresora es obligatorio.',
            'ip_url.required' => 'La IP o URL de la impresora es obligatoria.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $impresora = ConfiguracionHardware::create([
            'tipo' => 'impresora',
            'nombre' => $request->nombre,
            'ip_url' => $request->ip_url,
            'puerto' => $request->puerto ?? 9100,
            'activo' => true,
        ]);

        return response()->json([
            'data' => [
                'id' => $impresora->id,
                'nombre' => $impresora->nombre,
                'ip_url' => $impresora->ip_url,
                'puerto' => $impresora->puerto ?? 9100,
                'activo' => $impresora->activo,
            ],
            'message' => 'Impresora guardada correctamente.',
        ], 201);
    }

    /**
     * Actualizar IP y nombre de impresora.
     */
    public function update(Request $request, ConfiguracionHardware $configuracionHardware): JsonResponse
    {
        if ($configuracionHardware->tipo !== 'impresora') {
            return response()->json(['errors' => ['impresora' => ['El registro no es una impresora.']]], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => ['sometimes', 'string', 'max:255'],
            'ip_url' => ['sometimes', 'string', 'max:255'],
            'puerto' => ['nullable', 'integer', 'min:1', 'max:65535'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('nombre')) {
            $configuracionHardware->nombre = $request->nombre;
        }
        if ($request->has('ip_url')) {
            $configuracionHardware->ip_url = $request->ip_url;
        }
        if ($request->has('puerto')) {
            $configuracionHardware->puerto = $request->puerto;
        }
        $configuracionHardware->save();

        return response()->json([
            'data' => [
                'id' => $configuracionHardware->id,
                'nombre' => $configuracionHardware->nombre,
                'ip_url' => $configuracionHardware->ip_url,
                'puerto' => $configuracionHardware->puerto ?? 9100,
                'activo' => $configuracionHardware->activo,
            ],
            'message' => 'Impresora actualizada correctamente.',
        ], 200);
    }

    /**
     * Enviar comando de impresión RAW al backend (envía datos por socket a la impresora).
     */
    public function imprimirRaw(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'impresora_id' => ['required', 'exists:configuracion_hardware,id'],
            'contenido' => ['required', 'string'],
            'encoding' => ['nullable', 'string', 'in:UTF-8,Windows-1252,ASCII'],
        ], [
            'impresora_id.required' => 'La impresora es obligatoria.',
            'contenido.required' => 'El contenido a imprimir es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $impresora = ConfiguracionHardware::find($request->impresora_id);
        if ($impresora->tipo !== 'impresora') {
            return response()->json(['errors' => ['impresora' => ['El registro no es una impresora.']]], 404);
        }

        $host = $impresora->ip_url;
        $puerto = (int) ($impresora->puerto ?? 9100);
        $contenido = $request->contenido;
        $encoding = $request->encoding ?? 'UTF-8';

        if ($encoding !== 'UTF-8') {
            $contenido = mb_convert_encoding($contenido, $encoding, 'UTF-8');
        }

        $raw = $contenido;
        if (stripos($impresora->modelo ?? '', 'esc/p') !== false || stripos($impresora->nombre ?? '', 'termo') !== false) {
            $raw = $this->agregarComandosEscP($contenido);
        }

        try {
            $fp = @fsockopen($host, $puerto, $errno, $errstr, 10);
            if (! $fp) {
                return response()->json([
                    'errors' => ['impresora' => ["No se pudo conectar a la impresora: {$errstr} ({$errno})."]],
                ], 502);
            }

            stream_set_timeout($fp, 5);
            fwrite($fp, $raw);
            fclose($fp);
        } catch (\Throwable $e) {
            return response()->json([
                'errors' => ['impresora' => ['Error al enviar a la impresora: ' . $e->getMessage()]],
            ], 502);
        }

        return response()->json([
            'message' => 'Contenido enviado a la impresora correctamente.',
        ], 200);
    }

    /**
     * Opcional: añadir comandos ESC/P para impresoras térmicas (corte de papel, etc.).
     */
    private function agregarComandosEscP(string $texto): string
    {
        $inicio = "\x1B@";
        $corte = "\x1DVA\x03";
        return $inicio . $texto . $corte;
    }
}
