<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\ConfiguracionHardware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

/**
 * Control de Salidas (legacy): boletos de salida de camiones/volteos.
 * Para el flujo de Ventas e Inventario por sucursales (QR pedidos, importar en Macuspana), usar VentaController.
 */
class BoletoController extends Controller
{
    public function index(Request $request): JsonResponse
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

        $boletos = $query->orderBy('fecha_generacion', 'desc')->paginate(20);

        return response()->json(['data' => $boletos], JsonResponse::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'placa' => ['required', 'string', 'max:20'],
            'conductor' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string'],
            'foto_ruta' => ['nullable', 'string'], // Ruta de la foto ya capturada (opcional)
        ], [
            'placa.required' => 'La placa es obligatoria.',
            'placa.max' => 'La placa no debe exceder 20 caracteres.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Generar folio único
        $folio = $this->generarFolioUnico();

        // Generar código QR
        $codigoQr = $this->generarCodigoQR($folio);

        $data = [
            'folio' => $folio,
            'codigo_qr' => $codigoQr,
            'estatus' => 'pendiente',
            'foto_ruta' => $request->foto_ruta ?: null,
            'placa' => $request->placa,
            'conductor' => $request->conductor,
            'observaciones' => $request->observaciones,
            'usuario_generador_id' => auth()->id(),
            'fecha_generacion' => now(),
        ];

        $boleto = Boleto::create($data);
        $boleto->load('usuarioGenerador');

        return response()->json(['data' => $boleto], JsonResponse::HTTP_CREATED);
    }

    public function show(Boleto $boleto): JsonResponse
    {
        $boleto->load(['usuarioGenerador', 'usuarioValidador']);
        return response()->json(['data' => $boleto], JsonResponse::HTTP_OK);
    }

