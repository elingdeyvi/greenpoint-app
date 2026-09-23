<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Comentario: Permisos finos se controlan por middleware.
        return true;
    }

    public function rules(): array
    {
        return [
            'ubicacion' => ['required', 'string', 'max:255'],
            'subtitulo' => ['nullable', 'string', 'max:255'],
            'direccion' => ['nullable', 'string'],
            'telefono' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'mapa_url' => ['nullable', 'string', 'max:2048'],
            'orden' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

