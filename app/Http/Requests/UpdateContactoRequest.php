<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Comentario: Permisos finos se controlan por middleware.
        return true;
    }

    public function rules(): array
    {
        return [
            'ubicacion' => ['sometimes', 'required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string'],
            'telefono' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'mapa_url' => ['nullable', 'url', 'max:255'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

