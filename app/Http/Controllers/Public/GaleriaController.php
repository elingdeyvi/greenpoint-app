<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicSiteService;
use Inertia\Inertia;
use Inertia\Response;

class GaleriaController extends Controller
{
    public function __construct(private readonly PublicSiteService $publicSiteService)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Public/Galeria', [
            'items' => $this->publicSiteService->galeria(),
        ]);
    }
}
