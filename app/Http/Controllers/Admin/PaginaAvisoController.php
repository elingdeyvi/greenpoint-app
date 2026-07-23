<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePaginaAvisoRequest;
use App\Models\PaginaAviso;
use App\Services\PaginaAvisoService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PaginaAvisoController extends Controller
{
    public function __construct(private readonly PaginaAvisoService $service)
    {
    }

    public function edit(): Response
    {
        $pagina = PaginaAviso::query()
            ->with([
                'secciones.listas' => fn ($q) => $q->orderBy('orden'),
            ])
            ->first();

        if (!$pagina) {
            $pagina = PaginaAviso::create([
                'titulo' => 'Aviso de privacidad',
                'estado' => true,
            ])->load(['secciones.listas']);
        }

        return Inertia::render('Admin/Paginas/Aviso', [
            'pagina' => $pagina,
        ]);
    }

    public function update(UpdatePaginaAvisoRequest $request): RedirectResponse
    {
        $pagina = PaginaAviso::query()->first();

        if (!$pagina) {
            $pagina = PaginaAviso::create([
                'titulo' => $request->input('titulo', 'Aviso de privacidad'),
                'estado' => $request->boolean('estado', true),
            ]);
        }

        $this->service->updateFromRequest($pagina, $request);

        return redirect()
            ->back()
            ->with('success', 'Página Aviso actualizada correctamente.');
    }
}
