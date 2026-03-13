<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{
    public function index(): JsonResponse
    {
        $marcas = Marca::activos()->get();
        return response()->json(['data' => $marcas], JsonResponse::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo' => ['required', 'string', 'max:50', Rule::unique('marcas', 'codigo')],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'activo' => ['boolean'],
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ya existe una marca con este código.',
            'codigo.max' => 'El código no debe exceder 50 caracteres.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder 255 caracteres.',
            'logo.image' => 'El archivo debe ser una imagen.',
            'logo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'logo.max' => 'La imagen no debe exceder 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $request->except('logo');

        // Subir logo si existe
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('marcas', 'public');
            $data['logo'] = $logoPath;
        }

        $marca = Marca::create($data);
        return response()->json(['data' => $marca], JsonResponse::HTTP_CREATED);
    }

    public function show(Marca $marca): JsonResponse
    {
        return response()->json(['data' => $marca], JsonResponse::HTTP_OK);
    }

    public function update(Request $request, Marca $marca): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($marca) {
                    $exists = Marca::where('codigo', $value)
                        ->where('id', '!=', $marca->id)
                        ->exists();
                    if ($exists) {
                        $fail('Ya existe una marca con este código.');
                    }
                }
            ],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'activo' => ['boolean'],
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.max' => 'El código no debe exceder 50 caracteres.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder 255 caracteres.',
            'logo.image' => 'El archivo debe ser una imagen.',
            'logo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'logo.max' => 'La imagen no debe exceder 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $request->except('logo');

        // Actualizar logo si existe
        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe
            if ($marca->logo) {
                Storage::disk('public')->delete($marca->logo);
            }
            
            $logo = $request->file('logo');
            $logoPath = $logo->store('marcas', 'public');
            $data['logo'] = $logoPath;
        }

        $marca->update($data);
        return response()->json(['data' => $marca], JsonResponse::HTTP_OK);
    }

    public function destroy(Marca $marca): JsonResponse
    {
        $marca->update(['activo' => false]);
        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    public function uploadLogo(Request $request, Marca $marca): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            'logo.required' => 'Debe seleccionar una imagen.',
            'logo.image' => 'El archivo debe ser una imagen.',
            'logo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'logo.max' => 'La imagen no debe exceder 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Eliminar logo anterior si existe
        if ($marca->logo) {
            Storage::disk('public')->delete($marca->logo);
        }

        // Subir nuevo logo
        $logo = $request->file('logo');
        $logoPath = $logo->store('marcas', 'public');
        
        $marca->update(['logo' => $logoPath]);

        return response()->json(['data' => $marca], JsonResponse::HTTP_OK);
    }

    public function deleteLogo(Marca $marca): JsonResponse
    {
        if ($marca->logo) {
            Storage::disk('public')->delete($marca->logo);
            $marca->update(['logo' => null]);
        }

        return response()->json(['message' => 'Logo eliminado correctamente'], JsonResponse::HTTP_OK);
    }
}

