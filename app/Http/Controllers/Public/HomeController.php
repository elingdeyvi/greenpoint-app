<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicSiteService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(private readonly PublicSiteService $publicSiteService)
    {
    }

    public function index(): Response
    {
        $data = $this->publicSiteService->home();

        return Inertia::render('Public/Home', [
            'banners' => $data['banners'],
            'servicios' => $data['servicios'],
            'nosotros' => $data['nosotros'],
            'config' => $data['config'],
        ]);
    }
}
