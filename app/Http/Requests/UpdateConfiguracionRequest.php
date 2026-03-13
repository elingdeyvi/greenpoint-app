<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfiguracionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Comentario: Permisos finos se controlan por middleware.
        return true;
    }

    public function rules(): array
    {
        // Comentario: Se espera un arreglo de pares clave-valor para actualizar varias claves a la vez.
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.clave' => ['required', 'string', 'max:255'],
            'items.*.valor' => ['nullable', 'string'],
        ];
    }
}

