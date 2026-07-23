<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactoRequest;
use App\Http\Requests\UpdateContactoRequest;
use App\Models\Contacto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactoController extends Controller
{
    public function index(Request $request): Response
    {
        $contactos = Contacto::query()
            ->orderBy('orden')
            ->orderBy('id')
            ->paginate((int) $request->get('per_page', 15))
            ->withQueryString();

        return Inertia::render('Admin/Contactos/Index', [
            'contactos' => $contactos,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Contactos/Form', [
            'contacto' => null,
        ]);
    }

    public function store(StoreContactoRequest $request): RedirectResponse
    {
        Contacto::create($request->validated());

        return redirect()
            ->route('admin.contactos.index')
            ->with('success', 'Contacto guardado correctamente.');
    }

    public function edit(Contacto $contacto): Response
    {
        return Inertia::render('Admin/Contactos/Form', [
            'contacto' => $contacto,
        ]);
    }

    public function update(UpdateContactoRequest $request, Contacto $contacto): RedirectResponse
    {
        $contacto->update($request->validated());

        return redirect()
            ->route('admin.contactos.index')
            ->with('success', 'Contacto actualizado correctamente.');
    }

    public function destroy(Contacto $contacto): RedirectResponse
    {
        $contacto->delete();

        return redirect()
            ->route('admin.contactos.index')
            ->with('success', 'Contacto eliminado correctamente.');
    }
}
