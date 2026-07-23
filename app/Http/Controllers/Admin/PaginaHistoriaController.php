<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePaginaHistoriaRequest;
use App\Models\PaginaHistoria;
use App\Services\PaginaHistoriaService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PaginaHistoriaController extends Controller
{
    public function __construct(private readonly PaginaHistoriaService $service)
    {
    }

    public function edit(): Response
    {
        $pagina = PaginaHistoria::query()
            ->with([
                'eventos' => fn ($q) => $q->orderBy('orden'),
                'imagenes' => fn ($q) => $q->orderBy('orden'),
            ])
            ->first();

        if (!$pagina) {
            $pagina = PaginaHistoria::create([
                'titulo' => 'Historia',
                'estado' => true,
            ])->load(['eventos', 'imagenes']);
        }

        return Inertia::render('Admin/Paginas/Historia', [
            'pagina' => $pagina,
        ]);
    }

    public function update(UpdatePaginaHistoriaRequest $request): RedirectResponse
    {
        $pagina = PaginaHistoria::query()->first();

        if (!$pagina) {
            $pagina = PaginaHistoria::create([
                'titulo' => $request->input('titulo', 'Historia'),
                'estado' => $request->boolean('estado', true),
            ]);
        }

        $this->service->updateFromRequest($pagina, $request);

        return redirect()
            ->back()
            ->with('success', 'Página Historia actualizada correctamente.');
    }
}
