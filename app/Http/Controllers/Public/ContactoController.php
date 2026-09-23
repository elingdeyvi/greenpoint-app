<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ContactFormRequest;
use App\Models\Contacto;
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

    /**
     * Ficha de oficina (cgi-bin tabasco.html / veracruz.html / carmen.html).
     */
    public function show(Contacto $contacto): Response
    {
        return Inertia::render('Public/ContactoOficina', [
            'contacto' => $contacto,
        ]);
    }

    public function store(ContactFormRequest $request): RedirectResponse
    {
        $this->publicSiteService->enviarContacto($request->validated());

        return back()->with('success', 'Mensaje enviado correctamente.');
    }
}
