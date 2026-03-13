<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Cliente;
use App\Models\Contacto;
use App\Models\FormularioContacto;
use App\Models\Galeria;
use App\Models\PaginaAviso;
use App\Models\PaginaHistoria;
use App\Models\PaginaNosotros;
use App\Models\PaginaTecnologia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicSiteController extends Controller
{
    public function home(): JsonResponse
    {
        $banners = Cache::remember('public_banners', now()->addMinutes(10), function () {
            return Banner::query()
                ->where('activo', true)
                ->orderBy('orden')
                ->get();
        });

        $servicios = Cache::remember('public_servicios', now()->addMinutes(10), function () {
            return \App\Models\Servicio::query()
                ->where('activo', true)
                ->orderBy('orden')
                ->get();
        });

        return response()->json([
            'banners' => $banners,
            'servicios' => $servicios,
        ], JsonResponse::HTTP_OK);
    }

    public function serviciosIndex(): JsonResponse
    {
        $servicios = \App\Models\Servicio::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        return response()->json($servicios, JsonResponse::HTTP_OK);
    }

    public function serviciosShow(\App\Models\Servicio $servicio): JsonResponse
    {
        if (!$servicio->activo) {
            return response()->json(['message' => 'Not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json($servicio, JsonResponse::HTTP_OK);
    }

    public function clientesIndex(): JsonResponse
    {
        $clientes = Cliente::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        return response()->json($clientes, JsonResponse::HTTP_OK);
    }

    public function galeriaIndex(): JsonResponse
    {
        $items = Galeria::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        return response()->json($items, JsonResponse::HTTP_OK);
    }

    public function contactosIndex(): JsonResponse
    {
        $contactos = Contacto::query()
            ->orderBy('orden')
            ->get();

        return response()->json($contactos, JsonResponse::HTTP_OK);
    }

    public function paginaNosotros(): JsonResponse
    {
        $pagina = PaginaNosotros::query()
            ->with([
                'imagenes' => fn ($q) => $q->orderBy('orden'),
                'progreso' => fn ($q) => $q->orderBy('orden'),
            ])
            ->first();

        return response()->json($pagina, JsonResponse::HTTP_OK);
    }

    public function paginaHistoria(): JsonResponse
    {
        $pagina = PaginaHistoria::query()
            ->with([
                'eventos' => fn ($q) => $q->orderBy('orden'),
                'imagenes' => fn ($q) => $q->orderBy('orden'),
            ])
            ->first();

        return response()->json($pagina, JsonResponse::HTTP_OK);
    }

    public function paginaTecnologia(): JsonResponse
    {
        $pagina = PaginaTecnologia::query()
            ->with([
                'secciones' => fn ($q) => $q->orderBy('orden'),
            ])
            ->first();

        return response()->json($pagina, JsonResponse::HTTP_OK);
    }

    public function paginaAviso(): JsonResponse
    {
        $pagina = PaginaAviso::query()
            ->with([
                'secciones.listas' => fn ($q) => $q->orderBy('orden'),
            ])
            ->first();

        return response()->json($pagina, JsonResponse::HTTP_OK);
    }

    public function enviarFormularioContacto(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:255'],
            'mensaje' => ['required', 'string'],
        ]);

        $formulario = FormularioContacto::create($validated + ['leido' => false]);

        // Comentario: Aquí se podría enviar un correo de notificación usando Mail::to(...)->send(...)

        return response()->json([
            'success' => true,
            'data' => $formulario,
        ], JsonResponse::HTTP_CREATED);
    }
}

