<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClienteController extends Controller
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    public function index(Request $request): Response
    {
        $clientes = Cliente::query()
            ->orderBy('orden')
            ->orderBy('id')
            ->paginate((int) $request->get('per_page', 15))
            ->withQueryString();

        return Inertia::render('Admin/Clientes/Index', [
            'clientes' => $clientes,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Clientes/Form', [
            'cliente' => null,
        ]);
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->imageService->storeImage($request->file('logo'), 'clientes');
        }

        Cliente::create($data);

        return redirect()
            ->route('admin.clientes.index')
            ->with('success', 'Cliente guardado correctamente.');
    }

    public function edit(Cliente $cliente): Response
    {
        return Inertia::render('Admin/Clientes/Form', [
            'cliente' => $cliente,
        ]);
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $this->imageService->deleteImage($cliente->logo);
            $data['logo'] = $this->imageService->storeImage($request->file('logo'), 'clientes');
        }

        $cliente->update($data);

        return redirect()
            ->route('admin.clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $this->imageService->deleteImage($cliente->logo);
        $cliente->delete();

        return redirect()
            ->route('admin.clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
