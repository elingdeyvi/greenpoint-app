<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRedSocialRequest;
use App\Http\Requests\UpdateRedSocialRequest;
use App\Models\RedSocial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RedSocialController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->get('per_page', 15));

        $query = RedSocial::query()->orderBy('orden')->orderBy('id');

        return response()->json(
            $query->paginate($perPage),
            JsonResponse::HTTP_OK
        );
    }

    public function store(StoreRedSocialRequest $request): JsonResponse
    {
        $redSocial = RedSocial::create($request->validated());

        return response()->json($redSocial, JsonResponse::HTTP_CREATED);
    }

    public function show(RedSocial $redSocial): JsonResponse
    {
        return response()->json($redSocial, JsonResponse::HTTP_OK);
    }

    public function update(UpdateRedSocialRequest $request, RedSocial $redSocial): JsonResponse
    {
        $redSocial->update($request->validated());

        return response()->json($redSocial->refresh(), JsonResponse::HTTP_OK);
    }

    public function destroy(RedSocial $redSocial): JsonResponse
    {
        $redSocial->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}

