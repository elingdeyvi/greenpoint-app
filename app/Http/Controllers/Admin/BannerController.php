<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Models\Banner;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BannerController extends Controller
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    public function index(Request $request): Response
    {
        $banners = Banner::query()
            ->orderBy('orden')
            ->orderBy('id')
            ->paginate((int) $request->get('per_page', 15))
            ->withQueryString();

        return Inertia::render('Admin/Banners/Index', [
            'banners' => $banners,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Banners/Form', [
            'banner' => null,
        ]);
    }

    public function store(StoreBannerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->imageService->storeImage($request->file('imagen'), 'banners');
        }

        Banner::create($data);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner guardado correctamente.');
    }

    public function edit(Banner $banner): Response
    {
        return Inertia::render('Admin/Banners/Form', [
            'banner' => $banner,
        ]);
    }

    public function update(UpdateBannerRequest $request, Banner $banner): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $this->imageService->deleteImage($banner->imagen);
            $data['imagen'] = $this->imageService->storeImage($request->file('imagen'), 'banners');
        }

        $banner->update($data);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner actualizado correctamente.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $this->imageService->deleteImage($banner->imagen);
        $banner->delete();

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner eliminado correctamente.');
    }
}
