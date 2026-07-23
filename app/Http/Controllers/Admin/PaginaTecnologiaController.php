<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePaginaTecnologiaRequest;
use App\Models\PaginaTecnologia;
use App\Services\PaginaTecnologiaService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PaginaTecnologiaController extends Controller
{
    public function __construct(private readonly PaginaTecnologiaService $service)
    {
    }

    public function edit(): Response
    {
        $pagina = PaginaTecnologia::query()
            ->with([
                'secciones' => fn ($q) => $q->orderBy('orden'),
            ])
            ->first();

        if (!$pagina) {
            $pagina = PaginaTecnologia::create([
                'titulo' => 'Tecnología',
                'estado' => true,
            ])->load(['secciones']);
        }

        return Inertia::render('Admin/Paginas/Tecnologia', [
            'pagina' => $pagina,
        ]);
    }

    public function update(UpdatePaginaTecnologiaRequest $request): RedirectResponse
    {
        $pagina = PaginaTecnologia::query()->first();

        if (!$pagina) {
            $pagina = PaginaTecnologia::create([
                'titulo' => $request->input('titulo', 'Tecnología'),
                'estado' => $request->boolean('estado', true),
            ]);
        }

        $this->service->updateFromRequest($pagina, $request);

        return redirect()
            ->back()
            ->with('success', 'Página Tecnología actualizada correctamente.');
    }
}
