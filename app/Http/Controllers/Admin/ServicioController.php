<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServicioRequest;
use App\Http\Requests\UpdateServicioRequest;
use App\Models\Servicio;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServicioController extends Controller
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    public function index(Request $request): Response
    {
        $servicios = Servicio::query()
            ->orderBy('orden')
            ->orderBy('id')
            ->paginate((int) $request->get('per_page', 15))
            ->withQueryString();

        return Inertia::render('Admin/Servicios/Index', [
            'servicios' => $servicios,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Servicios/Form', [
            'servicio' => null,
        ]);
    }

    public function store(StoreServicioRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->imageService->storeImage($request->file('imagen'), 'servicios');
        }

        Servicio::create($data);

        return redirect()
            ->route('admin.servicios.index')
            ->with('success', 'Servicio guardado correctamente.');
    }

    public function edit(Servicio $servicio): Response
    {
        return Inertia::render('Admin/Servicios/Form', [
            'servicio' => $servicio,
        ]);
    }

    public function update(UpdateServicioRequest $request, Servicio $servicio): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $this->imageService->deleteImage($servicio->imagen);
            $data['imagen'] = $this->imageService->storeImage($request->file('imagen'), 'servicios');
        }

        $servicio->update($data);

        return redirect()
            ->route('admin.servicios.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        $this->imageService->deleteImage($servicio->imagen);
        $servicio->delete();

        return redirect()
            ->route('admin.servicios.index')
            ->with('success', 'Servicio eliminado correctamente.');
    }
}
