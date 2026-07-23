<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePaginaNosotrosRequest;
use App\Models\PaginaNosotros;
use App\Services\PaginaNosotrosService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PaginaNosotrosController extends Controller
{
    public function __construct(private readonly PaginaNosotrosService $service)
    {
    }

    public function edit(): Response
    {
        $pagina = PaginaNosotros::query()
            ->with([
                'imagenes' => fn ($q) => $q->orderBy('orden'),
                'progreso' => fn ($q) => $q->orderBy('orden'),
            ])
            ->first();

        if (!$pagina) {
            $pagina = PaginaNosotros::create([
                'titulo' => 'Nosotros',
                'estado' => true,
            ])->load(['imagenes', 'progreso']);
        }

        return Inertia::render('Admin/Paginas/Nosotros', [
            'pagina' => $pagina,
        ]);
    }

    public function update(UpdatePaginaNosotrosRequest $request): RedirectResponse
    {
        $pagina = PaginaNosotros::query()->first();

        if (!$pagina) {
            $pagina = PaginaNosotros::create([
                'titulo' => $request->input('titulo', 'Nosotros'),
                'estado' => $request->boolean('estado', true),
            ]);
        }

        $this->service->updateFromRequest($pagina, $request);

        return redirect()
            ->back()
            ->with('success', 'Página Nosotros actualizada correctamente.');
    }
}
