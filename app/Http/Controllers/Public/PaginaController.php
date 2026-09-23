<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PaginaHistoria;
use App\Services\PublicSiteService;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    /**
     * Sirve el CV/brochure PDF dinámico (mismo path que producción: /cv.pdf).
     */
    public function cv(): StreamedResponse
    {
        $pagina = PaginaHistoria::query()->first();
        $path = $pagina?->cv_pdf;

        if (! $path || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path, 'cv.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="cv.pdf"',
        ]);
    }
}
