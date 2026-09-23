<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:255'],
            'subtitulo' => ['nullable', 'string', 'max:255'],
            'titulo_seccion' => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'imagen_secundaria' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'plantilla' => ['nullable', 'string', Rule::in(['detalle', 'about', 'split'])],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ];
    }
}
