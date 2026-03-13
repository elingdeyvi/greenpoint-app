<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use App\Models\Banner;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function __construct(private readonly ImageService $imageService)
    {
        // Comentario: Middleware de permisos se puede aplicar aquí si es necesario.
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->get('per_page', 15));

        $query = Banner::query()->orderBy('orden')->orderBy('id');

        return response()->json(
            $query->paginate($perPage),
            JsonResponse::HTTP_OK
        );
    }

    public function store(StoreBannerRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->imageService->storeImage($request->file('imagen'), 'banners');
        }

        $banner = Banner::create($data);

        return response()->json($banner, JsonResponse::HTTP_CREATED);
    }

    public function show(Banner $banner): JsonResponse
    {
        return response()->json($banner, JsonResponse::HTTP_OK);
    }

    public function update(UpdateBannerRequest $request, Banner $banner): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $this->imageService->deleteImage($banner->imagen);
            $data['imagen'] = $this->imageService->storeImage($request->file('imagen'), 'banners');
        }

        $banner->update($data);

        return response()->json($banner->refresh(), JsonResponse::HTTP_OK);
    }

    public function destroy(Banner $banner): JsonResponse
    {
        $this->imageService->deleteImage($banner->imagen);
        $banner->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}

