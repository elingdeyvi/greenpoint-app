<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactoRequest;
use App\Http\Requests\UpdateContactoRequest;
use App\Models\Contacto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) ($request->get('per_page', 15));

        $query = Contacto::query()->orderBy('orden')->orderBy('id');

        return response()->json(
            $query->paginate($perPage),
            JsonResponse::HTTP_OK
        );
    }

    public function store(StoreContactoRequest $request): JsonResponse
    {
        $contacto = Contacto::create($request->validated());

        return response()->json($contacto, JsonResponse::HTTP_CREATED);
    }

    public function show(Contacto $contacto): JsonResponse
    {
        return response()->json($contacto, JsonResponse::HTTP_OK);
    }

    public function update(UpdateContactoRequest $request, Contacto $contacto): JsonResponse
    {
        $contacto->update($request->validated());

        return response()->json($contacto->refresh(), JsonResponse::HTTP_OK);
    }

    public function destroy(Contacto $contacto): JsonResponse
    {
        $contacto->delete();

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}

