<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormularioContacto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FormularioContactoController extends Controller
{
    public function index(Request $request): Response
    {
        $formularios = FormularioContacto::query()
            ->orderBy('leido')
            ->orderByDesc('created_at')
            ->paginate((int) $request->get('per_page', 15))
            ->withQueryString();

        return Inertia::render('Admin/FormulariosContacto/Index', [
            'formularios' => $formularios,
        ]);
    }

    public function show(FormularioContacto $formularioContacto): Response
    {
        return Inertia::render('Admin/FormulariosContacto/Show', [
            'formulario' => $formularioContacto,
        ]);
    }

    public function update(Request $request, FormularioContacto $formularioContacto): RedirectResponse
    {
        $request->validate([
            'leido' => ['required', 'boolean'],
        ]);

        $formularioContacto->update([
            'leido' => $request->boolean('leido'),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Formulario actualizado correctamente.');
    }
}