    /**
     * Capturar foto desde cámara IP.
     * Si url_snapshot es RTSP (rtsp://...) usa FFmpeg para extraer 1 frame del stream.
     * HiLook NVR no tiene ISAPI snapshot; RTSP + FFmpeg es la forma que sí funciona.
     */
    public function capturarFoto(Request $request): JsonResponse
    {
        $camara = ConfiguracionHardware::camaras()->activos()->first();

        if (!$camara) {
            return $this->devolverPlaceholderConAviso('No hay cámara IP configurada. Configure una en Administración → Hardware con URL RTSP.');
        }

        $url = $this->obtenerUrlCaptura($camara);

        // RTSP: captura desde video con FFmpeg (única opción en HiLook NVR)
        if (str_starts_with((string) $url, 'rtsp://')) {
            return $this->capturarFotoDesdeRtsp($url);
        }

        // HTTP/ISAPI: snapshot por URL (cuando el equipo lo soporte)
        try {
            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) {
                throw new \Exception('Error al capturar foto: ' . $response->status());
            }

            $nombreArchivo = 'volteos/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($nombreArchivo, $response->body());

            return response()->json([
                'data' => [
                    'foto_ruta' => $nombreArchivo,
                    'foto_url' => asset('storage/' . $nombreArchivo),
                ]
            ], JsonResponse::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'errors' => ['camera' => ['Error al capturar foto: ' . $e->getMessage()]]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Obtiene la URL de captura: url_snapshot si existe, o construida desde ip/puerto/usuario/password.
     * Para RTSP se puede guardar la URL completa en url_snapshot (ej. rtsp://user:pass@ip:554/Streaming/Channels/101).
     */
    private function obtenerUrlCaptura(ConfiguracionHardware $camara): string
    {
        if (!empty($camara->url_snapshot)) {
            return $camara->url_snapshot;
        }

        $credenciales = '';
        if ($camara->usuario && $camara->password) {
            $credenciales = $camara->usuario . ':' . $camara->password . '@';
        }
        $host = $camara->ip_url;
        $puerto = $camara->puerto ?: 80;
        return 'http://' . $credenciales . $host . ':' . $puerto . '/snapshot.cgi';
    }

    /**
     * Captura un frame del stream RTSP con FFmpeg.
     * Requiere: ffmpeg instalado y exec/Process permitido.
     */
    private function capturarFotoDesdeRtsp(string $rtsp): JsonResponse
    {
        $nombreArchivo = 'volteos/' . Str::uuid() . '.jpg';
        $rutaCompleta = Storage::disk('public')->path($nombreArchivo);

        $dir = dirname($rutaCompleta);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // -ss después de -i: saltar 2 s para evitar frame negro; -vf scale: tamaño mínimo usable
        $result = Process::timeout(25)->run([
            'ffmpeg',
            '-y',
            '-rtsp_transport', 'tcp',
            '-i', $rtsp,
            '-ss', '00:00:02',
            '-frames:v', '1',
            '-vf', 'scale=1280:-2',
            '-q:v', '2',
            $rutaCompleta,
        ]);

        if (!$result->successful()) {
            $salida = trim($result->errorOutput() ?: $result->output());
            return response()->json([
                'errors' => [
                    'camera' => [
                        'Error al capturar desde RTSP (FFmpeg). ¿Está instalado ffmpeg? ' . ($salida ?: $result->exitCode())
                    ]
                ]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!is_file($rutaCompleta) || filesize($rutaCompleta) === 0) {
            @unlink($rutaCompleta);
            return response()->json([
                'errors' => ['camera' => ['FFmpeg no generó imagen. Compruebe la URL RTSP y que el canal exista.']]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'data' => [
                'foto_ruta' => $nombreArchivo,
                'foto_url' => asset('storage/' . $nombreArchivo),
            ]
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Devuelve imagen placeholder con aviso cuando no hay cámara configurada.
     * El frontend puede mostrar mensaje para configurar la cámara en Administración → Hardware.
     */
    private function devolverPlaceholderConAviso(string $mensajeAviso): JsonResponse
    {
        $imagenBytes = $this->crearPlaceholderJpeg();
        if ($imagenBytes === null) {
            return response()->json([
                'errors' => ['camera' => [$mensajeAviso]]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        $nombreArchivo = 'volteos/' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($nombreArchivo, $imagenBytes);
        return response()->json([
            'data' => [
                'foto_ruta' => $nombreArchivo,
                'foto_url'  => asset('storage/' . $nombreArchivo),
            ],
            'prueba' => true,
            'mensaje_aviso' => $mensajeAviso,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * SOLO PRUEBAS — Snapshot con datos estáticos (sin cámara).
     * El HiLook NVR-108H-D/8P no tiene endpoint ISAPI para snapshot; en producción
     * usar RTSP + FFmpeg. Este endpoint devuelve la misma estructura que capturarFoto.
     */
    public function capturarFotoTest(Request $request): JsonResponse
    {
        $nombreArchivo = 'volteos/' . Str::uuid() . '.jpg';

        $imagenBytes = $this->crearPlaceholderJpeg();

        if ($imagenBytes === null) {
            return response()->json([
                'errors' => ['camera' => ['No se pudo generar imagen de prueba (GD no disponible).']]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        Storage::disk('public')->put($nombreArchivo, $imagenBytes);

        return response()->json([
            'data' => [
                'foto_ruta' => $nombreArchivo,
                'foto_url'  => asset('storage/' . $nombreArchivo),
            ]
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Genera un JPEG placeholder para pruebas (640x480). Requiere extensión GD.
     */
    private function crearPlaceholderJpeg(): ?string
    {
        if (!function_exists('imagecreate') || !function_exists('imagejpeg') || !function_exists('imagestring')) {
            return null;
        }

        $w = 640;
        $h = 480;
        $img = @imagecreate($w, $h);
        if ($img === false) {
            return null;
        }

        $gris = imagecolorallocate($img, 80, 80, 80);
        $blanco = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $gris);
        imagestring($img, 5, (int) ($w / 2 - 72), (int) ($h / 2 - 7), 'PRUEBA - Sin camara', $blanco);
        ob_start();
        imagejpeg($img, null, 85);
        $bytes = ob_get_clean();
        imagedestroy($img);

        return $bytes ?: null;
    }

    /**
     * Validar boleto (por QR o folio)
     */
    public function validar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'folio' => ['required_without:codigo_qr', 'string'],
            'codigo_qr' => ['required_without:folio', 'string'],
        ], [
            'folio.required_without' => 'Debe proporcionar el folio o código QR.',
            'codigo_qr.required_without' => 'Debe proporcionar el folio o código QR.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $query = Boleto::query();

        if ($request->has('folio')) {
            $query->where('folio', $request->folio);
        } elseif ($request->has('codigo_qr')) {
            $query->where('codigo_qr', $request->codigo_qr);
        }

        $boleto = $query->first();

        if (!$boleto) {
            return response()->json([
                'errors' => ['boleto' => ['Boleto no encontrado.']],
                'valido' => false,
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($boleto->estatus !== 'pendiente') {
            return response()->json([
                'errors' => ['boleto' => ['El boleto ya fue utilizado o está cancelado.']],
                'valido' => false,
                'estatus' => $boleto->estatus,
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Marcar como utilizado
        $boleto->update([
            'estatus' => 'utilizado',
            'usuario_validador_id' => auth()->id(),
            'fecha_validacion' => now(),
        ]);

        $boleto->load(['usuarioGenerador', 'usuarioValidador']);

        return response()->json([
            'data' => $boleto,
            'valido' => true,
            'message' => 'Boleto validado correctamente.',
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Generar folio único
     */
    private function generarFolioUnico(): string
    {
        do {
            $folio = 'BOL-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Boleto::where('folio', $folio)->exists());

        return $folio;
    }

    /**
     * Generar código QR
     */
    private function generarCodigoQR(string $folio): string
    {
        // Generar código QR como string base64 o JSON
        $qrData = [
            'folio' => $folio,
            'timestamp' => now()->toIso8601String(),
        ];

        return base64_encode(json_encode($qrData));
    }
}

