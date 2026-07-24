<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateConfiguracionRequest;
use App\Models\Configuracion;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ConfiguracionController extends Controller
{
    public function index(): Response
    {
        $items = Configuracion::query()
            ->orderBy('clave')
            ->get(['id', 'clave', 'valor']);

        return Inertia::render('Admin/Configuracion/Index', [
            'items' => $items,
        ]);
    }

    public function update(UpdateConfiguracionRequest $request): RedirectResponse
    {
        $items = $request->validated('items');

        foreach ($items as $item) {
            Configuracion::updateOrCreate(
                ['clave' => $item['clave']],
                ['valor' => $item['valor'] ?? null]
            );
        }

        app(\App\Services\PublicSiteService::class)->forgetCache();

        return redirect()
            ->back()
            ->with('success', 'Configuración actualizada correctamente.');
    }
}
