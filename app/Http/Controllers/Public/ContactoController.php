<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ContactFormRequest;
use App\Services\PublicSiteService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContactoController extends Controller
{
    public function __construct(private readonly PublicSiteService $publicSiteService)
    {
    }

    public function index(): Response
    {
        return Inertia::render('Public/Contacto', [
            'contactos' => $this->publicSiteService->contactos(),
            'redesSociales' => $this->publicSiteService->redesSociales(),
        ]);
    }

    public function store(ContactFormRequest $request): RedirectResponse
    {
        $this->publicSiteService->enviarContacto($request->validated());

        return back()->with('success', 'Mensaje enviado correctamente.');
    }
}
