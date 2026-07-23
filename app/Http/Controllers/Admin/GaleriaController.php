<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGaleriaRequest;
use App\Http\Requests\UpdateGaleriaRequest;
use App\Models\Galeria;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GaleriaController extends Controller
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    public function index(Request $request): Response
    {
        $items = Galeria::query()
            ->orderBy('orden')
            ->orderBy('id')
            ->paginate((int) $request->get('per_page', 15))
            ->withQueryString();

        return Inertia::render('Admin/Galeria/Index', [
            'items' => $items,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Galeria/Form', [
            'item' => null,
        ]);
    }

    public function store(StoreGaleriaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->imageService->storeImage($request->file('imagen'), 'galeria');
        }

        Galeria::create($data);

        return redirect()
            ->route('admin.galeria.index')
            ->with('success', 'Imagen de galería guardada correctamente.');
    }

    public function edit(Galeria $galeria): Response
    {
        return Inertia::render('Admin/Galeria/Form', [
            'item' => $galeria,
        ]);
    }

    public function update(UpdateGaleriaRequest $request, Galeria $galeria): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $this->imageService->deleteImage($galeria->imagen);
            $data['imagen'] = $this->imageService->storeImage($request->file('imagen'), 'galeria');
        }

        $galeria->update($data);

        return redirect()
            ->route('admin.galeria.index')
            ->with('success', 'Imagen de galería actualizada correctamente.');
    }

    public function destroy(Galeria $galeria): RedirectResponse
    {
        $this->imageService->deleteImage($galeria->imagen);
        $galeria->delete();

        return redirect()
            ->route('admin.galeria.index')
            ->with('success', 'Imagen de galería eliminada correctamente.');
    }
}
