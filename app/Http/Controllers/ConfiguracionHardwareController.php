<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionHardware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;

class ConfiguracionHardwareController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ConfiguracionHardware::query();

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('activo')) {
            $query->where('activo', $request->activo);
        }

        $hardware = $query->orderBy('tipo')->orderBy('nombre')->get();

        return response()->json(['data' => $hardware], JsonResponse::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tipo' => ['required', 'string', 'in:camara_ip,impresora'],
            'nombre' => ['required', 'string', 'max:255'],
            'ip_url' => ['nullable', 'string', 'max:255'],
            'puerto' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'usuario' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'url_snapshot' => ['nullable', 'string', 'max:500'],
            'modelo' => ['nullable', 'string', 'max:255'],
            'configuracion_adicional' => ['nullable', 'json'],
            'activo' => ['boolean'],
        ], [
            'tipo.required' => 'El tipo es obligatorio.',
            'tipo.in' => 'El tipo debe ser "camara_ip" o "impresora".',
            'nombre.required' => 'El nombre es obligatorio.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $request->all();
        if ($request->has('configuracion_adicional')) {
            $data['configuracion_adicional'] = json_decode($request->configuracion_adicional, true);
        }

        $hardware = ConfiguracionHardware::create($data);

        return response()->json(['data' => $hardware], JsonResponse::HTTP_CREATED);
    }

    public function show(ConfiguracionHardware $configuracionHardware): JsonResponse
    {
        return response()->json(['data' => $configuracionHardware], JsonResponse::HTTP_OK);
    }

    public function update(Request $request, ConfiguracionHardware $configuracionHardware): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tipo' => ['required', 'string', 'in:camara_ip,impresora'],
            'nombre' => ['required', 'string', 'max:255'],
            'ip_url' => ['nullable', 'string', 'max:255'],
            'puerto' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'usuario' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'url_snapshot' => ['nullable', 'string', 'max:500'],
            'modelo' => ['nullable', 'string', 'max:255'],
            'configuracion_adicional' => ['nullable', 'json'],
            'activo' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $request->all();
        if ($request->has('configuracion_adicional')) {
            $data['configuracion_adicional'] = json_decode($request->configuracion_adicional, true);
        }

        $configuracionHardware->update($data);

        return response()->json(['data' => $configuracionHardware], JsonResponse::HTTP_OK);
    }

    public function destroy(ConfiguracionHardware $configuracionHardware): JsonResponse
    {
        $configuracionHardware->update(['activo' => false]);
        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * SOLO PRUEBAS — Prueba video con valores estáticos (sin login, sin BD).
     * Usa una URL RTSP fija. Requiere ffprobe/ffmpeg instalado.
     */
    public function probarVideoTest(): JsonResponse
    {
        $rtsp = 'rtsp://admin:3lfutur0@192.168.1.68:554/Streaming/Channels/101';

        $result = Process::timeout(15)->run([
            'ffprobe',
            '-v', 'error',
            '-rtsp_transport', 'tcp',
            '-i', $rtsp,
            '-show_entries', 'format=duration',
            '-of', 'default=noprint_wrappers=1:nokey=1',
        ]);

        if ($result->successful()) {
            return response()->json([
                'accesible' => true,
                'mensaje' => 'El video es accesible. El stream RTSP responde correctamente.',
                'url_probada' => $rtsp,
            ], JsonResponse::HTTP_OK);
        }

        $salida = trim($result->errorOutput() ?: $result->output() ?: '');
        return response()->json([
            'accesible' => false,
            'mensaje' => $salida ? 'No se pudo acceder al video: ' . $salida : 'No se pudo conectar al stream. Compruebe que ffprobe/ffmpeg esté instalado y que el NVR esté encendido y en la red.',
            'url_probada' => $rtsp,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Comprueba si se puede acceder al video (stream RTSP) de la cámara.
     * Usa ffprobe para conectar al stream. Requiere ffprobe/ffmpeg instalado.
     */
    public function probarVideo(ConfiguracionHardware $configuracionHardware): JsonResponse
    {
        if ($configuracionHardware->tipo !== 'camara_ip') {
            return response()->json([
                'accesible' => false,
                'mensaje' => 'Solo se puede probar video en dispositivos tipo Cámara IP.',
            ], JsonResponse::HTTP_OK);
        }

        $url = $configuracionHardware->url_snapshot;
        if (empty($url)) {
            return response()->json([
                'accesible' => false,
                'mensaje' => 'Configure la URL RTSP (ej. rtsp://usuario:password@192.168.1.68:554/Streaming/Channels/101) en el campo URL Snapshot o RTSP.',
            ], JsonResponse::HTTP_OK);
        }

        if (!str_starts_with($url, 'rtsp://')) {
            return response()->json([
                'accesible' => false,
                'mensaje' => 'Para probar el video debe usar una URL RTSP (rtsp://...). Las URLs HTTP no permiten ver el stream.',
            ], JsonResponse::HTTP_OK);
        }

        $result = Process::timeout(15)->run([
            'ffprobe',
            '-v', 'error',
            '-rtsp_transport', 'tcp',
            '-i', $url,
            '-show_entries', 'format=duration',
            '-of', 'default=noprint_wrappers=1:nokey=1',
        ]);

        if ($result->successful()) {
            return response()->json([
                'accesible' => true,
                'mensaje' => 'El video es accesible. El stream RTSP responde correctamente.',
            ], JsonResponse::HTTP_OK);
        }

        $salida = trim($result->errorOutput() ?: $result->output() ?: '');
        $mensaje = $salida
            ? 'No se pudo acceder al video: ' . $salida
            : 'No se pudo conectar al stream. Compruebe la URL RTSP, que ffprobe/ffmpeg esté instalado y que el NVR/cámara esté encendido y en la red.';

        return response()->json([
            'accesible' => false,
            'mensaje' => $mensaje,
        ], JsonResponse::HTTP_OK);
    }
}

