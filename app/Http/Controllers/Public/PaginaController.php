<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicSiteService;
use Inertia\Inertia;
use Inertia\Response;

class PaginaController extends Controller
{
    public function __construct(private readonly PublicSiteService $publicSiteService)
    {
    }

    public function nosotros(): Response
    {
        return Inertia::render('Public/Nosotros', [
            'pagina' => $this->publicSiteService->paginaNosotros(),
        ]);
    }

    public function historia(): Response
    {
        return Inertia::render('Public/Historia', [
            'pagina' => $this->publicSiteService->paginaHistoria(),
        ]);
    }

    public function tecnologia(): Response
    {
        return Inertia::render('Public/Tecnologia', [
            'pagina' => $this->publicSiteService->paginaTecnologia(),
        ]);
    }

    public function aviso(): Response
    {
        return Inertia::render('Public/Aviso', [
            'pagina' => $this->publicSiteService->paginaAviso(),
        ]);
    }
}
