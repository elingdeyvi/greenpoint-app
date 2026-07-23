<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRedSocialRequest;
use App\Http\Requests\UpdateRedSocialRequest;
use App\Models\RedSocial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RedSocialController extends Controller
{
    public function index(Request $request): Response
    {
        $redes = RedSocial::query()
            ->orderBy('orden')
            ->orderBy('id')
            ->paginate((int) $request->get('per_page', 15))
            ->withQueryString();

        return Inertia::render('Admin/RedesSociales/Index', [
            'redes' => $redes,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/RedesSociales/Form', [
            'redSocial' => null,
        ]);
    }

    public function store(StoreRedSocialRequest $request): RedirectResponse
    {
        RedSocial::create($request->validated());

        return redirect()
            ->route('admin.redes-sociales.index')
            ->with('success', 'Red social guardada correctamente.');
    }

    public function edit(RedSocial $redSocial): Response
    {
        return Inertia::render('Admin/RedesSociales/Form', [
            'redSocial' => $redSocial,
        ]);
    }

    public function update(UpdateRedSocialRequest $request, RedSocial $redSocial): RedirectResponse
    {
        $redSocial->update($request->validated());

        return redirect()
            ->route('admin.redes-sociales.index')
            ->with('success', 'Red social actualizada correctamente.');
    }

    public function destroy(RedSocial $redSocial): RedirectResponse
    {
        $redSocial->delete();

        return redirect()
            ->route('admin.redes-sociales.index')
            ->with('success', 'Red social eliminada correctamente.');
    }
}
