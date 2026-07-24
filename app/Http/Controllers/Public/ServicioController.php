<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use App\Services\PublicSiteService;
use Inertia\Inertia;
use Inertia\Response;

class ServicioController extends Controller
{
    public function __construct(private readonly PublicSiteService $publicSiteService)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Public/Servicios/Index', [
            'servicios' => $this->publicSiteService->servicios(),
        ]);
    }

    public function show(Servicio $servicio): Response
    {
        $servicio = $this->publicSiteService->servicio($servicio);

        if (!$servicio) {
            abort(404);
        }

        return Inertia::render('Public/Servicios/Show', [
            'servicio' => $servicio,
        ]);
    }
}
